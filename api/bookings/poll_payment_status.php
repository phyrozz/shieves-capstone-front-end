<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checking Payment Status</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <div id="status-message">Checking payment status...</div>
    <script>
        // Function to check payment status
        function checkPaymentStatus(invoiceId) {
            axios.get('check_payment_status.php?invoice_id=' + invoiceId)
                .then(function (response) {
                    if (response.data.status === 'success') {
                        document.getElementById('status-message').innerText = 'Payment successful! Redirecting...';
                        // Redirect to success page after a short delay
                        setTimeout(function() {
                            window.location.href = 'success_page.php'; // Replace with your success page
                        }, 2000);
                    } else if (response.data.status === 'pending') {
                        document.getElementById('status-message').innerText = 'Payment is still pending. Checking again...';
                        // Check again after a short delay
                        setTimeout(function() {
                            checkPaymentStatus(invoiceId);
                        }, 5000);
                    } else {
                        document.getElementById('status-message').innerText = 'Error: ' + response.data.message;
                    }
                })
                .catch(function (error) {
                    document.getElementById('status-message').innerText = 'Error: ' + error.message;
                });
        }

        // Get invoice ID from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const invoiceId = urlParams.get('invoice_id');

        if (invoiceId) {
            checkPaymentStatus(invoiceId);
        } else {
            document.getElementById('status-message').innerText = 'Invoice ID is missing';
        }
    </script>
</body>
</html>
