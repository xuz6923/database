<?php
session_start();

// 检查用户是否已登录
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// 检查是否通过 GET 参数传递了 room_id
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // 根据 room_id 跳转到不同的房间页面
    switch ($room_id) {
        case 1:
            header('Location: bedroom.php?room_id=1');
            break;
        case 3:
            header('Location: kitchen.php?room_id=3');
            break;
        case 4:
            header('Location: living_room.php?room_id=4');
            break;
        case 5:
            header('Location: bathroom.php?room_id=5');
            break;
        default:
            echo "Invalid room ID.";
            exit;
    }
} else {
    echo "Room ID not provided.";
    exit;
}
?>
