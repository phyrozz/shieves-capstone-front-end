<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

include "../conn.php";

function adminBookingStatusTone(string $name, string $kind): string {
    $normalized = strtolower(trim($name));
    if ($normalized === 'pending') return 'tone-pending';
    if (in_array($normalized, ['cancelled', 'canceled'], true)) return 'tone-cancelled';
    if (($kind === 'booking' && $normalized === 'booked') || ($kind === 'payment' && $normalized === 'paid')) {
        return 'tone-success';
    }
    return 'tone-neutral';
}

function adminBookingDateLabel(?string $value): string {
    if (!$value || strpos($value, '0000-00-00') === 0) return 'Unavailable';
    $timestamp = strtotime($value);
    return $timestamp === false ? 'Unavailable' : date('M j, Y', $timestamp);
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

$paymentStatusStmt = $conn->prepare("SELECT id, name FROM payment_statuses ORDER BY id");
$paymentStatusStmt->execute();
$paymentStatusesResult = $paymentStatusStmt->get_result();
$paymentStatuses = [];
while ($paymentStatusRow = $paymentStatusesResult->fetch_assoc()) {
    $paymentStatuses[] = $paymentStatusRow;
}
$paymentStatusStmt->close();

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
$query = "SELECT bookings.id as booking_id, bookings.name AS full_name, bookings.email, bookings.phone_number, bookings.time_in, bookings.time_out, bookings.status_id, statuses.name AS status_name, bookings.payment_status_id, packages.name AS package_name, packages.price as package_price, payment_statuses.name as payment_status_name
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
    <link rel="stylesheet" href="css/reports.css">
    <style>
        .status-edit-toolbar {
            display:flex;
            align-items:center;
            flex-wrap:wrap;
            gap:12px;
            margin:0 0 16px;
        }
        .status-edit-button {
            border:0;
            border-radius:8px;
            padding:10px 15px;
            background:#0f766e;
            color:#fff;
            font:600 14px/1.2 Inter,"Segoe UI",sans-serif;
            cursor:pointer;
        }
        .status-edit-button:hover { background:#115e59; }
        .status-edit-button:focus-visible { outline:3px solid #5eead4; outline-offset:2px; }
        .status-edit-message { color:#475569; font-size:13px; }
        .status-edit-message.is-error { color:#b91c1c; }
        .status-pill {
            appearance:none;
            min-width:112px;
            border:1px solid transparent;
            border-radius:999px;
            padding:7px 12px;
            font:600 13px/1.2 Inter,"Segoe UI",sans-serif;
            opacity:1;
        }
        .status-pill:disabled { opacity:1; cursor:default; }
        .status-pill:not(:disabled) {
            padding-right:27px;
            cursor:pointer;
            background-image:linear-gradient(45deg,transparent 50%,currentColor 50%),linear-gradient(135deg,currentColor 50%,transparent 50%);
            background-position:calc(100% - 13px) 50%,calc(100% - 8px) 50%;
            background-size:5px 5px,5px 5px;
            background-repeat:no-repeat;
        }
        .tone-pending { background-color:#fef3c7; color:#92400e; border-color:#fde68a; }
        .tone-cancelled { background-color:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .tone-success { background-color:#dcfce7; color:#166534; border-color:#bbf7d0; }
        .tone-neutral { background-color:#f1f5f9; color:#475569; border-color:#cbd5e1; }
        .booking-date-range { display:grid; gap:3px; min-width:125px; font-size:12px; line-height:1.3; }
        .booking-date-range strong { color:#64748b; font-size:10px; letter-spacing:.04em; text-transform:uppercase; }
    </style>
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="../node_modules/axios/dist/axios.min.js"></script>
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
</head>
<body class="sales-report">
<div class="report-sidebar"><?php include "../components/admin_navbar.php"; ?></div>
<main class="report-main">
    <header class="report-header">
        <div>
            <h1>Client Booking</h1>
            <p class="muted">Review guest details and manage booking statuses.</p>
        </div>
    </header>
        <div class="p-5">
            <div class="status-edit-toolbar">
                <button class="status-edit-button" type="button" id="status-edit-toggle" aria-pressed="false">Enable status editing</button>
                <span class="status-edit-message" id="status-edit-message" role="status" aria-live="polite">Status editing is off. Enable editing to change booking or payment status.</span>
            </div>
            <table id="bookings-table" class="stripe">
                <thead>
                    <tr>
                        <th scope="col">NAME</th>
                        <th scope="col">EMAIL</th>
                        <th scope="col">CONTACT NUMBER</th>
                        <th scope="col">PACKAGE</th>
                        <th scope="col">BOOKING DATES</th>
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
                        <td data-order="<?= htmlspecialchars((string)$row["time_in"], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="booking-date-range">
                                <span><strong>In</strong> <?= htmlspecialchars(adminBookingDateLabel($row["time_in"]), ENT_QUOTES, 'UTF-8') ?></span>
                                <span><strong>Out</strong> <?= htmlspecialchars(adminBookingDateLabel($row["time_out"]), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </td>
                        <td data-order="<?= htmlspecialchars($row["status_name"], ENT_QUOTES, 'UTF-8') ?>">
                            <select class="status-pill <?= adminBookingStatusTone($row["status_name"], 'booking') ?>" data-booking-id="<?= htmlspecialchars((string)$row["booking_id"], ENT_QUOTES, 'UTF-8') ?>" data-status-kind="booking" data-saved-value="<?= (int)$row["status_id"] ?>" aria-label="Booking status for <?= htmlspecialchars($row["full_name"], ENT_QUOTES, 'UTF-8') ?>" disabled>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= (int)$status["id"] ?>" data-tone="<?= adminBookingStatusTone($status["name"], 'booking') ?>" <?= (int)$row["status_id"] === (int)$status["id"] ? "selected" : "" ?>><?= htmlspecialchars($status["name"], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td data-order="<?= htmlspecialchars($row["payment_status_name"], ENT_QUOTES, 'UTF-8') ?>">
                            <select class="status-pill <?= adminBookingStatusTone($row["payment_status_name"], 'payment') ?>" data-booking-id="<?= htmlspecialchars((string)$row["booking_id"], ENT_QUOTES, 'UTF-8') ?>" data-status-kind="payment" data-saved-value="<?= (int)$row["payment_status_id"] ?>" aria-label="Payment status for <?= htmlspecialchars($row["full_name"], ENT_QUOTES, 'UTF-8') ?>" disabled>
                                <?php foreach ($paymentStatuses as $status): ?>
                                    <option value="<?= (int)$status["id"] ?>" data-tone="<?= adminBookingStatusTone($status["name"], 'payment') ?>" <?= (int)$row["payment_status_id"] === (int)$status["id"] ? "selected" : "" ?>><?= htmlspecialchars($status["name"], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
</main>

<script>
    const statusEditToggle = document.getElementById('status-edit-toggle');
    const statusEditMessage = document.getElementById('status-edit-message');
    const statusSelects = Array.from(document.querySelectorAll('.status-pill'));
    const statusTones = ['tone-pending', 'tone-cancelled', 'tone-success', 'tone-neutral'];
    let statusEditingEnabled = false;

    function applyStatusTone(select) {
        select.classList.remove(...statusTones);
        select.classList.add(select.selectedOptions[0]?.dataset.tone || 'tone-neutral');
    }

    statusEditToggle.addEventListener('click', () => {
        statusEditingEnabled = !statusEditingEnabled;
        statusEditToggle.setAttribute('aria-pressed', String(statusEditingEnabled));
        statusEditToggle.textContent = statusEditingEnabled ? 'Disable status editing' : 'Enable status editing';
        statusSelects.forEach(select => {
            select.disabled = !statusEditingEnabled || select.dataset.saving === 'true';
        });
        statusEditMessage.classList.remove('is-error');
        statusEditMessage.textContent = statusEditingEnabled
            ? 'Status editing is on. Changes save automatically.'
            : 'Status editing is off.';
    });

    document.getElementById('bookings-table').addEventListener('change', async event => {
        const select = event.target.closest('.status-pill');
        if (!select || !statusEditingEnabled) return;

        const previousValue = select.dataset.savedValue;
        const selectedValue = select.value;
        select.dataset.saving = 'true';
        select.disabled = true;
        applyStatusTone(select);
        statusEditMessage.classList.remove('is-error');
        statusEditMessage.textContent = 'Saving status…';

        try {
            const response = await fetch('../api/admin/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({
                    booking_id: select.dataset.bookingId,
                    status_type: select.dataset.statusKind,
                    new_status_id: selectedValue
                })
            });
            const result = await response.json().catch(() => ({}));
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'The status could not be saved.');
            }
            select.dataset.savedValue = selectedValue;
            const statusCell = select.closest('td');
            if (statusCell) statusCell.dataset.order = select.selectedOptions[0].textContent.trim();
            if (select.dataset.statusKind === 'booking' && result.payment_status_id) {
                const paymentSelect = select.closest('tr')?.querySelector('select[data-status-kind="payment"]');
                const paymentValue = String(result.payment_status_id);
                const paymentOption = paymentSelect && Array.from(paymentSelect.options).find(option => option.value === paymentValue);
                if (paymentSelect && paymentOption) {
                    paymentSelect.value = paymentValue;
                    paymentSelect.dataset.savedValue = paymentValue;
                    applyStatusTone(paymentSelect);
                    const paymentCell = paymentSelect.closest('td');
                    if (paymentCell) paymentCell.dataset.order = paymentOption.textContent.trim();
                }
            }
            statusEditMessage.textContent = result.message || `${select.dataset.statusKind === 'payment' ? 'Payment' : 'Booking'} status saved.`;
        } catch (error) {
            select.value = previousValue;
            applyStatusTone(select);
            statusEditMessage.classList.add('is-error');
            statusEditMessage.textContent = error.message || 'The status could not be saved. Please try again.';
        } finally {
            delete select.dataset.saving;
            select.disabled = !statusEditingEnabled;
        }
    });

    new DataTable('#bookings-table', {});
</script>
</body>
</html>
