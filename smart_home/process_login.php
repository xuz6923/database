<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 连接数据库
    $conn = new mysqli('localhost', 'root', '', 'smart_home');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 检查用户名是否存在
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // 验证密码
        if (password_verify($password, $user['password'])) {
            // 登录成功，设置 session
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id']; // Assuming user_id is the column name for user ID
            $_SESSION['username'] = $username;

            // 重定向到 floorplan.php
            header("Location: floorplan.php");
            exit();
        } else {
            // 密码不正确
            $_SESSION['error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }
    } else {
        // 用户名不正确
        $_SESSION['error'] = "Invalid username.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
