<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("location: login.php");
    exit;
}

include "../conn.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>J.M. Apilado Resort Admin Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="../tailwind.css">
<link rel="stylesheet" href="../css/theme.css">
<link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.lordicon.com/lordicon.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="../node_modules/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .dashboard-bookings-table { width:100%; min-width:760px; border-collapse:separate; border-spacing:0; }
    .dashboard-bookings-table th { padding:13px 16px; background:#f1f5f9; color:#475569; font-size:11px; font-weight:700; letter-spacing:.06em; text-align:left; white-space:nowrap; }
    .dashboard-bookings-table td { padding:14px 16px; border-top:1px solid #e8edf2; color:#334155; font-size:13px; vertical-align:middle; }
    .dashboard-bookings-table tbody tr:hover { background:#f8fafc; }
    .dashboard-status-pill,.dashboard-conflict-pill { display:inline-flex; align-items:center; border:1px solid transparent; border-radius:999px; padding:5px 10px; font-size:12px; font-weight:600; white-space:nowrap; }
    .dashboard-status-pending { background:#fef3c7; border-color:#fde68a; color:#92400e; }
    .dashboard-status-booked { background:#dcfce7; border-color:#bbf7d0; color:#166534; }
    .dashboard-status-cancelled { background:#fee2e2; border-color:#fecaca; color:#991b1b; }
    .dashboard-status-other { background:#f1f5f9; border-color:#cbd5e1; color:#475569; }
    .dashboard-conflict-yes { background:#fee2e2; border-color:#fecaca; color:#991b1b; }
    .dashboard-conflict-no { background:#f1f5f9; border-color:#cbd5e1; color:#475569; }
</style>
</head>
<body class="bg-secondary">
<div class="flex min-h-screen bg-secondary">
        <?php include "../components/admin_navbar.php"; ?>
        <main class="flex-1 min-w-0 p-8 bg-gradient-to-br bg-secondary pl-72">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Booking Calendar</h3>
            <div id="calendar" class="h-3/4"></div>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Bookings</h3>
        <div class="overflow-x-auto">
            <table class="dashboard-bookings-table bg-white shadow rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th scope="col">Booking ID</th>
                        <th scope="col">Guest Name</th>
                        <th scope="col">Check-in</th>
                        <th scope="col">Check-out</th>
                        <th scope="col">Status</th>
                        <th scope="col">Conflict</th>
                    </tr>
                </thead>
                <tbody id="bookings-table-body">
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-xs text-gray-500">Showing the 10 latest check-in dates.</p>
        </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dashboardData = <?php
        $stmt = $conn->prepare("SELECT bookings.id AS booking_id,
                                       bookings.name AS customer_name,
                                       COALESCE(packages.name, 'Unavailable package') AS package_name,
                                       bookings.time_in AS time_in,
                                       bookings.time_out AS time_out,
                                       bookings.status_id AS status_id,
                                       COALESCE(statuses.name, 'Unavailable') AS status,
                                       bookings.payment_status_id AS payment_status_id,
                                       COALESCE(payment_statuses.name, 'Unavailable') AS payment_status
                                FROM bookings
                                LEFT JOIN packages ON bookings.package_id = packages.id
                                LEFT JOIN statuses ON bookings.status_id = statuses.id
                                LEFT JOIN payment_statuses ON bookings.payment_status_id = payment_statuses.id
                                ORDER BY bookings.time_in DESC, bookings.id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $dashboardBookings = [];
        while ($row = $result->fetch_assoc()) {
            $dashboardBookings[] = [
                'id' => (string)$row['booking_id'],
                'customerName' => $row['customer_name'],
                'packageName' => $row['package_name'],
                'title' => $row['customer_name'] . ' - ' . $row['package_name'],
                'start' => $row['time_in'],
                'end' => $row['time_out'],
                'bookingStatusId' => (int)$row['status_id'],
                'status' => $row['status'],
                'paymentStatusId' => (int)$row['payment_status_id'],
                'paymentStatus' => $row['payment_status'],
                'conflict' => false,
            ];
        }
        $stmt->close();

        $bookingStatusStmt = $conn->prepare('SELECT id, name FROM statuses ORDER BY id');
        $bookingStatusStmt->execute();
        $bookingStatuses = [];
        $bookingStatusResult = $bookingStatusStmt->get_result();
        while ($status = $bookingStatusResult->fetch_assoc()) {
            $bookingStatuses[] = ['id' => (int)$status['id'], 'name' => $status['name']];
        }
        $bookingStatusStmt->close();

        $paymentStatusStmt = $conn->prepare('SELECT id, name FROM payment_statuses ORDER BY id');
        $paymentStatusStmt->execute();
        $paymentStatuses = [];
        $paymentStatusResult = $paymentStatusStmt->get_result();
        while ($status = $paymentStatusResult->fetch_assoc()) {
            $paymentStatuses[] = ['id' => (int)$status['id'], 'name' => $status['name']];
        }
        $paymentStatusStmt->close();
        $conn->close();
        echo json_encode([
            'events' => $dashboardBookings,
            'bookingStatuses' => $bookingStatuses,
            'paymentStatuses' => $paymentStatuses,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?: '{"events":[],"bookingStatuses":[],"paymentStatuses":[]}';
    ?>;
    const events = dashboardData.events;
    const bookingStatuses = dashboardData.bookingStatuses;
    const paymentStatuses = dashboardData.paymentStatuses;

    const normalizeStatus = value => String(value || '').trim().toLowerCase();
    const isBooked = value => ['booked', 'confirmed'].includes(normalizeStatus(value));
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[character]));
    function displayDate(value) {
        if (!value) return 'Unavailable';
        const date = new Date(`${String(value).slice(0, 10)}T00:00:00Z`);
        if (Number.isNaN(date.getTime())) return 'Unavailable';
        return new Intl.DateTimeFormat('en-PH', {
            day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC'
        }).format(date);
    }
    const statusTone = value => {
        const status = normalizeStatus(value);
        if (status === 'pending') return 'pending';
        if (['booked', 'confirmed'].includes(status)) return 'booked';
        if (['cancelled', 'canceled'].includes(status)) return 'cancelled';
        return 'other';
    };

    // ------------------ CONFLICT CHECKER ------------------
    function checkConflicts() {
        for (let i = 0; i < events.length; i++) {
            for (let j = i + 1; j < events.length; j++) {
                let a = events[i];
                let b = events[j];

                if (isBooked(a.status) && isBooked(b.status)) {
                    let startA = new Date(`${String(a.start).slice(0, 10)}T00:00:00Z`);
                    let endA   = new Date(`${String(a.end).slice(0, 10)}T00:00:00Z`);
                    let startB = new Date(`${String(b.start).slice(0, 10)}T00:00:00Z`);
                    let endB   = new Date(`${String(b.end).slice(0, 10)}T00:00:00Z`);

                    if (startA < endB && startB < endA) {
                        a.conflict = true;
                        b.conflict = true;
                    }
                }
            }
        }
    }
    checkConflicts();

    // ------------------ CALENDAR ------------------

    // The query sorts by check-in date because the bookings table has no created_at field.
    const latestBookingId = events.length ? events[0].id : null;

    function buildStatusOptions(options, selectedId) {
        return options.map(option => `<option value="${Number(option.id)}" ${Number(option.id) === Number(selectedId) ? 'selected' : ''}>${escapeHtml(option.name)}</option>`).join('');
    }

    function openStatusEditor(booking) {
        Swal.fire({
            title: 'Booking Details',
            html: `
                <div style="text-align:left;line-height:1.6">
                    <p><strong>Customer:</strong> ${escapeHtml(booking.customerName)}</p>
                    <p><strong>Package:</strong> ${escapeHtml(booking.packageName)}</p>
                    <p><strong>Check-in:</strong> ${escapeHtml(displayDate(booking.start))}</p>
                    <p><strong>Check-out:</strong> ${escapeHtml(displayDate(booking.end))}</p>
                    <label style="display:block;margin-top:14px;font-weight:600">Booking status
                        <select id="dashboard-booking-status" style="display:block;width:100%;margin:6px 0 12px;padding:9px;border:1px solid #cbd5e1;border-radius:8px">
                            ${buildStatusOptions(bookingStatuses, booking.bookingStatusId)}
                        </select>
                    </label>
                    <label style="display:block;font-weight:600">Payment status
                        <select id="dashboard-payment-status" style="display:block;width:100%;margin:6px 0;padding:9px;border:1px solid #cbd5e1;border-radius:8px">
                            ${buildStatusOptions(paymentStatuses, booking.paymentStatusId)}
                        </select>
                    </label>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save statuses',
            cancelButtonText: 'Close',
            showLoaderOnConfirm: true,
            didOpen: popup => {
                const bookingSelect = popup.querySelector('#dashboard-booking-status');
                const paymentSelect = popup.querySelector('#dashboard-payment-status');
                const syncCancellation = () => {
                    const isCancelled = ['cancelled', 'canceled'].includes(normalizeStatus(bookingSelect.selectedOptions[0]?.textContent));
                    if (isCancelled) {
                        const cancelledOption = Array.from(paymentSelect.options).find(option => ['cancelled', 'canceled'].includes(normalizeStatus(option.textContent)));
                        if (cancelledOption) paymentSelect.value = cancelledOption.value;
                    }
                    paymentSelect.disabled = isCancelled;
                };
                bookingSelect.addEventListener('change', syncCancellation);
                syncCancellation();
            },
            preConfirm: async () => {
                const newStatusId = document.getElementById('dashboard-booking-status').value;
                const newPaymentStatusId = document.getElementById('dashboard-payment-status').value;
                try {
                    const response = await fetch('../api/admin/update_status.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            booking_id: booking.id,
                            status_type: 'both',
                            new_status_id: newStatusId,
                            new_payment_status_id: newPaymentStatusId
                        })
                    });
                    const result = await response.json().catch(() => ({}));
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'The statuses could not be saved.');
                    }
                    return result;
                } catch (error) {
                    Swal.showValidationMessage(error.message || 'The statuses could not be saved. Please try again.');
                    return false;
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Statuses updated',
                    text: result.value.message || 'Booking and payment statuses updated.'
                }).then(() => window.location.reload());
            }
        });
    }

var calendarEl = document.getElementById('calendar');
var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: events.map(e => {
        let bgColor = "#e2e8f0", borderColor = "#94a3b8", textColor = "#334155";
        switch (normalizeStatus(e.status)) {
            case 'pending': bgColor = '#fef3c7'; borderColor = '#f59e0b'; textColor = '#78350f'; break;
            case 'booked':
            case 'confirmed': bgColor = '#dcfce7'; borderColor = '#22c55e'; textColor = '#14532d'; break;
            case 'cancelled':
            case 'canceled': bgColor = '#fee2e2'; borderColor = '#ef4444'; textColor = '#7f1d1d'; break;
        }

        // Highlight the latest booking with a glow/bold border
        let highlightStyle = {};
        if (e.id === latestBookingId) {
            borderColor = "#000"; // black border
            highlightStyle = {
                classNames: ["latest-booking"] // custom class
            };
        }

        return {
            ...e,
            backgroundColor: bgColor,
            borderColor: e.conflict ? "red" : borderColor,
            textColor: textColor,
            ...highlightStyle
        };
    }),
    eventClick: function(info) {
        const clickedEvent = events.find(e => String(e.id) === String(info.event.id));
        if (!clickedEvent) return;

        if (clickedEvent.conflict) {
            let conflicts = events.filter(e => {
                if (e.id === clickedEvent.id) return false;
                if (!isBooked(e.status)) return false;

                let startA = new Date(`${String(clickedEvent.start).slice(0, 10)}T00:00:00Z`);
                let endA   = new Date(`${String(clickedEvent.end).slice(0, 10)}T00:00:00Z`);
                let startB = new Date(`${String(e.start).slice(0, 10)}T00:00:00Z`);
                let endB   = new Date(`${String(e.end).slice(0, 10)}T00:00:00Z`);

                return (startA < endB && startB < endA);
            });

            let conflictHtml = `
                <p><strong>Clicked Booking:</strong><br>
                ${escapeHtml(clickedEvent.customerName)}<br>
                ${escapeHtml(displayDate(clickedEvent.start))} – ${escapeHtml(displayDate(clickedEvent.end))}</p>
                <hr><p><strong>Overlapping with:</strong></p>
            `;
            conflicts.forEach(c => {
                conflictHtml += `
                    <p>${escapeHtml(c.customerName)}<br>${escapeHtml(displayDate(c.start))} – ${escapeHtml(displayDate(c.end))}</p>
                `;
            });

            Swal.fire({
                icon: 'warning',
                title: 'Booking conflict',
                html: conflictHtml,
                showCancelButton: true,
                confirmButtonText: 'Edit statuses',
                cancelButtonText: 'Close',
                confirmButtonColor: '#0f766e'
            }).then(result => {
                if (result.isConfirmed) openStatusEditor(clickedEvent);
            });
        } else {
            openStatusEditor(clickedEvent);
        }
    }
});
calendar.render();



    // ------------------ BOOKINGS TABLE ------------------
    function populateBookingsTable() {
        const tableBody = document.getElementById('bookings-table-body');
        if (!events.length) {
            tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500">No bookings found.</td></tr>';
            return;
        }
        events.slice(0, 10).forEach(event => {
            if(event.title !== "Available") {
                const row = tableBody.insertRow();
                row.innerHTML = `
                    <td class="py-3 px-6 text-left whitespace-nowrap font-mono text-xs">${escapeHtml(event.id || '')}</td>
                    <td class="py-3 px-6 text-left">${escapeHtml(event.customerName || 'Unavailable')}</td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">${escapeHtml(displayDate(event.start))}</td>
                    <td class="py-3 px-6 text-left whitespace-nowrap">${escapeHtml(displayDate(event.end))}</td>
                    <td class="py-3 px-6 text-left"><span class="dashboard-status-pill dashboard-status-${statusTone(event.status)}">${escapeHtml(event.status || 'Unavailable')}</span></td>
                    <td class="py-3 px-6 text-left"><span class="dashboard-conflict-pill dashboard-conflict-${event.conflict ? 'yes' : 'no'}">${event.conflict ? 'Yes' : 'No'}</span></td>
                `;
            }
        });
    }

    populateBookingsTable();
});
</script>


</body></html>
