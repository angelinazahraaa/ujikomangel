<?php
session_start(); // Memulai sesi
session_destroy(); // Menghancurkan semua data sesi
header("Location: index.php"); // Arahkan ke halaman login
exit(); // Menghentikan eksekusi skrip
?>