<?php
session_start();
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

if (!$user) {
    echo "User  not found!";
    exit();
}

// Hitung jumlah pengikut berdasarkan jumlah like
$sql_likes = "SELECT COUNT(*) as total_likes FROM likes WHERE user_id = '$userId'";
$result_likes = $conn->query($sql_likes);
$likes_data = $result_likes->fetch_assoc();
$total_likes = $likes_data['total_likes'];


// Ambil riwayat postingan foto dari tabel images
$sql_images = "SELECT * FROM images";
$result_images = $conn->query($sql_images);

if (isset($_POST['delete_image'])) {
    $image_id = $_POST['image_id'];

    // Hapus komentar dan like terkait
    $conn->query("DELETE FROM comment WHERE images_id = '$image_id'");
    $conn->query("DELETE FROM likes WHERE images_id = '$image_id'");

    // Hapus gambar
    $conn->query("DELETE FROM images WHERE id = '$image_id'");

    // Redirect untuk menghindari pengiriman ulang formulir
    header("Location: post.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <style>
        /* Gaya tombol */
        .btn-pink {
            background-color: #d63384; /* Warna pink */
            color: white; /* Warna teks putih */
            border: none; /* Tanpa border */
            border-radius: 20px; /* Sudut membulat */
            padding: 10px 20px; /* Padding tombol */
            font-size: 16px; /* Ukuran font */
            text-transform: uppercase; /* Teks huruf kapital */
            margin: 5px; /* Jarak antar tombol */
            cursor: pointer; /* Menunjukkan bahwa tombol dapat diklik */
            transition: background-color 0.3s; /* Efek transisi warna */
        }

        .btn-pink:hover {
            background-color: #c81e6f; /* Warna saat hover */
        }

        /* Gaya kolom pink muda bulat */
        .circle-column {
            background-color: #f0c0d6; /* Warna pink muda */
            width: 80px; /* Lebar kolom */
            height: 80px; /* Tinggi kolom */
            border-radius: 50%; /* Membuat kolom bulat */
            display: flex; /* Menggunakan flexbox untuk pusatkan isi */
            align-items: center; /* Pusatkan secara vertikal */
            justify-content: center; /* Pusatkan secara horizontal */
            position: fixed; /* Mengubah menjadi posisi tetap */
            bottom: 20px; /* Jarak dari bawah */
            right: 20px; /* Jarak dari kanan */
        }

        /* Gaya ikon plus */
        .icon-plus {
            color: #d63384; /* Warna ikon */
            font-size: 30px; /* Ukuran ikon */
        }
        .menubar {
            justify-content: center; /* Pusatkan secara horizontal */
        }
        .image-gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .image-gallery img {
            width: 250px; /* Atur lebar gambar */
            height: 250px; /* Atur tinggi gambar */
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */
            margin: 5px; /* Jarak antar gambar */
            border-radius: 10px; /* Sudut membulat */
        }
    </style>
</head>
<body>
<br><br>
<header id="fh5co-header">
    <div class="container text-center">
    <nav>
            <ul>
                <li><a href="work.php">profil/foto</a></li>
                <li class="active"><a href="post.php">Kelola Postingan</a></li>
                <li><a href="profile_admin.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="d-flex justify-content-center" style="flex-direction: column; align-items: center; position: relative;">
<div class="image-gallery">
        <?php while ($image = $result_images->fetch_assoc()) { ?>
            <div style="position: relative;">
                <img src="<?php echo $image['file_name']; ?>" alt="<?php echo $image['title']; ?>">
                <div style="position: absolute; bottom: 10px; left: 10px;">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
                        <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                        <button type="submit" name="delete_image" class="btn btn-pink">Hapus</button>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
    <br><br><br><br><br><br>
    
</div>
</body>
</html>