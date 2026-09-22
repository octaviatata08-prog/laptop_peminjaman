<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'Admin') {
    header("Location: index.php");
    exit;
}


/* TAMBAH SISWA */

if (isset($_POST['tambah'])) {

    $nis = $_POST['nis'];
    $nama = $_POST['nama_siswa'];
    $kelas = $_POST['kelas'];
    $no_telp = $_POST['no_telp'];

    mysqli_query(
        $koneksi,
        "INSERT INTO siswa
        (nis, nama_siswa, kelas, no_telp)
        VALUES
        ('$nis','$nama','$kelas','$no_telp')"
    );

    header("Location: siswa.php");
    exit;
}


/* HAPUS */

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query(
        $koneksi,
        "DELETE FROM siswa
         WHERE id_siswa='$id'"
    );

    header("Location: siswa.php");
    exit;
}


$data_siswa = mysqli_query(
    $koneksi,
    "SELECT * FROM siswa
     ORDER BY nama_siswa ASC"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Data Siswa</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="navbar">

<b>💻 LAPTOP PEMINJAMAN</b>

<div>

<a href="index.php">Dashboard</a>

<a href="barang.php">Barang</a>

<a href="siswa.php">Siswa</a>

<a href="peminjaman.php">Peminjaman</a>

<a href="pengembalian.php">Pengembalian</a>

<a href="laporan.php">Laporan</a>

<a href="logout.php">Logout</a>

</div>

</div>


<div class="container">

<div class="welcome">

<h2>👨‍🎓 Data Siswa</h2>

<p>
Kelola data siswa yang dapat menggunakan sistem.
</p>

</div>


<div class="card">

<h3>➕ Tambah Siswa</h3>

<form method="POST">

<label>NIS</label>

<input
type="text"
name="nis"
required
>


<label>Nama Siswa</label>

<input
type="text"
name="nama_siswa"
required
>


<label>Kelas</label>

<input
type="text"
name="kelas"
placeholder="Contoh: XII RPL 1"
required
>


<label>No. Telepon</label>

<input
type="text"
name="no_telp"
>


<br><br>

<button type="submit" name="tambah">
💾 Simpan Siswa
</button>

</form>

</div>


<div class="card">

<h3>📋 Daftar Siswa</h3>

<table>

<thead>

<tr>

<th>No</th>

<th>NIS</th>

<th>Nama</th>

<th>Kelas</th>

<th>No. Telepon</th>

<th>Aksi</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

while ($data = mysqli_fetch_assoc($data_siswa)) {

?>

<tr>

<td><?= $no++ ?></td>

<td><?= htmlspecialchars($data['nis']) ?></td>

<td>
<b>
<?= htmlspecialchars($data['nama_siswa']) ?>
</b>
</td>

<td>
<?= htmlspecialchars($data['kelas']) ?>
</td>

<td>
<?= htmlspecialchars($data['no_telp']) ?>
</td>

<td>

<a
href="siswa.php?hapus=<?= $data['id_siswa'] ?>"
class="return-btn"
onclick="return confirm('Hapus siswa ini?')"
>
🗑️ Hapus
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>