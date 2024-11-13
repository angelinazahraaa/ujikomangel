<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ujikom');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['imageId'])) {
    $imageId = $_GET['imageId'];
    $sql_comments = "SELECT comment.comment, user.nama AS username FROM comment JOIN user ON comment.user_id = user.id_user WHERE comment.images_id = ?";
    $stmt_comments = $conn->prepare($sql_comments);
    $stmt_comments->bind_param("i", $imageId);
    $stmt_comments->execute();
    $result_comments = $stmt_comments->get_result();

    $comments = [];
    while ($comment_row = $result_comments->fetch_assoc()) {
        $comments[] = $comment_row;
    }

    echo json_encode($comments);
}
?>