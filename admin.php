<?php
require_once('database.php');
session_start(); // Memulai session
$_SESSION['status'] = ""; 

if ($_SESSION['status'] == "login") {
    header("location:login.php");
} else {
    if (isset($_POST['masuk'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Cek apakah username dan password valid
        if (cek_login($username, $password)) {
            // Ambil role dari database setelah login berhasil
            $role = get_user_role($username);

            $_SESSION['username'] = $username; // Masukkan session username
            $_SESSION['status'] = "login"; // Masukkan session status login
            $_SESSION['role'] = $role; // Masukkan session role

            // Debugging: Cek session dan role
            // var_dump($_SESSION);

            // Arahkan pengguna sesuai dengan perannya
            if ($_SESSION['role'] == "admin") {
                header("location:work.php"); // Arahkan ke halaman admin
            } else {
                header("location:workuser.php"); // Arahkan ke halaman user biasa
            }
            exit; // Jangan lupa exit setelah header
        } else {
            header("location:login.php?msg=gagal"); // Jika gagal login
            exit;
        }
    }
}
?>
