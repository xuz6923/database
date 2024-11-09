<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Smart Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="forgot-password-page">
    <header>
        <h1>Reset Your Password</h1>
    </header>

    <div class="forgot-password-container">
        <form action="reset-password.php" method="POST" class="forgot-password-form">
            <h2>Forgot Password</h2>

            <!-- Username Input -->
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <!-- New Password Input -->
            <div class="input-group">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new-password" required>
            </div>

            <!-- Confirm Password Input -->
            <div class="input-group">
                <label for="confirm-password">Confirm Your Password</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>


            <button type="submit" class="forgot-password-btn">Submit</button>
            
             <!-- Back to Login -->
             <div class="options">
                <a href="login.php">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>
