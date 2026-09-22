<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php?error=Silakan login terlebih dahulu");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

/* =========================
   QUERY RIWAYAT PEMINJAMAN
   ========================= */

if ($role == 'Siswa') {

    $id_siswa = $_SESSION['id_siswa'];

    $query = mysqli_query($koneksi, "
        SELECT
            p.id_peminjaman,
            p.tanggal_pinjam,
            p.tanggal_rencana_kembali,
            p.status_peminjaman,

            s.nama_siswa,
            s.nis,
            s.kelas,

            a.nama_admin,

            pt.nama_petugas,

            GROUP_CONCAT(
                CONCAT(
                    b.nama_barang,
                    ' (',
                    dp.jumlah_pinjam,
                    ')'
                )
                SEPARATOR ', '
            ) AS daftar_barang

        FROM peminjaman p

        LEFT JOIN siswa s
            ON p.id_siswa = s.id_siswa

        LEFT JOIN admin a
            ON p.id_admin = a.id_admin

        LEFT JOIN petugas_lab pt
            ON p.id_petugas = pt.id_petugas

        LEFT JOIN detail_peminjaman dp
            ON p.id_peminjaman = dp.id_peminjaman

        LEFT JOIN barang b
            ON dp.id_barang = b.id_barang

        WHERE p.id_siswa = '$id_siswa'

        GROUP BY
            p.id_peminjaman,
            p.tanggal_pinjam,
            p.tanggal_rencana_kembali,
            p.status_peminjaman,
            s.nama_siswa,
            s.nis,
            s.kelas,
            a.nama_admin,
            pt.nama_petugas

        ORDER BY p.id_peminjaman DESC
    ");

} else {

    $query = mysqli_query($koneksi, "
        SELECT
            p.id_peminjaman,
            p.tanggal_pinjam,
            p.tanggal_rencana_kembali,
            p.status_peminjaman,

            s.nama_siswa,
            s.nis,
            s.kelas,

            a.nama_admin,

            pt.nama_petugas,

            GROUP_CONCAT(
                CONCAT(
                    b.nama_barang,
                    ' (',
                    dp.jumlah_pinjam,
                    ')'
                )
                SEPARATOR ', '
            ) AS daftar_barang

        FROM peminjaman p

        LEFT JOIN siswa s
            ON p.id_siswa = s.id_siswa

        LEFT JOIN admin a
            ON p.id_admin = a.id_admin

        LEFT JOIN petugas_lab pt
            ON p.id_petugas = pt.id_petugas

        LEFT JOIN detail_peminjaman dp
            ON p.id_peminjaman = dp.id_peminjaman

        LEFT JOIN barang b
            ON dp.id_barang = b.id_barang

        GROUP BY
            p.id_peminjaman,
            p.tanggal_pinjam,
            p.tanggal_rencana_kembali,
            p.status_peminjaman,
            s.nama_siswa,
            s.nis,
            s.kelas,
            a.nama_admin,
            pt.nama_petugas

        ORDER BY p.id_peminjaman DESC
    ");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Peminjaman</title>

<link rel="stylesheet" href="style.css">

<style>

.page-title {
    margin-bottom: 5px;
}

.page-description {
    color: #777;
    margin-top: 5px;
}

.table-wrapper {
    overflow-x: auto;
}

.loan-table {
    width: 100%;
    border-collapse: collapse;
}

.loan-table th {
    background: #f9d5e6;
    color: #633d53;
    padding: 14px;
    text-align: left;
    white-space: nowrap;
}

.loan-table td {
    padding: 14px;
    border-bottom: 1px solid #f2dfe8;
    vertical-align: middle;
}

.loan-table tr:hover td {
    background: #fff8fb;
}

.nama-siswa {
    font-weight: bold;
    color: #493744;
}

.info-kecil {
    color: #888;
    font-size: 12px;
    margin-top: 3px;
}

.status-dipinjam {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #ffe0ef;
    color: #c74686;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.status-dikembalikan {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #e0f7e8;
    color: #27834b;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.status-terlambat {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #ffe1e1;
    color: #c73737;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.empty-data {
    text-align: center;
    padding: 60px 20px;
    color: #888;
}

.empty-data h3 {
    color: #666;
    margin-bottom: 5px;
}

</style>

</head>

<body>


<!-- =========================
     NAVBAR
     ========================= -->

<div class="navbar">

    <b>LAPTOP PEMINJAMAN</b>

    <div>

        <?php if ($role == 'Admin') { ?>

            <a href="index.php">Dashboard</a>

            <a href="barang.php">Barang</a>

            <a href="siswa.php">Siswa</a>

            <a href="peminjaman.php">Peminjaman</a>

            <a href="pengembalian.php">Pengembalian</a>

            <a href="logout.php">Logout</a>

        <?php } elseif ($role == 'Petugas Lab') { ?>

            <a href="index.php">Dashboard</a>

            <a href="cek_barang.php">Cek Kondisi</a>

            <a href="peminjaman.php">Peminjaman</a>

            <a href="pengembalian.php">Pengembalian</a>

            <a href="logout.php">Logout</a>

        <?php } else { ?>

            <a href="index.php">Dashboard</a>

            <a href="barang.php">Barang</a>

            <a href="peminjaman.php">Peminjaman Saya</a>

            <a href="pengembalian.php">Pengembalian</a>

            <a href="logout.php">Logout</a>

        <?php } ?>

    </div>

</div>


<!-- =========================
     CONTENT
     ========================= -->

<div class="container">

    <h2 class="page-title">
        Riwayat Peminjaman
    </h2>

    <p class="page-description">

        <?php if ($role == 'Siswa') { ?>

            Daftar riwayat peminjaman barang yang kamu lakukan.

        <?php } else { ?>

            Daftar seluruh transaksi peminjaman barang laboratorium.

        <?php } ?>

    </p>


    <div class="card">

        <h3>
            Data Peminjaman
        </h3>

        <p>
            Menampilkan data peminjaman barang laboratorium.
        </p>


        <div class="table-wrapper">

            <table class="loan-table">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Peminjam</th>

                        <th>Barang</th>

                        <th>Tanggal Pinjam</th>

                        <th>Rencana Kembali</th>

                        <th>Admin</th>

                        <th>Petugas</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                if ($query && mysqli_num_rows($query) > 0) {

                    while ($data = mysqli_fetch_assoc($query)) {

                        $status = strtolower(
                            trim($data['status_peminjaman'])
                        );

                        if ($status == 'dipinjam') {

                            $status_class = 'status-dipinjam';

                        } elseif ($status == 'dikembalikan') {

                            $status_class = 'status-dikembalikan';

                        } elseif ($status == 'terlambat') {

                            $status_class = 'status-terlambat';

                        } else {

                            $status_class = 'status-dipinjam';

                        }

                ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>


                        <td>

                            <div class="nama-siswa">

                                <?= htmlspecialchars(
                                    $data['nama_siswa']
                                ) ?>

                            </div>

                            <div class="info-kecil">

                                NIS:
                                <?= htmlspecialchars(
                                    $data['nis']
                                ) ?>

                            </div>

                            <div class="info-kecil">

                                <?= htmlspecialchars(
                                    $data['kelas']
                                ) ?>

                            </div>

                        </td>


                        <td>

                            <b>

                                <?= !empty($data['daftar_barang'])
                                    ? htmlspecialchars(
                                        $data['daftar_barang']
                                    )
                                    : '-'
                                ?>

                            </b>

                        </td>


                        <td>

                            <?= !empty($data['tanggal_pinjam'])

                                ? date(
                                    'd-m-Y',
                                    strtotime(
                                        $data['tanggal_pinjam']
                                    )
                                )

                                : '-'
                            ?>

                        </td>


                        <td>

                            <?= !empty(
                                $data['tanggal_rencana_kembali']
                            )

                                ? date(
                                    'd-m-Y',
                                    strtotime(
                                        $data[
                                            'tanggal_rencana_kembali'
                                        ]
                                    )
                                )

                                : '-'
                            ?>

                        </td>


                        <td>

                            <?= !empty($data['nama_admin'])

                                ? htmlspecialchars(
                                    $data['nama_admin']
                                )

                                : '-'
                            ?>

                        </td>


                        <td>

                            <?= !empty($data['nama_petugas'])

                                ? htmlspecialchars(
                                    $data['nama_petugas']
                                )

                                : '-'
                            ?>

                        </td>


                        <td>

                            <span class="<?= $status_class ?>">

                                <?= htmlspecialchars(
                                    $data['status_peminjaman']
                                ) ?>

                            </span>

                        </td>

                    </tr>


                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="8">

                            <div class="empty-data">

                                <h3>
                                    Belum Ada Data Peminjaman
                                </h3>

                                <p>
                                    Belum ada transaksi peminjaman yang tercatat.
                                </p>

                            </div>

                        </td>

                    </tr>

                <?php

                }

                ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


</body>

</html>