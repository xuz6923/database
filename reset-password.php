<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'smart_home');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the current password for the user from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $current_password = $user['password'];

        // Check if the new password is the same as the current password
        if ($new_password === $current_password) {
            echo "<script>
                alert('The new password cannot be the same as the current password. Please choose a different password.');
                window.location.href = 'forgot_password.php';
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('Username not found. Please try again.');
            window.location.href = 'forgot_password.php';
        </script>";
        exit();
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        echo "<script>
            alert('Passwords do not match. Please try again.');
            window.location.href = 'forgot_password.php';
        </script>";
        exit();
    }

    // Update the password directly without hashing
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $new_password, $username);

    if ($stmt->execute()) {
        echo "<script>
            alert('Password has been reset successfully. Please log in.');
            window.location.href = 'login.php';
        </script>";
    } else {
        echo "<script>
            alert('Error updating password. Please try again.');
            window.location.href = 'forgot_password.php';
        </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
