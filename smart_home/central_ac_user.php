<?php
session_start();

// 检查用户是否已登录。如果没有登录，重定向到登录页面
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // 当前登录用户的 ID

// 连接数据库
$conn = new mysqli('localhost', 'root', '', 'smart_home');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 更新当前用户的空调设置
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temperature = $_POST['temperature'];
    $mode = $_POST['mode'];
    $is_on = isset($_POST['is_on']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE user_air_conditioners SET temperature = ?, mode = ?, is_on = ? WHERE user_id = ?");
    $stmt->bind_param("isii", $temperature, $mode, $is_on, $user_id);
    $stmt->execute();
    $stmt->close();
}

// 获取当前用户的空调设置
$stmt = $conn->prepare("SELECT * FROM user_air_conditioners WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_ac_settings = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central Air Conditioner Control</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Central Air Conditioner Control for <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    </header>

    <div class="container">
        <!-- 温度控制表单 -->
        <form method="POST" class="ac-control-form">
            <h2>Your Air Conditioner Control</h2>

            <!-- 空调开关 -->
            <label for="is_on">Power:</label>
            <input type="checkbox" name="is_on" id="is_on" <?php echo $user_ac_settings['is_on'] ? 'checked' : ''; ?>>

            <!-- 温度控制 -->
            <label for="temperature">Temperature: </label>
            <input type="range" id="temperature" name="temperature" min="16" max="30" value="<?php echo $user_ac_settings['temperature']; ?>">
            <span id="tempDisplay"><?php echo $user_ac_settings['temperature']; ?>°C</span>

            <!-- 模式选择 -->
            <label for="mode">Mode: </label>
            <select name="mode" id="mode">
                <option value="cool" <?php if ($user_ac_settings['mode'] === 'cool') echo 'selected'; ?>>Cooling</option>
                <option value="heat" <?php if ($user_ac_settings['mode'] === 'heat') echo 'selected'; ?>>Heating</option>
                <option value="fan" <?php if ($user_ac_settings['mode'] === 'fan') echo 'selected'; ?>>Fan</option>
            </select>

            <button type="submit">Update Settings</button>
        </form>
    </div>

    <script>
        // 实时显示当前温度值
        const temperatureInput = document.getElementById('temperature');
        const tempDisplay = document.getElementById('tempDisplay');

        temperatureInput.addEventListener('input', function () {
            tempDisplay.textContent = `${this.value}°C`;
        });
    </script>
</body>
</html>
