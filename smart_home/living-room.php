<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "Session not set! Redirecting to login page.";
    header("Location: login.php");
    exit();
} else {
    echo "Session is set for user: " . $_SESSION['username'];
}


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
$stmt = $conn->prepare("SELECT * FROM ac WHERE user_id = ?");
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
    <title>Living Room Controls</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Living Room Control</h1>
    </header>

    <div class="container">
        <!-- Living Room Light Control -->
        <div class="device-control light-control">
            <h2>Lights</h2>
            <div id="light-bulb" class="light-bulb off"></div>
            <label for="brightness">Brightness</label>
            <input type="range" id="brightness" min="0" max="100" value="50" disabled>

            <h3>RGB Control</h3>
            <label for="red">Red</label>
            <input type="range" id="red" min="0" max="255" value="128" disabled>
            <p>Red: <span id="redValue">128</span></p>

            <label for="green">Green</label>
            <input type="range" id="green" min="0" max="255" value="128" disabled>
            <p>Green: <span id="greenValue">128</span></p>

            <label for="blue">Blue</label>
            <input type="range" id="blue" min="0" max="255" value="128" disabled>
            <p>Blue: <span id="blueValue">128</span></p>

            <button id="toggle-light">Toggle Light</button>
            <p>Status: <span id="lightStatus">Off</span></p>
        </div>

        <!-- Living Room Curtain Control -->
        <div class="device-control curtain-control">
            <h2>Curtain Control</h2>
            <div class="curtain-container">
                <div id="left-curtain" class="curtain curtain-left"></div>
                <div id="right-curtain" class="curtain curtain-right"></div>
            </div>

            <label for="leftCurtainRange">Left Curtain</label>
            <input type="range" id="leftCurtainRange" min="0" max="100" value="100">
            <p>Left Status: <span id="leftCurtainStatus">Closed</span></p>

            <label for="rightCurtainRange">Right Curtain</label>
            <input type="range" id="rightCurtainRange" min="0" max="100" value="100">
            <p>Right Status: <span id="rightCurtainStatus">Closed</span></p>
        </div>

        <!-- Living Room Air Conditioner Control -->
        <div class="device-control ac-control">
            <h2>Air Conditioner</h2>
            <div id="ac-dial" class="ac-dial off">
                <div class="dial-center">
                    <span id="acTempDisplay">22°C</span>
                </div>
            </div>
            <select id="modeSelect">
                <option value="cool">Cooling</option>
                <option value="heat">Heating</option>
                <option value="fan">Fan</option>
            </select>
            <input type="range" id="temperature" min="16" max="30" value="22" disabled>
            <button id="toggle-ac">Toggle AC</button>
            <p>Mode: <span id="acModeDisplay">Cooling</span></p>
            <p>Temperature: <span id="tempDisplay">22</span>°C</p>
        </div>

        <!-- Back to Floor Plan Button -->
        <div class="back-to-floorplan">
            <button onclick="goToFloorplan()">Back to Floor Plan</button>
        </div>
    </div>

    <script>
        // Light Control Logic
        const lightBulb = document.getElementById('light-bulb');
        const brightnessInput = document.getElementById('brightness');
        const redInput = document.getElementById('red');
        const greenInput = document.getElementById('green');
        const blueInput = document.getElementById('blue');
        const lightStatus = document.getElementById('lightStatus');
        let isLightOn = false;

        document.getElementById('toggle-light').addEventListener('click', () => {
            isLightOn = !isLightOn;
            lightStatus.textContent = isLightOn ? 'On' : 'Off';
            lightBulb.classList.toggle('off', !isLightOn);
            [brightnessInput, redInput, greenInput, blueInput].forEach(input => {
                input.disabled = !isLightOn;
            });
        });

        function updateLightColor() {
            const r = redInput.value, g = greenInput.value, b = blueInput.value;
            const brightness = brightnessInput.value / 100;
            lightBulb.style.backgroundColor = `rgba(${r}, ${g}, ${b}, ${brightness})`;
        }

        [brightnessInput, redInput, greenInput, blueInput].forEach(input => {
            input.addEventListener('input', updateLightColor);
        });

        // Curtain Control Logic
        const leftCurtain = document.getElementById('left-curtain');
        const rightCurtain = document.getElementById('right-curtain');
        const leftCurtainRange = document.getElementById('leftCurtainRange');
        const rightCurtainRange = document.getElementById('rightCurtainRange');
        const leftCurtainStatus = document.getElementById('leftCurtainStatus');
        const rightCurtainStatus = document.getElementById('rightCurtainStatus');

        leftCurtainRange.addEventListener('input', () => {
            const value = leftCurtainRange.value;
            leftCurtain.style.width = `${value}%`;
            leftCurtainStatus.textContent = value === '0' ? 'Open' : value === '100' ? 'Closed' : 'Partially Open';
        });

        rightCurtainRange.addEventListener('input', () => {
            const value = rightCurtainRange.value;
            rightCurtain.style.width = `${value}%`;
            rightCurtainStatus.textContent = value === '0' ? 'Open' : value === '100' ? 'Closed' : 'Partially Open';
        });

        // Air Conditioner Control Logic
        const acDial = document.getElementById('ac-dial');
        const acTempDisplay = document.getElementById('acTempDisplay');
        const tempInput = document.getElementById('temperature');
        const modeSelect = document.getElementById('modeSelect');
        const acModeDisplay = document.getElementById('acModeDisplay');
        const tempDisplay = document.getElementById('tempDisplay');
        let isAcOn = false;

        document.getElementById('toggle-ac').addEventListener('click', () => {
            isAcOn = !isAcOn;
            acDial.classList.toggle('off', !isAcOn);
            acTempDisplay.style.display = isAcOn ? 'block' : 'none';
            tempInput.disabled = !isAcOn;
        });

        tempInput.addEventListener('input', () => {
            tempDisplay.textContent = tempInput.value;
            acTempDisplay.textContent = `${tempInput.value}°C`;
        });

        modeSelect.addEventListener('change', () => {
            acModeDisplay.textContent = modeSelect.value.charAt(0).toUpperCase() + modeSelect.value.slice(1);
        });

        // Navigation to Floor Plan
        function goToFloorplan() {
            window.location.href = 'floorplan.php'; // 修改为 PHP 页面
        }
    </script>
</body>
</html>
