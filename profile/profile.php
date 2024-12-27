<?php
// Sambungkan ke database
require '../DB/Dbzahra.php';

// Mulai sesi
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/loginnew.php");
    exit;
}

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Periksa apakah entri profil sudah ada
$query_check = "SELECT COUNT(*) AS count FROM profil_pengguna WHERE user_id = ?";
$stmt_check = $conn->prepare($query_check);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();

// Jika entri profil tidak ada, buat entri dengan gambar profil default
if ($row_check['count'] == 0) {
    $query_insert = "INSERT INTO profil_pengguna (user_id, nama_lengkap, tempat_tinggal, no_hp, foto_profil) 
                     VALUES (?, '', '', '', 'profiledefault.png')";
    $stmt_insert = $conn->prepare($query_insert);
    $stmt_insert->bind_param("i", $user_id);
    $stmt_insert->execute();
}

// Ambil data profil pengguna dari database
$query = "SELECT nama_lengkap, tempat_tinggal, no_hp, foto_profil FROM profil_pengguna WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil input dari form, jika kosong gunakan data yang lama
    $nama_lengkap = !empty($_POST['fullName']) ? htmlspecialchars($_POST['fullName'], ENT_QUOTES) : $user['nama_lengkap'];
    $tempat_tinggal = !empty($_POST['residence']) ? htmlspecialchars($_POST['residence'], ENT_QUOTES) : $user['tempat_tinggal'];
    $no_hp = !empty($_POST['phone']) ? htmlspecialchars($_POST['phone'], ENT_QUOTES) : $user['no_hp'];
    $foto_profil = $user['foto_profil']; // Gunakan gambar lama jika tidak ada gambar baru

    // Proses upload gambar (jika ada gambar baru)
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'imgprofil/';
        $file_name = time() . '_' . basename($_FILES['profileImage']['name']);
        $target_file = $upload_dir . $file_name;

        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['profileImage']['tmp_name']);
        if (in_array($file_type, $allowed_types)) {
            // Pindahkan file ke folder tujuan
            if (move_uploaded_file($_FILES['profileImage']['tmp_name'], $target_file)) {
                $foto_profil = $file_name; // Perbarui gambar profil jika berhasil diupload
            } else {
                echo "<script>alert('Gagal mengunggah gambar.');</script>";
            }
        } else {
            echo "<script>alert('Tipe file tidak valid. Hanya diperbolehkan JPG, PNG, atau GIF.');</script>";
        }
    }

    // Query untuk memperbarui data profil
    $query = "UPDATE profil_pengguna 
              SET nama_lengkap = ?, tempat_tinggal = ?, no_hp = ?, foto_profil = ? 
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $nama_lengkap, $tempat_tinggal, $no_hp, $foto_profil, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!');</script>";
        // Redirect kembali ke halaman sebelumnya setelah memperbarui profil
        $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../Halaman/Final.php';
        header("Location: $redirect_url");
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>edit profile</title>
    <link rel="icon" href="../Foto/Logo.jpg" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="../css/profile.css?v=<?= time(); ?>" rel="stylesheet">
    <script>
        function handleProfileClick(event) {
            // Memeriksa apakah lebar layar kurang dari atau sama dengan 768px (contoh untuk perangkat mobile)
            if (window.innerWidth <= 768) {
                // Cegah dropdown muncul di perangkat mobile
                event.preventDefault();
                event.stopPropagation();
                
                // Redirect langsung ke halaman profil jika perangkat mobile
                window.location.href = "../profile/profile.php"; 
            }
        }
    </script>
</head>
<body>
<div class="profile-container">
    <!-- Tampilkan gambar profil dari database atau gambar default -->
    <img src="imgprofil/<?php echo htmlspecialchars(!empty($user['foto_profil']) ? $user['foto_profil'] : 'imgprofil/profiledefault.png'); ?>" 
         alt="Profile Picture" class="img-thumbnail">
    <p class="text-center">Change Image ↓</p>   
    
    <!-- Formulir untuk memperbarui profil -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="profileImage" class="form-label">Gambar Profil</label>
            <input type="file" class="form-control" name="profileImage" id="profileImage">
        </div>
        
        <div class="mb-3">
            <label for="fullName" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Masukkan nama lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label for="residence" class="form-label">Tempat Tinggal Lengkap</label>
            <input type="text" class="form-control" id="residence" name="residence" placeholder="Masukkan tempat tinggal" value="<?php echo htmlspecialchars($user['tempat_tinggal'] ?? ''); ?>">
        </div>
        
        <div class="mb-3">
            <label for="phone" class="form-label">No HP</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Masukkan nomor HP" value="<?php echo htmlspecialchars($user['no_hp'] ?? ''); ?>">
        </div>
        
        <div class="button-group">
            <button type="submit" class="btn btn-save">Save Profile</button>
            <a href="../Halaman/Final.php" class="btn btn-back">Kembali</a>
        </div>

    </form>
</div>
</body>
</html>
