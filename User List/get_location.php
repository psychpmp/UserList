<?php
include 'db_connection.php';

// Validate input
$id = intval($_GET['id']);

// Fetch user location
$sql = "SELECT last_latitude, last_longitude FROM users WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'latitude' => $user['last_latitude'],
        'longitude' => $user['last_longitude']
    ]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>