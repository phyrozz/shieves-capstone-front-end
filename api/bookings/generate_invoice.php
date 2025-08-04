<?php
require '../../vendor/autoload.php';
require '../../conn.php';

use Ramsey\Uuid\Uuid;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $phone_number = filter_input(INPUT_POST, "phonenumber", FILTER_SANITIZE_STRING);
    $dateRange = filter_input(INPUT_POST, "checkinout", FILTER_SANITIZE_STRING);
    $package = filter_input(INPUT_POST, "package", FILTER_SANITIZE_STRING);
    // $amount = filter_input(INPUT_POST, "amount", FILTER_VALIDATE_FLOAT);
    $packagename = filter_input(INPUT_POST, "package_name", FILTER_SANITIZE_STRING);
    $package_price = filter_input(INPUT_POST, "package_price", FILTER_VALIDATE_FLOAT);
    
    // Validate required fields
    if (!$name || !$email || !$phone_number || !$dateRange || !$package || !$package_price) {
        throw new Exception("Invalid input");
    }

    // Split the date range into check-in and check-out dates
    $dates = explode(" to ", $dateRange);

    // Handle cases where only one date is provided
    if (count($dates) == 1) {
        $checkin = $dates[0];
        $checkout = $dates[0]; // Same day for check-in and check-out
    } else {
        $checkin = $dates[0];
        $checkout = $dates[1];
    }

    // Calculate the invoice duration in seconds
    $checkinTimestamp = strtotime($checkin);
    $currentTimestamp = time();
    $invoiceDuration = $checkinTimestamp - $currentTimestamp;

    // Ensure the invoice duration is a positive number
    if ($invoiceDuration < 0) {
        throw new Exception("Check-in date must be in the future.");
    }

    // Ensure the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address");
    }

    // Generate a UUID for the external_id and booking_id
    $externalId = Uuid::uuid4()->toString();
    $bookingId = Uuid::uuid4()->toString();

    try {
        // Check for duplicate booking
        $checkDuplicateStmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM bookings 
        WHERE name = ? AND email = ? AND phone_number = ? AND package_id = ? AND time_in = ? AND time_out = ?
        ");
        $checkDuplicateStmt->bind_param("ssssss", $name, $email, $phone_number, $package, $checkin, $checkout);
        $checkDuplicateStmt->execute();
        $result = $checkDuplicateStmt->get_result();
        $row = $result->fetch_assoc();
        $checkDuplicateStmt->close();

        if ($row['count'] > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Duplicate entry found for invoice"]);
            exit;
            // echo json_encode([
            //     "status" => "error",
            //     "message" => "A booking with the same details already exists."
            // ]);
            // exit;
        }

        // Insert booking into the database
        $stmt = $conn->prepare("
            INSERT INTO bookings (id, email, name, phone_number, time_in, time_out, package_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssi", $bookingId, $email, $name, $phone_number, $checkin, $checkout, $package);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            throw new Exception("Failed to save booking record.");
        }

        $stmt->close();

        // Generate the PDF invoice
        $pdf = new \FPDF();
        $pdf->AddPage();

        // Add a custom font (optional, make sure the font file is available)
        // $pdf->AddFont('CustomFont', '', 'customfont.ttf', true);

        // Set document title
        $pdf->SetTitle('Booking Invoice');

        // Add a title with styling
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(33, 37, 41); // Dark gray
        $pdf->Cell(0, 10, 'Booking Invoice', 0, 1, 'C');
        $pdf->Ln(10);

        // Add a header line
        $pdf->SetDrawColor(50, 50, 50);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, 30, 200, 30);
        $pdf->Ln(10);

        // Add user details with improved spacing and fonts
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(66, 66, 66); // Gray

        // Create a labeled section with better layout
        $pdf->Cell(50, 10, 'Name:', 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, $name, 0, 1);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Email:', 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, $email, 0, 1);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Phone Number:', 0, 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, $phone_number, 0, 1);

        $pdf->Ln(10); // Add some space

        // Add booking details in a bordered section
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 250); // Light lavender
        $pdf->SetDrawColor(50, 50, 50);
        $pdf->Cell(190, 10, 'Booking Details', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Package:', 1, 0);
        $pdf->Cell(140, 10, $packagename, 1, 1);

        $pdf->Cell(50, 10, 'Price (PHP):', 1, 0);
        $pdf->Cell(140, 10, $package_price, 1, 1);

        $pdf->Cell(50, 10, 'Check-in Date:', 1, 0);
        $pdf->Cell(140, 10, $checkin, 1, 1);

        $pdf->Cell(50, 10, 'Check-out Date:', 1, 0);
        $pdf->Cell(140, 10, $checkout, 1, 1);

        $pdf->Ln(20); // Add space before footer

        // Footer section
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(128, 128, 128);
        $pdf->Cell(0, 10, 'Thank you for booking with us!', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Please present this invoice to our receptionist to confirm your booking. Thank you!', 0, 1, 'C');
        $pdf->Cell(0, 10, 'If you have any questions, please contact us.', 0, 1, 'C');

        // Output the PDF
        $pdf->Output('I', 'Invoice.pdf');
    } catch (Exception $e) {
        // Handle errors
        http_response_code(500);
        echo "Error: " . $e->getMessage();
    } finally {
        $conn->close();
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
