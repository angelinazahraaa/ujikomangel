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

// Ambil gambar dari database
$sql = "SELECT images.*, user.nama, 
            (SELECT COUNT(*) FROM likes WHERE images_id = images.id) AS total_likes 
        FROM images 
        JOIN user ON images.id_user = user.id_user";
$result = $conn->query($sql);

$userId = $_SESSION['id_user'];
$likes = [];
$sql_likes = "SELECT images_id FROM likes WHERE user_id = '$userId'";
$result_likes = $conn->query($sql_likes);
while ($row = $result_likes->fetch_assoc()) {
    $likes[] = $row['images_id'];
}

// Menangani pengiriman komentar atau penghapusan komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['comment'])) {
        // Menangani komentar baru
        $imageId = $_POST['imageId'];
        $comment = $_POST['comment'];

        // Simpan komentar ke database
        $stmt = $conn->prepare("INSERT INTO comment (images_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $imageId, $userId, $comment);
        if ($stmt->execute()) {
            echo "<script>alert('Komentar berhasil ditambah.');</script>";
        } else {
            echo "<script>alert('Gagal mengirim komentar.');</script>";
        }
    } elseif (isset($_POST['delete_comment_id'])) {
        // Menangani penghapusan komentar
        $commentId = $_POST['delete_comment_id'];

        // Cek apakah pengguna adalah pemilik komentar atau pemilik gambar
        $stmt = $conn->prepare("SELECT comment.user_id, images.id_user AS image_owner_id 
                                FROM comment 
                                JOIN images ON comment.images_id = images.id 
                                WHERE comment.id = ?");
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $commentRow = $result->fetch_assoc();

        // Hapus komentar jika pengguna adalah pemilik komentar atau pemilik gambar
        if ($result->num_rows > 0 && ($_SESSION['id_user'] == $commentRow['user_id'] || $_SESSION['id_user'] == $commentRow['image_owner_id'])) {
            $stmt_delete = $conn->prepare("DELETE FROM comment WHERE id = ?");
            $stmt_delete->bind_param("i", $commentId);
            if ($stmt_delete->execute()) {
                echo "<script>alert('Komentar berhasil dihapus.');</script>";
            } else {
                echo "<script>alert('Gagal menghapus komentar.');</script>";
            }
        } else {
            echo "<script>alert('Anda tidak memiliki izin untuk menghapus komentar ini.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Present &mdash; Galeri Foto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300,700,900' rel='stylesheet' type='text/css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .purple-comment-section {
            background-color: #d3c1e5;
            border-radius: 10px;
            padding: 20px;
            max-height: 300px; /* Maksimal tinggi untuk komentar */
            width: 100%;
        }
        .btn-purple {
            background-color: #6f42c1;
            color: white;
            align-items: flex-end;
        }
        
        .icon-love {
            color: #d63384;
            font-size: 1.5rem;
            cursor: pointer;
        }
        #imageDetails {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8); /* Background semi-transparan */
        }
            .modal-content {
            background-color: #fefefe;
            margin: 10% auto; /* 10% dari atas dan otomatis dari kiri dan kanan */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Lebar modal */
            max-width: 1200px; /* Lebar maksimum modal */
            display: flex;
        }
	
		#modal-title {
			text-align: center;
      		font-size: 24px;
      		margin-bottom: 20px;
		}
        .modal-item {
  			display: flex;
  			flex-wrap: wrap;
  			justify-content: space-between;
		}

		#modal-image {
			flex: 1;
  			width: auto;
  			height: 300px;
  			margin-bottom: 10px;
  			margin-right: 20px;
		}

		#modal-description {
  			flex: 1; 
 	 		margin-left: 10px;
  			margin-right: 10px;
  			margin-bottom: 20px;
		}
		.close{
			width: 25px;
			cursor: pointer;
		}
		.purple-comment-section {
  			flex: 1;
            max-height: 200px;
  			margin-left: 10px;
  			margin-right: 10px;
  			margin-bottom: 20px;
		}
        .comment-form {
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Posisikan input komentar dan tombol di sebelah kanan */
            width: 100%;
        }
        textarea {
            width: 100%;
			height: 35px;
            margin-bottom: 5px; /* Jarak bawah textarea */
        }

        .comment-list {
            list-style-type: none; /* Menghilangkan bullet */
            padding: 0; /* Menghilangkan padding */
        }

        .comment-item {
            background-color: #f8f9fa; /* Warna latar belakang untuk komentar */
            border: 1px solid #dee2e6; /* Border untuk komentar */
            border-radius: 5px; /* Sudut melengkung */
            padding: 10px; /* Padding di dalam komentar */
            margin-bottom: 10px; /* Jarak antar komentar */
        }

        .comment-item form {
            display: inline;
        }

        .table-container {
    
}

