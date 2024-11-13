<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ujikom');

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cek apakah id_user ada dalam session
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit();
}

$userId = $_SESSION['id_user'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $title = $conn->real_escape_string(trim($_POST['title']));
    $description = $conn->real_escape_string(trim($_POST['description']));

    // Proses upload file
    if (isset($_FILES['fileToUpload']) && $_FILES['fileToUpload']['error'] == 0) {
        $target_dir = "images/"; // Pastikan folder ini ada dan dapat ditulisi
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        
        // Validasi ukuran dan jenis file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                // Simpan informasi foto ke database
                $sql = "INSERT INTO images (id_user, title, description, file_name) VALUES ('$userId', '$title', '$description', '$target_file')";
                if ($conn->query($sql) === TRUE) {
                    echo "<script>alert('Foto berhasil diunggah dan informasi disimpan di database!'); window.location.href='profile.php';</script>";
                } else {
                    echo "<script>alert('Error: " . $conn->error . "');</script>";
                }
            } else {
                echo "<script>alert('Maaf, terjadi kesalahan saat mengunggah file.');</script>";
            }
        } else {
            echo "<script>alert('Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.');</script>";
        }
    } else {
        echo "<script>alert('Tidak ada file yang diunggah.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-pink {
            background-color: #ffcccb;
            color: #000;
            border: none;
        }
        .btn-pink:hover {
            background-color: #ffb3b3;
            color: #fff;
        }
        .table-container {
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Upload Foto</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Foto:</label>
                                <input type="text" class="form-control" name="title" id="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Foto:</label>
                                <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="fileToUpload" class="form-label">Pilih File</label>
                                <input type="file" class="form-control" id="fileToUpload" name="fileToUpload" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                    </div>
                </div>
            </div >
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>