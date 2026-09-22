<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'Siswa') {
    header("Location: index.php");
    exit;
}

$data = mysqli_query(
    $koneksi,
    "SELECT
        p.id_peminjaman,
        s.nis,
        s.nama_siswa,
        s.kelas,
        p.tanggal_pinjam,
        p.tanggal_rencana_kembali,
        p.status_peminjaman,

        GROUP_CONCAT(
            CONCAT(
                b.nama_barang,
                ' (',
                dp.jumlah_pinjam,
                ')'
            )
            SEPARATOR ', '
        ) AS barang

    FROM peminjaman p

    INNER JOIN siswa s
    ON p.id_siswa = s.id_siswa

    INNER JOIN detail_peminjaman dp
    ON p.id_peminjaman =
       dp.id_peminjaman

    INNER JOIN barang b
    ON dp.id_barang =
       b.id_barang

    GROUP BY
        p.id_peminjaman

    ORDER BY
        p.id_peminjaman DESC"
);

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Laporan</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<div class="navbar">

<b>💻 LAPTOP PEMINJAMAN</b>

<div>

<a href="index.php">Dashboard</a>

<a href="barang.php">Barang</a>

<a href="siswa.php">Siswa</a>

<a href="peminjaman.php">
Peminjaman
</a>

<a href="pengembalian.php">
Pengembalian
</a>

<a href="laporan.php">
Laporan
</a>

<a href="logout.php">
Logout
</a>

</div>

</div>


<div class="container">

<div class="welcome">

<h2>📊 Laporan Peminjaman</h2>

<p>
Riwayat seluruh transaksi peminjaman barang.
</p>

</div>


<div class="card">

<h3>
📋 Data Transaksi
</h3>

<button
onclick="window.print()"
>
🖨️ Cetak Laporan
</button>


<table>

<thead>

<tr>

<th>No</th>

<th>NIS</th>

<th>Siswa</th>

<th>Kelas</th>

<th>Barang</th>

<th>Tanggal Pinjam</th>

<th>Kembali</th>

<th>Status</th>

</tr>

</thead>

<tbody>

<?php

$no = 1;

while ($row =
mysqli_fetch_assoc($data)) {

?>

<tr>

<td>
<?= $no++ ?>
</td>

<td>
<?= htmlspecialchars($row['nis']) ?>
</td>

<td>
<b>
<?= htmlspecialchars(
$row['nama_siswa']
) ?>
</b>
</td>

<td>
<?= htmlspecialchars(
$row['kelas']
) ?>
</td>

<td>
<?= htmlspecialchars(
$row['barang']
) ?>
</td>

<td>
<?= date(
'd-m-Y',
strtotime(
$row['tanggal_pinjam']
)
) ?>
</td>

<td>
<?= date(
'd-m-Y',
strtotime(
$row['tanggal_rencana_kembali']
)
) ?>
</td>

<td>

<span class="status-active">

<?= htmlspecialchars(
$row['status_peminjaman']
) ?>

</span>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

</body>

</html>