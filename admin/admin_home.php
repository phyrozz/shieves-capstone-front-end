<?php
include "../conn.php";

session_start();


if(!isset ($_SESSION["username"]))
{
    header("location: admin_home.php");
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

$totalStmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings");
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalRecords = $totalResult->fetch_assoc()['count'];
$totalStmt->close();
$totalPages = ceil($totalRecords / $limit);

$stmt = $conn->prepare("SELECT bookings.id as booking_id, bookings.name AS full_name, bookings.email, bookings.phone_number, bookings.status_id, statuses.name AS status_name, packages.name AS package_name, packages.price as package_price
                        FROM bookings 
                        INNER JOIN statuses ON bookings.status_id = statuses.id
                        INNER JOIN packages ON bookings.package_id = packages.id
                        LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museo de San Pedro Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tailwind.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="../node_modules/axios/dist/axios.min.js"></script>

</head>
<body>
    <div class="bg-gradient-to-br from-slate-950 to-violet-950 h-screen">
        <?php include "../components/admin_navbar.php"; ?>
        <div class="flex flex-col items-center justify-center">
            <h2 class="text-4xl text-center font-satisfy pt-6 pb-10 text-white">Bookings</h2>
            <div class="bg-slate-950 relative overflow-auto w-[90vw] p-5 shadow-2xl shadow-slate-300 rounded-xl max-h-[90vh]">
                <table class="table w-full text-left rtl:text-right bg-slate-950 text-white">
                    <thead>
                        <tr class="font-bold text-sm">
                            <th scope="col" class="px-6 py-3">NAME</th>
                            <th scope="col" class="px-6 py-3">EMAIL</th>
                            <th scope="col" class="px-6 py-3">CONTACT NUMBER</th>
                            <th scope="col" class="px-6 py-3">PACKAGE</th>
                            <th scope="col" class="px-6 py-3">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="odd:bg-slate-800 even:bg-slate-900">
                            <td class="px-6 py-4"><p class='text-left'><?= htmlspecialchars($row["full_name"]) ?></p></td>
                            <td class="px-6 py-4"><p class='text-left'><?= htmlspecialchars($row["email"]) ?></p></td>
                            <td class="px-6 py-4"><p class='text-left'><?= htmlspecialchars($row["phone_number"]) ?></p></td>
                            <td class="px-6 py-4"><p class='text-left'><?= htmlspecialchars($row["package_name"]) ?></p></td>
                            <td class="px-6 py-4">
                                <select class='border-2 rounded-md p-1 bg-slate-700 border-slate-800' onchange='updateStatus(this, <?= $row["booking_id"] ?>)'>
                                    <?php foreach ($statuses as $status): ?>
                                        <option value='<?= htmlspecialchars($status["id"]) ?>' <?= $row["status_id"] == $status["id"] ? "selected" : "" ?>><?= htmlspecialchars($status["name"]) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="flex justify-center mt-4 mb-3">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="mx-1 px-3 py-1 bg-gray-300 text-gray-800 hover:bg-gray-400 transition-colors rounded">&laquo; Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="mx-1 px-3 py-1 <?= $i == $page ? 'bg-gray-800 text-white hover:bg-gray-900' : 'bg-gray-300 hover:bg-gray-400' ?> transition-colors rounded"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="mx-1 px-3 py-1 bg-gray-300 text-gray-800 hover:bg-gray-400 transition-colors rounded">Next &raquo;</a>
                    <?php endif; ?>
                </div>
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
    </script>
</body>
</html>