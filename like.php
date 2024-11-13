<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ujikom');

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['imageId']) && isset($_POST['userId'])) {
    $imageId = $_POST['imageId'];
    $userId = $_POST['userId'];

    // Cek apakah pengguna sudah menyukai gambar
    $sql_check_like = "SELECT * FROM likes WHERE user_id = '$userId' AND images_id = '$imageId'";
    $result_check_like = $conn->query($sql_check_like);

    if ($result_check_like->num_rows > 0) {
        echo "Anda sudah menyukai gambar ini.";
    } else {
        // Simpan like ke database
        $sql_like = "INSERT INTO likes (user_id, images_id) VALUES ('$userId', '$imageId')";
        $conn->query($sql_like);
        echo "Berhasil menyukai gambar.";
    }
}
?>