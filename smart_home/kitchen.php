<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "Session not set! Redirecting to login page.";
    header("Location: login.php");
    exit();
} else {
    echo "Session is set for user: " . $_SESSION['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Controls</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Kitchen Control</h1>
    </header>

    <div class="container">
        <!-- Kitchen Light Control -->
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

        // Toggle Light Button Logic
        document.getElementById('toggle-light').addEventListener('click', () => {
            isLightOn = !isLightOn;
            lightStatus.textContent = isLightOn ? 'On' : 'Off';
            lightBulb.classList.toggle('off', !isLightOn);
            [brightnessInput, redInput, greenInput, blueInput].forEach(input => {
                input.disabled = !isLightOn;
            });
        });

        // Update Light Color and Brightness
        function updateLightColor() {
            const r = redInput.value, g = greenInput.value, b = blueInput.value;
            const brightness = brightnessInput.value / 100;
            lightBulb.style.backgroundColor = `rgba(${r}, ${g}, ${b}, ${brightness})`;
            lightBulb.style.boxShadow = `0 0 ${brightness * 20}px rgba(${r}, ${g}, ${b}, ${brightness})`;
        }

        // Attach Event Listeners to RGB and Brightness Inputs
        [brightnessInput, redInput, greenInput, blueInput].forEach(input => {
            input.addEventListener('input', updateLightColor);
        });

        // Navigate Back to Floor Plan
        function goToFloorplan() {
            window.location.href = 'floorplan.php';  // 修改为 PHP 页面
        }
    </script>
</body>
</html>
