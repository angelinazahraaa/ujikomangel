<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'galerry_foto');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$imageId = $_POST['imageId'];

function getLikeCount($imageId, $conn) {
    $sql = "SELECT COUNT(*) as likes FROM likes WHERE images_id = '$imageId'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['like_count'];
}

$likeCount = getLikeCount($imageId, $conn);
echo json_encode(['likeCount' => $likeCount]);
?>