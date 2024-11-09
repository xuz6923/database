<?php
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $homeAddress = $_POST['home-address'];
    $familyMembers = $_POST['family-members'];

    // Validation for username and password
    $usernamePattern = '/^[a-zA-Z0-9]{5,}$/';
    $passwordPattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/';

    if (!preg_match($usernamePattern, $username)) {
        echo "<p style='color:red;'>Invalid username. It must be at least 5 characters long and contain only letters and numbers.</p>";
    } elseif (!preg_match($passwordPattern, $password)) {
        echo "<p style='color:red;'>Password must be at least 8 characters long and contain at least one letter and one number.</p>";
    } elseif ($password !== $confirmPassword) {
        echo "<p style='color:red;'>Passwords do not match.</p>";
    } else {
        // Connect to the database
        $conn = new mysqli('localhost', 'root', '', 'smart_home');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Username already exists
            echo "<p style='color:red;'>Username already exists. Please choose a different one.</p>";
        } else {
            // Proceed with registration

            // No password hashing, using plain text password
            // Insert user data into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, home_address, family_members) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $username, $email, $password, $homeAddress, $familyMembers); // 's' for string and 'i' for integer

            if ($stmt->execute()) {
                // JavaScript alert and redirection after success
                echo "<script>
                    alert('Account created successfully!');
                    window.location.href = 'login.php';
                </script>";
            } else {
                echo "<p style='color:red;'>Error creating account. Please try again.</p>";
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Smart Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="signup-page">
    <header>
        <h1>Create Your Account</h1>
    </header>

    <div class="signup-container">
        <form action="signup.php" method="POST" class="signup-form">
            <!-- Section 1: Basic Information -->
            <div class="form-section">
                <h2>Basic Information</h2>
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </div>
            </div>

            <!-- Section 2: Home Information -->
            <div class="form-section">
                <h2>Home Information</h2>
                <div class="input-group">
                    <label for="home-address">Home Address</label>
                    <input type="text" id="home-address" name="home-address" required>
                </div>
                <div class="input-group">
                    <label for="family-members">Number of Family Members</label>
                    <input type="number" id="family-members" name="family-members" min="1" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="signup-btn">Sign Up</button>

            <div class="options">
                <a href="login.php">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>
