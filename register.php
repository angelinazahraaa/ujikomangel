<?php
session_start();
$registration_success = false; // Variabel untuk menandai keberhasilan registrasi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form registrasi
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Koneksi ke database
    $conn = new mysqli('localhost', 'root', '', 'ujikom');

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Validasi password
    if ($password !== $confirm_password) {
        echo "<script>alert('Password tidak cocok.');</script>";
    } else {
        // Simpan data ke database
        $sql = "INSERT INTO user (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            $registration_success = true; // Tandai bahwa registrasi berhasil
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Fungsi untuk mengarahkan ke halaman login setelah alert
        function redirectToLogin() {
            window.location.href = "index.php";
        }
    </script>
</head>
<body>
<div class="container">
    <div id="registerbox" class="mainbox col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title text-center">REGISTER </div>
            </div>
            <div class="panel-body">
                <form action="" method="POST" class="form-horizontal">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input id="username" type="text" class="form-control" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input id="confirm_password" type="password" class="form-control" name="confirm_password" placeholder="Konfirmasi Password" required>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 controls">
                            <button type="submit" name="register" class="btn btn-primary pull-right">Daftar</button>
                        </div>
                    </div>
                </form>
                <?php if ($registration_success): ?>
                    <script>
                        alert("Registrasi berhasil!");
                        redirectToLogin(); 
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>