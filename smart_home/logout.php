<?php
session_start();
session_destroy(); // Destroy all session data

// Redirect to login page
header('Location: login.php');
exit;
?>
