<?php
// Start the session to store user information
session_start();

// Display errors for debugging purposes (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "smart_home";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check for a connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted username and password
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Prepare the SQL query to fetch the stored password from the database
    $sql = "SELECT password FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Fetch the result row containing the stored plaintext password
        $row = $result->fetch_assoc();

        // Directly compare the entered password with the stored plaintext password
        if ($password === $row['password']) {
            // Store the username in the session
            $_SESSION['username'] = $username;
            
            // Redirect to the floorplan PHP page
            header("Location: floorplan.php");
            exit;
        } else {
            // Invalid password error message
            $_SESSION['error'] = "Invalid password. Please try again.";
            header("Location: login.php");
            exit;
        }
    } else {
        // Username not found error message
        $_SESSION['error'] = "Username not found.";
        header("Location: login.php");
        exit;
    }
}

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Smart Home</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // JavaScript validation for username and password
        function validateForm() {
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;
            
            // Simplified patterns to match your previous working code
            const usernamePattern = /^[a-zA-Z0-9]{5,}$/;
            const passwordPattern = /^.{5,}$/; // Only checks that password has at least 5 characters

            if (!usernamePattern.test(username)) {
                alert("Username must be at least 5 characters long and contain only letters and numbers.");
                return false;
            }
            if (!passwordPattern.test(password)) {
                alert("Password must be at least 5 characters long.");
                return false;
            }
            return true;
        }

        // Display error messages from the session (if any)
        window.onload = function() {
            <?php if (isset($_SESSION['error'])): ?>
                alert("<?php echo $_SESSION['error']; ?>");
                <?php unset($_SESSION['error']); // Clear the error after displaying it ?>
            <?php endif; ?>
        };
    </script>
</head>
<body class="login-page">
    <header>
        <h1>Welcome Back to Smart Home</h1>
    </header>

    <div class="login-container">
        <form action="login.php" method="POST" class="login-form" onsubmit="return validateForm()">
            <h2>Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
            <div class="options">
                <a href="forgot_password.php">Forgot Password?</a> | <a href="signup.php">Sign Up</a>
            </div>
        </form>
    </div>
</body>
</html>