.table {
    width: 100%;
    border-collapse: collapse;
    max-height: 100px;
    overflow-y: auto;
}

.table th, .table td {
    padding: 3px;
    text-align: left;
}

.table th {
    background-color: #f2f2f2;
    font-weight: bold;
}


.btn-danger {
    margin-left: 10px;
}
    </style>
</head>
<body>

<header id="fh5co-header" role="banner">
    <div class="container text-center">
        <div id="fh5co-logo">
            <a href="workuser.php"><img src="images/lgoi-removebg-preview.png" alt="Present Free HTML 5 Bootstrap Template"></a>
        </div>
        <nav>
            <ul>
                <li class="active"><a href="workuser.php">profil/foto</a></li>
                <li><a href="profile.php">user</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header><script>
    // Menampilkan alert dengan pesan selamat datang
    window.onload = function() {
        // Mengambil nama pengguna dari PHP dan memastikan bahwa karakter khusus sudah di-escape
        var userName = <?php echo json_encode(htmlspecialchars($user['nama'])); ?>; 
        alert("Selamat Datang, " + userName);
    };
</script>

<div class="container-fluid pt70 pb70">
    <div id="fh5co-projects-feed" class="fh5co-projects-feed clearfix masonry">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="fh5co-project masonry-brick">
                <a href="#" class="image-link" data-id="<?php echo $row['id']; ?>" data-title="<?php echo $row['title']; ?>" data-file="<?php echo $row['file_name']; ?>" data-description="<?php echo $row['description']; ?>" data-username="<?php echo $row['nama']; ?>">
                    <img src="<?php echo $row['file_name']; ?>" alt="<?php echo $row['title']; ?>" width="100%">
                    <h2><?php echo $row['title']; ?></h2>
                    <i class="bi bi-heart icon-love <?php echo in_array($row['id'], $likes) ? 'liked' : ''; ?>" style="font-size: 2rem; margin-right: 10px; cursor: pointer;" data-image-id="<?php echo $row['id']; ?>"></i>
                    <span class="like-count">(<?php echo $row['total_likes']; ?>)</span> <!-- Menampilkan total likes -->
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div id="imageDetails" class="modal">
    <div class="modal-content">
        <span class="close"><img src="css/cross.png" class="close"></span>
        <h2 id="modal-title"></h2>
        <div class="modal-item">
            <img src="" alt="" id="modal-image">
            <p id="modal-description"></p>
            <div class="purple-comment-section">
                <h3>Komentar</h3>
                <div class="comment-form">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <input type="hidden" name="imageId" id="imageId" value="">
                        <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
                        <button type="submit" class="btn btn-purple">Kirim</button>
                    </form>
                    <br><br>
                    <table class="table">
                        <thead>
                            
                        </thead>
                        <tbody id="comment-list">
                            <?php
                            $imageId = isset($_GET['imageId']) ? $_GET['imageId'] : 0;
                            $stmt_comments = $conn->prepare("SELECT comment.id, comment.comment, user.nama, comment.user_id FROM comment JOIN user ON comment.user_id = user.id_user WHERE comment.images_id = ?");
                            $stmt_comments->bind_param("i", $imageId);
                            $stmt_comments->execute();
                            $result_comments = $stmt_comments->get_result();

                            while ($data = $result_comments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($data['comment']); ?></td>
                                    <td>
                                        <?php if ($data['user_id'] == $userId): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="delete_comment_id" value="<?php echo $data['id']; ?>">
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin menghapus komentar ini?')">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                        </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.js"></script>
<script>
    function showImageDetails(imageId, title, file, description) {
        document.getElementById('modal-image').src = file;
        document.getElementById('modal-title').innerHTML = title;
        document.getElementById('modal-description').innerHTML = description;
        document.getElementById('imageId').value = imageId;
        document.getElementById('imageDetails').style.display = 'block';

        // Ambil komentar dari database
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_comments.php?imageId=' + imageId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                var commentList = document.getElementById('comment-list');
                commentList.innerHTML = '';

                response.forEach(function(comment) {
                    var commentElement = document.createElement('tr');
                    commentElement.innerHTML = "<td>" + comment.username + "</td><td>" + comment.comment + "</td>";
                    commentList.appendChild(commentElement);
                });
            }
        };
        xhr.send();
    }

    // Menambahkan event listener untuk tombol close
    document.addEventListener('DOMContentLoaded', function() {
        var closeButton = document.querySelector('.close');
        closeButton.addEventListener('click', function() {
            document.getElementById('imageDetails').style.display = 'none';
        });

        var imageLinks = document.querySelectorAll('.image-link');
        imageLinks.forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var imageId = link.getAttribute('data-id');
                var title = link.getAttribute('data-title');
                var file = link.getAttribute('data-file');
                var description = link.getAttribute('data-description');
                showImageDetails(imageId, title, file, description);
            });
        });
    });
</script>
</body>
</html>