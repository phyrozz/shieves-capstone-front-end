<?php 
include "../conn.php";

session_start();

if (!isset($_SESSION["username"])) {
    header("location: login.php");
}
?>
<html>
    <head>
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
</head>
<div class="flex min-h-screen bg-secondary">
        <?php include "../components/admin_navbar.php"; ?>
        <div class="flex-1 p-8 bg-gradient-to-br bg-secondary h-screen col-span-9 pl-72">
<body class="bg-secondary">
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Booking Calendar</h3>
            <div id="calendar" class="h-3/4"></div>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Bookings</h3>
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Booking ID</th>
                        <th class="py-3 px-6 text-left">Guest Name</th>
                        <th class="py-3 px-6 text-left">Check-in</th>
                        <th class="py-3 px-6 text-left">Check-out</th>
                        <th class="py-3 px-6 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light" id="bookings-table-body">
                    <!-- Bookings will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var events = [<?php 
        include "../conn.php";
                
        $stmt = $conn->prepare("SELECT bookings.id AS booking_id,
                                       bookings.name AS customer_name, 
                                       packages.name AS package_name, 
                                       bookings.time_in AS time_in, 
                                       bookings.time_out AS time_out,
                                       bookings.payment_status_id AS status
                                FROM bookings
                                INNER JOIN packages 
                                ON bookings.package_id = packages.id");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $color = "";
            $statusLabel = "";
            switch ($row['status']) {
                case 1: $color = "gray"; $statusLabel="Pending"; break;
                case 2: $color = "orange"; $statusLabel="Confirmed"; break;
                case 3: $color = "red"; $statusLabel="Cancelled"; break;
                default: $color = "green"; $statusLabel="Available"; break;
            }

            echo "{";
            echo "id: '" . $row['booking_id'] . "',";
            echo "title: '" . $row['customer_name'] . " - " . $row['package_name'] . "',";
            echo "start: '" . $row['time_in'] . "',";
            echo "end: '" . $row['time_out'] . "',";
            echo "status: '" . $statusLabel . "',";
            echo "color: '" . $color . "',";
            echo "conflict: false";
            echo "},";
        }

        $stmt->close();
        $conn->close();
    ?>];

    // ------------------ CONFLICT CHECKER ------------------
    function checkConflicts() {
        for (let i = 0; i < events.length; i++) {
            for (let j = i + 1; j < events.length; j++) {
                let a = events[i];
                let b = events[j];

                if (a.status === "Confirmed" && b.status === "Confirmed") {
                    let startA = new Date(a.start);
                    let endA   = new Date(a.end);
                    let startB = new Date(b.start);
                    let endB   = new Date(b.end);

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

    // Find the latest booking by created_at or fallback to last element
let latestBookingId = null;
if (events.length > 0) {
    // Assuming your booking object has created_at or id sequence
    let sorted = [...events].sort((a, b) => new Date(b.created_at || b.start) - new Date(a.created_at || a.start));
    latestBookingId = sorted[0].id;
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
        let bgColor, borderColor, textColor = "white";

        switch (e.status) {
            case 1: bgColor = "gray"; borderColor = "darkgray"; break;   // Pending
            case 2: bgColor = "orange"; borderColor = "darkorange"; break; // Booked
            case 3: bgColor = "red"; borderColor = "darkred"; break;     // Cancelled
            default: bgColor = "green"; borderColor = "darkgreen"; break; // Available
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
        const clickedEvent = events.find(e => e.id == info.event.id);

        let statusLabel = "green";
        let statusColor = info.event.backgroundColor || "green";

        switch (info.event.extendedProps.status) {
            case 1: statusLabel = "Pending"; statusColor = "gray"; break;
            case 2: statusLabel = "Booked"; statusColor = "orange"; break;
            case 3: statusLabel = "Cancelled"; statusColor = "red"; break;
            default: statusLabel = "Available"; statusColor = "green"; break;
        }

        if (clickedEvent.conflict) {
            let conflicts = events.filter(e => {
                if (e.id === clickedEvent.id) return false;
                if (e.status !== 2) return false;

                let startA = new Date(clickedEvent.start);
                let endA   = new Date(clickedEvent.end);
                let startB = new Date(e.start);
                let endB   = new Date(e.end);

                return (startA < endB && startB < endA);
            });

            let conflictHtml = `
                <p><strong>Clicked Booking:</strong><br>
                ${clickedEvent.title}<br>
                ${clickedEvent.start} → ${clickedEvent.end}</p>
                <hr><p><strong>Overlapping with:</strong></p>
            `;
            conflicts.forEach(c => {
                conflictHtml += `
                    <p>🔸 ${c.title}<br>${c.start} → ${c.end}</p>
                `;
            });

            Swal.fire({
                icon: 'warning',
                title: '⚠️ Conflict Detected',
                html: conflictHtml,
                confirmButtonText: 'Close',
                confirmButtonColor: '#d33'
            });
        } else {
            Swal.fire({
                title: 'Booking Details',
                html: `
                    <div style="text-align: left; line-height: 1.6;">
                        <p><strong>Customer:</strong> ${info.event.title.split(' - ')[0]}</p>
                        <p><strong>Package:</strong> ${(info.event.title.split(' - ')[1] || "N/A")}</p>
                        <p><strong>Status:</strong> <span style="color:${statusColor}; font-weight:bold;">${statusLabel}</span></p>
                        <p><strong>Check-in:</strong> ${info.event.start.toISOString().slice(0,16).replace('T',' ')}</p>
                        <p><strong>Check-out:</strong> ${info.event.end ? info.event.end.toISOString().slice(0,16).replace('T',' ') : 'N/A'}</p>
                    </div>
                `,
                confirmButtonText: 'Close',
                width: 500
            });
        }
    }
});
calendar.render();



    // ------------------ BOOKINGS TABLE ------------------
    function populateBookingsTable() {
        const tableBody = document.getElementById('bookings-table-body');
        events.forEach(event => {
            if(event.title !== "Available") {
                const row = tableBody.insertRow();
                row.innerHTML = `
                    <td class="py-3 px-6 text-left whitespace-nowrap">${event.id || ''}</td>
                    <td class="py-3 px-6 text-left">${event.title.split(' - ')[0]}</td>
                    <td class="py-3 px-6 text-left">${event.start}</td>
                    <td class="py-3 px-6 text-left">${event.end}</td>
                    <td class="py-3 px-6 text-left">${event.status}</td>
                    <td class="py-3 px-6 text-left">${event.conflict ? "⚠️ Yes" : "No"}</td>
                `;
            }
        });
    }

    populateBookingsTable();
});
</script>


</body></html>