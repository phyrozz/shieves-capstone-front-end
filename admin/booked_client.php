<?php
include "../conn.php";

session_start();

if (!isset($_SESSION["username"])) {
    header("location: login.php");
}

// Retrieve all possible statuses
$statusStmt = $conn->prepare("SELECT id, name FROM statuses");
$statusStmt->execute();
$statusesResult = $statusStmt->get_result();
$statuses = [];
while ($statusRow = $statusesResult->fetch_assoc()) {
    $statuses[] = $statusRow;
}
$statusStmt->close();

$limit = 20; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Modify the query to include the search condition
$searchCondition = $search ? "WHERE bookings.name LIKE CONCAT('%', ?, '%') OR bookings.email LIKE CONCAT('%', ?, '%') OR bookings.phone_number LIKE CONCAT('%', ?, '%')" : '';

// Get total records for pagination
$totalStmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings $searchCondition");
if ($search) {
    $totalStmt->bind_param('sss', $search, $search, $search);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRecords = $totalResult->fetch_assoc()['count'];
$totalStmt->close();
$totalPages = ceil($totalRecords / $limit);

// Get records for the current page
$query = "SELECT bookings.id as booking_id, bookings.name AS full_name, bookings.email, bookings.phone_number, bookings.status_id, statuses.name AS status_name, packages.name AS package_name, packages.price as package_price, payment_statuses.name as payment_status_name
          FROM bookings 
          INNER JOIN statuses ON bookings.status_id = statuses.id
          INNER JOIN packages ON bookings.package_id = packages.id
          INNER JOIN payment_statuses ON bookings.payment_status_id = payment_statuses.id
          $searchCondition
          LIMIT ?, ?";
$stmt = $conn->prepare($query);

if ($search) {
    $stmt->bind_param('sssii', $search, $search, $search, $offset, $limit);
} else {
    $stmt->bind_param('ii', $offset, $limit);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J.M. Apilado Resort - Bookings</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tailwind.css">
    <link rel="stylesheet" href="../css/theme.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="../node_modules/axios/dist/axios.min.js"></script>
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
</head>
<body>
<div class="flex min-h-screen bg-secondary">
    <?php include "../components/admin_navbar.php"; ?>
    <div class="flex-1 p-8 bg-gradient-to-br bg-secondary h-screen" style="padding-left: 300px;">
        <div class="p-5">
            <table id="bookings-table" class="stripe">
                <thead>
                    <tr>
                        <th scope="col">NAME</th>
                        <th scope="col">EMAIL</th>
                        <th scope="col">CONTACT NUMBER</th>
                        <th scope="col">PACKAGE</th>
                        <th scope="col">STATUS</th>
                        <th scope="col">PAYMENT STATUS</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><p><?= htmlspecialchars($row["full_name"]) ?></p></td>
                        <td><p><?= htmlspecialchars($row["email"]) ?></p></td>
                        <td><p><?= htmlspecialchars($row["phone_number"]) ?></p></td>
                        <td><p><?= htmlspecialchars($row["package_name"]) ?></p></td>
                        <td>
                            <?php if ($row["payment_status_name"] == "Paid"): ?>
                            <p><?= htmlspecialchars($row["status_name"]) ?></p>
                            <?php else: ?>
                            <select onchange='updateStatus(this, "<?= $row["booking_id"] ?>")'>
                                <?php foreach ($statuses as $status): ?>
                                    <option value='<?= htmlspecialchars($status["id"]) ?>' <?= $row["status_id"] == $status["id"] ? "selected" : "" ?>><?= htmlspecialchars($status["name"]) ?></option>
                                <?php endforeach; ?>
                            </select>                                
                            <?php endif; ?>
                        </td>
                        <td><p><?= htmlspecialchars($row["payment_status_name"]) ?></p></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateStatus(select, bookingId) {
        const newStatusId = select.value;
        axios.post('../api/admin/update_status.php', { booking_id: bookingId, new_status_id: newStatusId })
            .then(response => {
                console.log(response.data);
            })
            .catch(error => {
                console.error('There was an error!', error);
            });
    }

    new DataTable('#bookings-table', {});
</script>
</body>
</html>