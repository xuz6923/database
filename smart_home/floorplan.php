<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home - Room Selection</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="floorplan-page">
    <header>
        <h1>Select a Room to Control</h1>
        <!-- Logout Button -->
        <button class="logout-btn" onclick="logout()">Logout</button>
    </header>

    <div class="floorplan-container">
        <!-- Reordered Room Options -->
        <div class="room" id="bedroom" onclick="goToRoom('bedroom')">
            <span>Bedroom</span>
        </div>
        <div class="room" id="living-room" onclick="goToRoom('living-room')">
            <span>Living Room</span>
        </div>
        <div class="room" id="kitchen" onclick="goToRoom('kitchen')">
            <span>Kitchen</span>
        </div>
        <div class="room" id="bathroom" onclick="goToRoom('bathroom')">
            <span>Bathroom</span>
        </div>
    </div>

    <script>
        function goToRoom(room) {
            window.location.href = room + '.php'; // Redirect to room-specific PHP page
        }

        function logout() {
            // Redirect to the PHP logout script
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>
