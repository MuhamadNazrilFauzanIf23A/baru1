<?php 
// Koneksi ke database
require '../DB/Dbzahra.php';

// Ambil data pemesanan yang belum disetujui, ditolak, atau sedang berlangsung
$sqlPemesanan = "
    SELECT 
        p.*, 
        m.nama AS nama_mobil, 
        m.gambar AS gambar_mobil, 
        pp.nama_lengkap, 
        pp.tempat_tinggal, 
        pp.no_hp, 
        p.waktu_pengambilan
    FROM 
        pemesanan p
    JOIN 
        list_mobil m ON p.id_mobil = m.id
    JOIN 
        profil_pengguna pp ON p.user_id = pp.user_id
    WHERE 
        p.status IN ('pending', 'disetujui', 'ditolak')
    ORDER BY p.id DESC";



$resultPemesanan = $conn->query($sqlPemesanan);

$pemesananList = [];
if ($resultPemesanan && $resultPemesanan->num_rows > 0) {
    while ($row = $resultPemesanan->fetch_assoc()) {
        $pemesananList[] = $row;
    }
}

// Jika admin menyetujui atau menolak pemesanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pemesananId = $_POST['pemesanan_id'];
    $aksi = $_POST['aksi'];

    if ($aksi === 'setujui') {
        $sqlUpdate = "UPDATE pemesanan SET status = 'disetujui' WHERE id = $pemesananId";
    } elseif ($aksi === 'tolak') {
        $sqlUpdate = "UPDATE pemesanan SET status = 'ditolak' WHERE id = $pemesananId";
    }

    if (isset($sqlUpdate) && $conn->query($sqlUpdate)) {
        header('Location: Databoking.php'); // Refresh halaman
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="icon" href="../Foto/Logo.jpg" />
    <link rel="stylesheet" href="../CSS/bootstrap.min.css">
    <link href="../css/databoking.css?v=<?= time(); ?>" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Title with a link to the admin page -->
        <div class="row">
            <div class="col-12 text-center">
                <a href="admin.php" style="text-decoration: none;">
                    <h1 class="mb-4">Dashboard Admin</h1>
                </a>
            </div>
        </div>

        <?php if (!empty($pemesananList)): ?>
            <!-- Table wrapped with a responsive div for mobile -->
            <div class="table-responsive">
                <table class="table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Tempat Tinggal</th>
                        <th>No HP</th>
                        <th>Nama Mobil</th>
                        <th>Harga</th>
                        <th>Masa Sewa (Hari)</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Paket Sewa</th>
                        <th>Waktu Pengambilan</th>
                        <th>Sopir</th>
                        <th>Gambar Mobil</th>
                        <th>Status</th>
                        <th>Bukti Transfer</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
    <?php foreach ($pemesananList as $pemesanan): ?>
        <tr>
            <td><?= htmlspecialchars($pemesanan['nama_lengkap'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($pemesanan['tempat_tinggal'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($pemesanan['no_hp'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($pemesanan['nama_mobil'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>Rp <?= number_format($pemesanan['harga'], 0, ',', '.'); ?></td>
            <td><?= htmlspecialchars($pemesanan['masa_sewa'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($pemesanan['tanggal_mulai'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($pemesanan['tanggal_selesai'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?= htmlspecialchars($pemesanan['paket_sewa'], ENT_QUOTES, 'UTF-8'); ?> Jam</td>
            <td><?= date('h:i A', strtotime($pemesanan['waktu_pengambilan'])); ?></td> <!-- Format waktu -->
            <td><?= ($pemesanan['sopir'] == 'iya') ? 'Ya' : 'Tidak'; ?></td>
            <td>
                <img src="../Foto/<?= htmlspecialchars($pemesanan['gambar_mobil'], ENT_QUOTES, 'UTF-8'); ?>" 
                alt="<?= htmlspecialchars($pemesanan['nama_mobil'], ENT_QUOTES, 'UTF-8'); ?>" 
                class="img-fluid">
            </td>
            <td>
                <?php 
                if ($pemesanan['status'] === 'ditolak') {
                    echo "<span style='color: red;'>Ditolak oleh Admin</span>";
                } elseif ($pemesanan['status'] === 'disetujui') {
                    echo "<span style='color: green;'>Disetujui oleh Admin</span>";
                } else {
                    echo "<span style='color: orange;'>Pending (Belum Diterima)</span>";
                }
                ?>
            </td>
            <td>
                <?php if (!empty($pemesanan['file_unggahan'])): ?>
                    <a href="../Booking/uploads/<?= htmlspecialchars($pemesanan['file_unggahan'], ENT_QUOTES, 'UTF-8'); ?>" 
                    download>
                        Unduh Bukti
                    </a>
                <?php else: ?>
                    Tidak ada file
                <?php endif; ?>
            </td>

            <td>
                <form method="POST">
                    <input type="hidden" name="pemesanan_id" value="<?= $pemesanan['id']; ?>">
                    <?php if ($pemesanan['status'] === 'pending'): ?>
                        <button type="submit" name="aksi" value="setujui" class="btn btn-success btn-lg">Setujui</button>
                        <button type="submit" name="aksi" value="tolak" class="btn btn-danger btn-lg">Tolak</button>
                    <?php elseif ($pemesanan['status'] === 'disetujui'): ?>
                        <button type="button" class="btn btn-secondary btn-lg" disabled>Disetujui oleh Admin</button>
                    <?php elseif ($pemesanan['status'] === 'ditolak'): ?>
                        <button type="button" class="btn btn-secondary btn-lg" disabled>Ditolak oleh Admin</button>
                    <?php endif; ?>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

                </table>
            </div>
        <?php else: ?>
            <p class="text-center">Tidak ada pemesanan untuk ditampilkan.</p>
        <?php endif; ?>
    </div>

    <script src="../JS/bootstrap.bundle.min.js"></script>
</body>
</html>
