<?php
session_start(); // Start the session.

// Unset user specific session variables.
if (isset($_SESSION['username'])) {
    unset($_SESSION['username']); // Remove the user identifier.
}

// Redirect to the user login page.
header('Location: login.php');
exit;
?>