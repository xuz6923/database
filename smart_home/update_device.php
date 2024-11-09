<?php
// 连接数据库
$conn = new mysqli('localhost', 'root', '', 'smart_home');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 获取 POST 的 JSON 数据
$data = json_decode(file_get_contents("php://input"), true);

// 获取设备类型
$device_type = $data['device_type'];

if ($device_type === 'light') {
    $brightness = $data['brightness'];
    $red = $data['red'];
    $green = $data['green'];
    $blue = $data['blue'];
    $status = isset($data['status']) ? $data['status'] : null;

    // 更新设备表中的灯光数据
    $stmt = $conn->prepare("UPDATE devices SET brightness = ?, red = ?, green = ?, blue = ?, status = ?, updated_at = NOW() WHERE device_type = 'light'");
    $stmt->bind_param("iiiii", $brightness, $red, $green, $blue, $status);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['message' => 'Light settings updated successfully']);
} elseif ($device_type === 'curtain') {
    $position = $data['position'];
    $side = $data['side'];

    // 更新设备表中的窗帘数据（根据左右窗帘来更新）
    $stmt = $conn->prepare("UPDATE devices SET position = ?, updated_at = NOW() WHERE device_type = 'curtain' AND side = ?");
    $stmt->bind_param("is", $position, $side);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['message' => 'Curtain settings updated successfully']);
} elseif ($device_type === 'ac') {
    $temperature = isset($data['temperature']) ? $data['temperature'] : null;
    $mode = isset($data['mode']) ? $data['mode'] : null;
    $status = isset($data['status']) ? $data['status'] : null;

    // 更新设备表中的空调数据
    $stmt = $conn->prepare("UPDATE devices SET temperature = ?, mode = ?, status = ?, updated_at = NOW() WHERE device_type = 'ac'");
    $stmt->bind_param("iss", $temperature, $mode, $status);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['message' => 'AC settings updated successfully']);
}

$conn->close();
?>
