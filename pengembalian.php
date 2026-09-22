<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php?error=Silakan login terlebih dahulu");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

$query = mysqli_query($koneksi, "
    SELECT
        pg.id_pengembalian,
        pg.tanggal_kembali,
        pg.kondisi_saat_kembali,
        pg.keterangan,

        p.id_peminjaman,
        p.tanggal_pinjam,
        p.tanggal_rencana_kembali,
        p.status_peminjaman,

        s.nama_siswa,
        s.nis,
        s.kelas,

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

    FROM pengembalian pg

    INNER JOIN peminjaman p
        ON pg.id_peminjaman = p.id_peminjaman

    LEFT JOIN siswa s
        ON p.id_siswa = s.id_siswa

    LEFT JOIN petugas_lab pt
        ON pg.id_petugas = pt.id_petugas

    LEFT JOIN detail_peminjaman dp
        ON p.id_peminjaman = dp.id_peminjaman

    LEFT JOIN barang b
        ON dp.id_barang = b.id_barang

    GROUP BY
        pg.id_pengembalian,
        pg.tanggal_kembali,
        pg.kondisi_saat_kembali,
        pg.keterangan,
        p.id_peminjaman,
        p.tanggal_pinjam,
        p.tanggal_rencana_kembali,
        p.status_peminjaman,
        s.nama_siswa,
        s.nis,
        s.kelas,
        pt.nama_petugas

    ORDER BY pg.id_pengembalian DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Riwayat Pengembalian</title>

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

.return-table {
    width: 100%;
    border-collapse: collapse;
}

.return-table th {
    background: #f9d5e6;
    color: #633d53;
    padding: 14px;
    text-align: left;
    white-space: nowrap;
}

.return-table td {
    padding: 14px;
    border-bottom: 1px solid #f2dfe8;
    vertical-align: middle;
}

.return-table tr:hover td {
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

.kondisi-baik {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #e0f7e8;
    color: #27834b;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.kondisi-ringan {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #fff0d2;
    color: #a96c13;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.kondisi-berat {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #ffe1e1;
    color: #c73737;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.status-kembali {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 20px;
    background: #e0f7e8;
    color: #27834b;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
}

.keterangan {
    max-width: 250px;
    color: #666;
    font-size: 13px;
    line-height: 1.5;
}

.empty-data {
    text-align: center;
    padding: 60px 20px;
    color: #888;
}

.empty-icon {
    font-size: 50px;
    margin-bottom: 12px;
}

.empty-data h3 {
    color: #666;
    margin-bottom: 5px;
}

</style>

</head>

<body>

<!-- NAVBAR -->

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


<!-- CONTENT -->

<div class="container">

    <h2 class="page-title">
        Riwayat Pengembalian
    </h2>

    <p class="page-description">
        Daftar barang laboratorium yang telah dikembalikan.
    </p>


    <div class="card">

        <h3>
            Data Pengembalian
        </h3>

        <p>
            Menampilkan seluruh transaksi barang yang sudah dikembalikan.
        </p>


        <div class="table-wrapper">

            <table class="return-table">

                <thead>

                    <tr>

                        <th>No</th>

                        <th>Peminjam</th>

                        <th>Barang</th>

                        <th>Tanggal Pinjam</th>

                        <th>Tanggal Kembali</th>

                        <th>Kondisi</th>

                        <th>Keterangan</th>

                        <th>Petugas</th>

                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                <?php

                $no = 1;

                if (mysqli_num_rows($query) > 0) {

                    while ($data = mysqli_fetch_assoc($query)) {

                        $kondisi = strtolower(
                            trim($data['kondisi_saat_kembali'])
                        );


                        if ($kondisi == 'baik') {

                            $class_kondisi = 'kondisi-baik';

                        } elseif ($kondisi == 'rusak ringan') {

                            $class_kondisi = 'kondisi-ringan';

                        } else {

                            $class_kondisi = 'kondisi-berat';

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

                                <?= htmlspecialchars(
                                    $data['daftar_barang']
                                ) ?>

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

                            <?= !empty($data['tanggal_kembali'])

                                ? date(
                                    'd-m-Y',
                                    strtotime(
                                        $data['tanggal_kembali']
                                    )
                                )

                                : '-'
                            ?>

                        </td>


                        <td>

                            <span class="<?= $class_kondisi ?>">

                                <?= htmlspecialchars(
                                    $data['kondisi_saat_kembali']
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <div class="keterangan">

                                <?php

                                if (!empty($data['keterangan'])) {

                                    echo htmlspecialchars(
                                        $data['keterangan']
                                    );

                                } else {

                                    echo '-';

                                }

                                ?>

                            </div>

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

                            <span class="status-kembali">

                                Dikembalikan

                            </span>

                        </td>

                    </tr>


                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="9">

                            <div class="empty-data">

                                <div class="empty-icon">
                                    -
                                </div>

                                <h3>
                                    Belum Ada Pengembalian
                                </h3>

                                <p>
                                    Belum ada barang yang dikembalikan.
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