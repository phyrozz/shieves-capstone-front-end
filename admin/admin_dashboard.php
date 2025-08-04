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
                
        $stmt = $conn->prepare("SELECT bookings.name AS customer_name, packages.name AS package_name, bookings.time_in AS time_in, bookings.time_out AS time_out
        FROM bookings
        INNER JOIN packages ON bookings.package_id = packages.id 
        WHERE payment_status_id = 2");

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "{";
            echo "title: '" . $row['customer_name'] . " - " . $row['package_name'] . "',";
            echo "start: '" . $row['time_in'] . "',";
            echo "end: '" . $row['time_out'] . "'";
            echo "},";
        }

        $stmt->close();
        $conn->close();
    ?>];

    console.log(events)

    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: events,
        eventClick: function(info) {
            alert('Customer Name: ' + info.event.title.split(' - ')[0] + '\nPackage Name: ' + info.event.title.split(' - ')[1]);
        }
    });
    calendar.render();
});

function populateBookingsTable() {
    const bookings = [
        { id: 'B009', name: 'Mr. Johnson', checkIn: '2023-06-15', checkOut: '2023-06-20', status: 'VIP' },
        { id: 'B001', name: 'John Doe', checkIn: '2023-06-10', checkOut: '2023-06-15', status: 'Checked Out' },
        { id: 'B002', name: 'Jane Smith', checkIn: '2023-06-12', checkOut: '2023-06-14', status: 'Checked Out' },
        { id: 'B003', name: 'Bob Johnson', checkIn: '2023-06-18', checkOut: '2023-06-22', status: 'Confirmed' },
        { id: 'B004', name: 'Alice Brown', checkIn: '2023-06-20', checkOut: '2023-06-25', status: 'Confirmed' },
        { id: 'B005', name: 'Charlie Wilson', checkIn: '2023-06-22', checkOut: '2023-06-24', status: 'Confirmed' },
        { id: 'B006', name: 'Eva Green', checkIn: '2023-06-25', checkOut: '2023-06-30', status: 'Confirmed' },
        { id: 'B007', name: 'David Lee', checkIn: '2023-07-01', checkOut: '2023-07-05', status: 'Confirmed' },
        { id: 'B008', name: 'Sophia Chen', checkIn: '2023-07-03', checkOut: '2023-07-08', status: 'Confirmed' }
    ];

    const tableBody = document.getElementById('bookings-table-body');
    bookings.forEach(booking => {
        const row = tableBody.insertRow();
        row.innerHTML = `
            <td class="py-3 px-6 text-left whitespace-nowrap">${booking.id}</td>
            <td class="py-3 px-6 text-left">${booking.name}</td>
            <td class="py-3 px-6 text-left">${booking.checkIn}</td>
            <td class="py-3 px-6 text-left">${booking.checkOut}</td>
            <td class="py-3 px-6 text-left">${booking.status}</td>
        `;
    });
}

populateBookingsTable();
</script>

</body></html>