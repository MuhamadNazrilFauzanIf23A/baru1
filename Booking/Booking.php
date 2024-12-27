<?php
session_start();

// Memuat kelas dan inisialisasi
require '../DB/zahra.php';
require '../class/users.php';
require '../class/pemesanan.php';

$db = new Database();
$conn = $db->connect();

$user = new User($conn);
$rentalMobil = new RentalMobil($conn);

// Validasi login
if (!$user->isLoggedIn()) {
    header("Location: ../Login/loginnew.php");
    exit;
}

// Ambil data pengguna
$userId = $_SESSION['user_id'];

// Ambil data mobil berdasarkan ID
$idMobil = $_GET['id'] ?? 1;
$mobil = $rentalMobil->getMobilById($idMobil);
if (!$mobil) {
    die("Data mobil tidak ditemukan.");
}

$harga6jam = $mobil['harga_per6jam'];
$harga12jam = $mobil['harga_per12jam'];
$harga24jam = $mobil['harga_per24jam'];
$isPremium = $mobil['is_premium'];

// Ambil stok mobil
$stok = $rentalMobil->getStokByMobilId($idMobil);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggalMulai = $_POST['tanggalMulai'];
    $masaSewa = (int)$_POST['masaSewa'];
    $paketSewa = $_POST['paketSewa'];
    $sopir = $_POST['sopir'];
    $waktuPengambilan = $_POST['waktuPengambilan'];

    // Validasi mobil premium
    if ($isPremium === 'ya' && $sopir === 'tidak') {
        die("Mobil premium harus menggunakan sopir.");
    }

    // Hitung harga
    $hargaPerPaket = 0;
    if ($paketSewa == '6') {
        $hargaPerPaket = $harga6jam;
    } elseif ($paketSewa == '12') {
        $hargaPerPaket = $harga12jam;
    } elseif ($paketSewa == '24') {
        $hargaPerPaket = $harga24jam;
    }

    // Hitung biaya sopir jika dipilih
    $hargaSopir = 0;
    if ($sopir === 'iya') {
        if ($paketSewa == '6') {
            $hargaSopir = 100000 * $masaSewa;
        } elseif ($paketSewa == '12') {
            $hargaSopir = 150000 * $masaSewa;
        } elseif ($paketSewa == '24') {
            $hargaSopir = 250000 * $masaSewa;
        }
    }

    $totalHarga = ($hargaPerPaket * $masaSewa) + $hargaSopir;

    // Proses unggahan file bukti transfer
    $fileName = '';
    if (isset($_FILES['bukti'])) {
        $uploadDir = 'uploads/';
        $fileTmpName = $_FILES['bukti']['tmp_name'];
        $fileName = basename($_FILES['bukti']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpName, $uploadFilePath)) {
            // File berhasil diupload
        } else {
            echo "Gagal mengunggah bukti transfer.";
            exit;
        }
    } else {
        echo "Bukti transfer belum diunggah.";
        exit;
    }

// simpan pemesanan
$rentalMobil->simpanPemesanan(
    $userId,
    $idMobil,
    $tanggalMulai,
    date('Y-m-d', strtotime("+$masaSewa days", strtotime($tanggalMulai))),
    $masaSewa,
    $paketSewa,
    $totalHarga,
    $sopir,
    $waktuPengambilan,
    $fileName,
    'pending' // Status default
);

// Update stok
$rentalMobil->updateStokMobil($idMobil, $stok - 1);


    header("Location: ../Halaman/Final.php");
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Booking - Zahrarental</title>
    <link rel="icon" href="../Foto/Logo.jpg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/booking.css?v=<?= time(); ?>" rel="stylesheet">
<script>
    // Fungsi untuk memperbarui total harga
    function updateTotalHarga() {
        const paketSewa = document.getElementById('paketSewa').value;
        const masaSewa = document.getElementById('masaSewa').value;
        const sopir = document.getElementById('sopir').value;
        let hargaPerPaket = 0;
        let hargaSopir = 0;

        // Pilih harga per paket berdasarkan pilihan pengguna
        if (paketSewa === '6') {    
            hargaPerPaket = <?= $harga6jam; ?>;
        } else if (paketSewa === '12') {
            hargaPerPaket = <?= $harga12jam; ?>;
        } else if (paketSewa === '24') {
            hargaPerPaket = <?= $harga24jam; ?>;
        }

        // Tentukan harga sopir per paket berdasarkan pilihan pengguna
        if (sopir === 'iya') {
            let hargaSopirPerPaket = 100000; // Harga sopir untuk 6 jam
            if (paketSewa === '12') {
                hargaSopir = 150000 * masaSewa;  // Harga sopir untuk 12 jam
            } else if (paketSewa === '6') {
                hargaSopir = 100000 * masaSewa; // Untuk 6 jam
            } else if (paketSewa === '24') {
                hargaSopir = 250000 * masaSewa;  // Untuk 24 jam
            }
        }

        // Hitung total harga
        if (hargaPerPaket > 0 && masaSewa > 0) {
            const totalHarga = (hargaPerPaket * masaSewa) + hargaSopir;
            document.getElementById('totalHarga').innerText = 'Rp ' + totalHarga.toLocaleString();
        } else {
            document.getElementById('totalHarga').innerText = 'Rp 0';
        }
    }

    function updateTanggalSelesai() {
    const tanggalMulaiInput = document.getElementById('tanggalMulai');
    const masaSewaInput = document.getElementById('masaSewa');
    const tanggalSelesaiInput = document.getElementById('tanggalSelesai');

    const tanggalMulai = new Date(tanggalMulaiInput.value);
    const masaSewa = parseInt(masaSewaInput.value);

    if (tanggalMulai && masaSewa) {
        // Hitung tanggal selesai berdasarkan masa sewa
        const tanggalSelesai = new Date(tanggalMulai);
        tanggalSelesai.setDate(tanggalSelesai.getDate() + masaSewa);

        // Format tanggal selesai menjadi string (YYYY-MM-DD)
        const formattedTanggalSelesai = tanggalSelesai.toISOString().split('T')[0];
        tanggalSelesaiInput.value = formattedTanggalSelesai;
    }
}
    // sidebar
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
<body>

<?php
// navbar
include 'navbar.php';

// formulir
include 'formpembayaran.php';
?>
    <script src="payment.js?v=<?= time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>