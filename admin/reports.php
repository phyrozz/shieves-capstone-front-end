<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J.M. Apilado Resort - Sales Reports</title>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="flex min-h-screen bg-secondary">
        <?php include "../components/admin_navbar.php"; ?>
        <div class="w-64 bg-white shadow-md">
            <!-- Sidebar content here -->
        </div>
        <div class="flex-1 ml-64 p-8";>
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Reports</h1>
            
            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Revenue Overview</h3>
                <div class="h-64">
                    <canvas id="revenue-chart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent PAID Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-200 text-gray-600">
                            <tr>
                                <th class="py-3 px-4 text-left">Transaction ID</th>
                                <th class="py-3 px-4 text-left">Guest Name</th>
                                <th class="py-3 px-4 text-left">Amount</th>
                                <th class="py-3 px-4 text-left">Date</th>
                                <th class="py-3 px-4 text-left">Payment Method</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600" id="paid-transactions">
                            <!-- Table rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Revenue (YTD)</h3>
                    <div class="text-3xl font-bold text-blue-600">$4,275,890</div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Average Daily Rate</h3>
                    <div class="text-3xl font-bold text-green-600">$325</div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.p-2').forEach(item => {
        item.addEventListener('click', function() {
            if (this.textContent !== 'Reports') {
                window.location.href = 'https://admin.luxuryresort.com/' + this.textContent.toLowerCase();
            }
        });
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Revenue',
                data: [320000, 350000, 400000, 450000, 500000, 550000, 600000, 650000, 600000, 550000, 500000, 450000],
                borderColor: '#3498db',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Populate PAID Transactions Table
    const paidTransactions = [
        { id: 'T1001', name: 'John Doe', amount: 1250, date: '2023-06-15', method: 'Credit Card' },
        { id: 'T1002', name: 'Jane Smith', amount: 980, date: '2023-06-14', method: 'PayPal' },
        { id: 'T1003', name: 'Robert Johnson', amount: 2100, date: '2023-06-13', method: 'Bank Transfer' },
        { id: 'T1004', name: 'Emily Brown', amount: 1750, date: '2023-06-12', method: 'Credit Card' },
        { id: 'T1005', name: 'Michael Wilson', amount: 3200, date: '2023-06-11', method: 'Credit Card' },
        { id: 'T1006', name: 'Sarah Davis', amount: 890, date: '2023-06-10', method: 'PayPal' },
        { id: 'T1007', name: 'David Lee', amount: 1600, date: '2023-06-09', method: 'Bank Transfer' },
        { id: 'T1008', name: 'Lisa Anderson', amount: 2300, date: '2023-06-08', method: 'Credit Card' },
    ];

    const tableBody = document.getElementById('paid-transactions');
    paidTransactions.forEach(transaction => {
        const row = tableBody.insertRow();
        row.innerHTML = `
            <td class="py-2 px-4">${transaction.id}</td>
            <td class="py-2 px-4">${transaction.name}</td>
            <td class="py-2 px-4">$${transaction.amount.toLocaleString()}</td>
            <td class="py-2 px-4">${transaction.date}</td>
            <td class="py-2 px-4">${transaction.method}</td>
        `;
    });
    </script>
</body>
</html>