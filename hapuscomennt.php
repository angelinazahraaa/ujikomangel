<?php
// Menampilkan komentar dengan tombol hapus untuk semua pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment_id'])) {
    $commentId = $_POST['delete_comment_id'];
    $stmt = $conn->prepare("DELETE FROM comment WHERE id = ?");
    $stmt->bind_param("i", $commentId);
    if ($stmt->execute()) {
        echo "Komentar berhasil dihapus.";
        // Redirect untuk memuat ulang halaman
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Gagal menghapus komentar.";
    }
}

// Ambil komentar berdasarkan imageId yang sedang ditampilkan
if (isset($_GET['imageId'])) {
    $imageId = $_GET['imageId'];
    $sql_comments = "SELECT comment.id, comment.comment, user.nama FROM comment JOIN user ON comment.user_id = user.id_user WHERE comment.images_id = ?";
    $stmt_comments = $conn->prepare($sql_comments);
    $stmt_comments->bind_param("i", $imageId);
    $stmt_comments->execute();
    $result_comments = $stmt_comments->get_result();

   
}
?>