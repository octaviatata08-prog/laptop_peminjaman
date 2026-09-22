<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];

/* TAMBAH BARANG */
if (isset($_POST['tambah'])) {

    $kode = $_POST['kode_barang'];
    $nama = $_POST['nama_barang'];
    $jenis = $_POST['jenis_barang'];
    $jumlah = $_POST['jumlah'];
    $kondisi = $_POST['kondisi'];

    $status = $jumlah > 0 ? 'Tersedia' : 'Habis';

    mysqli_query(
        $koneksi,
        "INSERT INTO barang
        (kode_barang, nama_barang, jenis_barang, jumlah, kondisi, status)
        VALUES
        ('$kode','$nama','$jenis','$jumlah','$kondisi','$status')"
    );

    header("Location: barang.php");
    exit;
}

/* HAPUS BARANG */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query(
        $koneksi,
        "DELETE FROM barang WHERE id_barang='$id'"
    );

    header("Location: barang.php");
    exit;
}

$data_barang = mysqli_query(
    $koneksi,
    "SELECT * FROM barang
     ORDER BY id_barang DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Data Barang</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="navbar">

    <b>💻 LAPTOP PEMINJAMAN</b>

    <div>

        <a href="index.php">Dashboard</a>

        <a href="barang.php">Barang</a>

        <?php if ($role == 'Admin') { ?>
            <a href="siswa.php">Siswa</a>
        <?php } ?>

        <a href="peminjaman.php">Peminjaman</a>

        <a href="pengembalian.php">Pengembalian</a>

        <?php if ($role != 'Siswa') { ?>
            <a href="laporan.php">Laporan</a>
        <?php } ?>

        <a href="logout.php">Logout</a>

    </div>

</div>


<div class="container">

<div class="welcome">

<h2>📦 Data Barang</h2>

<p>
Kelola barang yang tersedia di laboratorium.
</p>

</div>


<?php if ($role == 'Admin' || $role == 'Petugas Lab') { ?>

<div class="card">

<h3>➕ Tambah Barang</h3>

<form method="POST">

<label>Kode Barang</label>

<input
type="text"
name="kode_barang"
placeholder="Contoh: LPT001"
required
>


<label>Nama Barang</label>

<input
type="text"
name="nama_barang"
placeholder="Contoh: Laptop Lenovo"
required
>


<label>Jenis Barang</label>

<input
type="text"
name="jenis_barang"
placeholder="Contoh: Laptop"
required
>


<label>Jumlah</label>

<input
type="number"
name="jumlah"
min="0"
required
>


<label>Kondisi</label>

<select name="kondisi">

<option value="Baik">Baik</option>

<option value="Rusak Ringan">
Rusak Ringan
</option>

<option value="Rusak Berat">
Rusak Berat
</option>

</select>


<br><br>

<button type="submit" name="tambah">
💾 Simpan Barang
</button>

</form>

</div>

<?php } ?>


<div class="card">

<h3>📋 Daftar Barang</h3>

<table>

<thead>

<tr>

<th>No</th>

<th>Kode</th>

<th>Nama Barang</th>

<th>Jenis</th>

<th>Jumlah</th>

<th>Kondisi</th>

<th>Status</th>

<?php if ($role == 'Admin' || $role == 'Petugas Lab') { ?>
<th>Aksi</th>
<?php } ?>

</tr>

</thead>

<tbody>

<?php

$no = 1;

while ($data = mysqli_fetch_assoc($data_barang)) {

?>

<tr>

<td><?= $no++ ?></td>

<td>
<?= htmlspecialchars($data['kode_barang']) ?>
</td>

<td>
<b>
<?= htmlspecialchars($data['nama_barang']) ?>
</b>
</td>

<td>
<?= htmlspecialchars($data['jenis_barang']) ?>
</td>

<td>
<?= $data['jumlah'] ?>
</td>

<td>
<?= htmlspecialchars($data['kondisi']) ?>
</td>

<td>

<span class="status-active">

<?= htmlspecialchars($data['status']) ?>

</span>

</td>


<?php if ($role == 'Admin' || $role == 'Petugas Lab') { ?>

<td>

<a
href="barang.php?hapus=<?= $data['id_barang'] ?>"
class="return-btn"
onclick="return confirm('Hapus barang ini?')"
>
🗑️ Hapus
</a>

</td>

<?php } ?>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>