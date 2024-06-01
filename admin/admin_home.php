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

// Retrieve all bookings with their statuses
$stmt = $conn->prepare("SELECT bookings.id as booking_id, bookings.name AS full_name, bookings.email, bookings.phone_number, bookings.status_id, statuses.name AS status_name, packages.name AS package_name, packages.price as package_price
                        FROM bookings 
                        INNER JOIN statuses ON bookings.status_id = statuses.id
                        INNER JOIN packages ON bookings.package_id = packages.id
                        ");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Museo de San Pedro Admin</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Satisfy&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tailwind.css">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="../node_modules/axios/dist/axios.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                    <h2 class="display-6">ADMIN PANEL</h2>
                    </div>
                    <div class="card-body">
                      <table class="table table-bordered text-center">
                        <thead>
                            <tr class="font-bold text-sm">
                                <td>NAME</td>
                                <td>EMAIL</td>
                                <td>CONTACT NUMBER</td>
                                <td>PACKAGE</td>
                                <td>STATUS</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr >";
                            echo "<td><p class='text-left'>" . htmlspecialchars($row["full_name"]) . "</p></td>";
                            echo "<td><p class='text-left'>" . htmlspecialchars($row["email"]) . "</p></td>";
                            echo "<td><p class='text-left'>" . htmlspecialchars($row["phone_number"]) . "</p></td>";
                            echo "<td><p class='text-left'>" . htmlspecialchars($row["package_name"]) . "</p></td>";
                            echo "<td>";
                            echo "<select class='text-sm' onchange='updateStatus(this, " . $row['booking_id'] . ")'>";
                            foreach ($statuses as $status) {
                                $selected = $row["status_id"] == $status['id'] ? "selected" : "";
                                echo "<option value='" . htmlspecialchars($status['id']) . "' $selected>" . htmlspecialchars($status['name']) . "</option>";
                            }
                            echo "</select>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        </tbody>
                      </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="logout-container">
        <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <script>
        function updateStatus(select, bookingId) {
            const newStatusId = select.value;
            axios.post('update_status.php', { booking_id: bookingId, new_status_id: newStatusId })
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