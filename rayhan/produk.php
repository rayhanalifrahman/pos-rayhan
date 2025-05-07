<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_produk'];
    $nama = $_POST['nama_produk'];
    $harga = $_POST['harga'];       
    $stok = $_POST['stok'];

    // Query untuk mengupdate dan menginsert data produk  
    $query = $id
        ? "UPDATE produk SET nama_produk='$nama', harga='$harga', stok='$stok' WHERE id_produk=$id"
        : "INSERT INTO produk (nama_produk, harga, stok) VALUES ('$nama', '$harga', '$stok')";
    $koneksi->query($query);
    header('Location: produk.php');
    exit;
}

// Kode untuk menghapus data  
if (isset($_GET['hapus'])) {
    $koneksi->query("DELETE FROM produk WHERE id_produk=" . $_GET['hapus']);
    header('Location: produk.php');
    exit;
}
$edit = isset($_GET['edit']) ? $koneksi->query("SELECT * FROM produk WHERE id_produk=" . $_GET['edit'])->fetch_assoc() : null;
$produk = $koneksi->query("SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang</title>
    <link rel="stylesheet" href="produk.css">
</head>

<body>
    <h2><?= $edit ? 'Edit Barang' : 'Tambah Barang' ?></h2>

    <!-- Untuk menginput, mengedit barang -->
    <form method="POST">
        <input type="hidden" name="id_produk" value="<?= $edit['id_produk'] ?? '' ?>">
        <input name="nama_produk" value="<?= $edit['nama_produk'] ?? '' ?>" placeholder="Nama Produk" required><br>
        <input type="number" name="harga" value="<?= $edit['harga'] ?? '' ?>" placeholder="Harga" required><br>
        <input type="number" name="stok" value="<?= $edit['stok'] ?? '' ?>" placeholder="Stok" required><br>
        <button type="submit"><?= $edit ? 'Edit' : 'Tambah' ?></button> 
        <br>
        <?php if ($edit): ?><a href="produk.php" class="back2">Kembali</a><?php endif; ?>
    </form>
    <br>
    <h2>Data Barang</h2>
    <a href="dashboard.php" class="back">Kembali</a>
    <!-- Menampilkan daftar produk dalam tabel -->
    <table>
        <thead>
            <tr>
                <th>ID produk</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <!-- Menampilkan data produk dari database -->
            <?php while ($p = mysqli_fetch_assoc($produk)): ?>
                <tr>
                    <td><?= $p['id_produk'] ?></td>
                    <td><?= $p['nama_produk'] ?></td>
                    <td>Rp<?= number_format($p['harga'], 0, ',', '.') ?></td>
                    <td><?= $p['stok'] ?></td>
                    <td>
                        <a href="?edit=<?= $p['id_produk'] ?>">Edit</a>
                        <a href="?hapus=<?= $p['id_produk'] ?>" onclick="return confirm('Yakin Hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <br>
</body>

</html>