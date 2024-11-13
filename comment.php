<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in.']);
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'ujikom');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && isset($_POST['imageId'])) {
    $imageId = $_POST['imageId'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['id_user'];

    $stmt = $conn->prepare("INSERT INTO comments (image_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $imageId, $userId, $comment);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>