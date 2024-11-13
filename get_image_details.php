<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'ujikom');

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID gambar dari URL
$imageId = $_GET['imageId'];

// Ambil detail gambar dari database
$sql = "SELECT images.*, user.nama FROM images JOIN user ON images.id_user = user.id_user WHERE images.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $imageId);
$stmt->execute();
$result = $stmt->get_result();
$imageDetails = $result->fetch_assoc();

// Tampilkan detail gambar dalam format JSON
echo json_encode($imageDetails);
?>