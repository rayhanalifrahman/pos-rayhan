<?php
include 'koneksi.php';  // Pastikan koneksi.php sudah benar dan terhubung

// Mendeklarasikan tanggal dan bulan
$tanggal = $_POST['tanggal'] ?? '';
$bulan = $_POST['bulan'] ?? '';

// Query dasar untuk laporan
$query = "SELECT p.tanggal_penjualan, p.id_penjualan, pr.nama_produk, dp.jumlah_produk, dp.subtotal
          FROM detail_penjualan dp
          JOIN penjualan p ON dp.id_penjualan = p.id_penjualan
          JOIN produk pr ON dp.id_produk = pr.id_produk";

// Filter untuk tanggal dan bulan
$filters = [];

if ($tanggal) {
    // Pastikan format tanggal yang dimasukkan benar sesuai database (yyyy-mm-dd)
    $filters[] = "DATE(p.tanggal_penjualan) = '" . mysqli_real_escape_string($koneksi, $tanggal) . "'";
}

if ($bulan) {
    // Pastikan bulan adalah angka yang valid
    $filters[] = "MONTH(p.tanggal_penjualan) = " . (int)$bulan;
}

// Jika ada filter, tambahkan ke query
if ($filters) {
    $query .= " WHERE " . implode(" AND ", $filters);
}

// Menjalankan query
$result = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <link rel="stylesheet" href="laporan.css">
</head>

<body>
    <h2>Laporan Penjualan</h2>
    <!-- Form untuk memilih tanggal atau bulan -->
    <form method="POST" class="no-print">
        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
        <select name="bulan">
            <option value="">Pilih Bulan</option>
            <?php foreach (['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $i => $name): ?>
                <option value="<?= $i + 1 ?>" <?= ($bulan == $i + 1) ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Terapkan</button>
    </form>
    <br><br>
    <!-- Tombol untuk kembali ke dashboard, mengeprint dan ekspor ke excel -->
    <div class="actions">
        <a href="dashboard.php">Kembali</a>
        <button onclick="window.print()">Print</button>
        <button onclick="exportToExcel()">Excel</button>
    </div>
    <br><br>
    <!-- Tabel untuk menampilkan laporan penjualan -->
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID Penjualan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $r['tanggal_penjualan'] ?></td>
                        <td><?= $r['id_penjualan'] ?></td>
                        <td><?= $r['nama_produk'] ?></td>
                        <td><?= $r['jumlah_produk'] ?></td>
                        <td>Rp <?= number_format($r['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">Data Tidak Ada</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Fungsi untuk mengekspor ke excel
        function exportToExcel() {
            const table = document.querySelector("table").outerHTML;
            const link = document.createElement("a");
            link.href = URL.createObjectURL(new Blob([`<head><meta charset="UTF-8">${table}</head>`], {type: `application/vnd.ms-excel`})); 
            link.download = "Laporan_Danantara.xls";
            link.click();
        }
    </script>
</body>

</html>
