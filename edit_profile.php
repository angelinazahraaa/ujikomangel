
<?php
session_start();

// Cek apakah id_user ada dalam session
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit();
}

$userId = $_SESSION['id_user'];
$conn = new mysqli('localhost', 'root', '', 'ujikom');

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pengguna dari database
$userId = $_SESSION['id_user']; // Pastikan Anda menyimpan ID pengguna saat login
$sql = "SELECT * FROM user WHERE id_user = '$userId'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $name = $conn->real_escape_string(trim($_POST['name']));
    $instagram = $conn->real_escape_string(trim($_POST['instagram']));
    
    // Proses upload foto
    $profile_picture = $user['profile']; // Default ke foto lama
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "images/"; // Pastikan folder ini ada dan dapat ditulisi
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validasi jenis file gambar
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowed_types)) {
            // Cek apakah file sudah ada
            if (!file_exists($target_file)) {
                // Upload file
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                    $profile_picture = $target_file;
                } else {
                    echo "<script>alert('Maaf, terjadi kesalahan saat mengunggah foto.');</script>";
                }
            } else {
                echo "<script>alert('Maaf, file sudah ada. Silakan ganti nama file.');</script>";
            }
        } else {
            echo "<script>alert('Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.');</script>";
        }
    }

    // Update data di database
    $sql = "UPDATE user SET nama='$name', instagram='$instagram', profile='$profile_picture' WHERE id_user='$userId'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="instagram" class="form-label">Instagram</label>
            <input type="text" class="form-control" id="instagram" name="instagram" value="<?php echo htmlspecialchars($user['instagram']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="profile_picture" class="form-label">Foto Profil</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture">
            <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>