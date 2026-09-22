<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];


/* =====================================================
   DATA ADMIN
===================================================== */

if ($role == 'Admin') {

    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total FROM barang"
    );
    $total_barang = mysqli_fetch_assoc($q)['total'];

    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total FROM siswa"
    );
    $total_siswa = mysqli_fetch_assoc($q)['total'];

    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total FROM petugas_lab"
    );
    $total_petugas = mysqli_fetch_assoc($q)['total'];

    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total FROM peminjaman"
    );
    $total_transaksi = mysqli_fetch_assoc($q)['total'];

    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM peminjaman
         WHERE status_peminjaman = 'Dipinjam'
         AND tanggal_rencana_kembali < CURDATE()"
    );
    $total_terlambat = mysqli_fetch_assoc($q)['total'];

    $siswa_meminjam = mysqli_query($koneksi,
        "SELECT
            p.id_peminjaman,
            p.tanggal_pinjam,
            p.tanggal_rencana_kembali,
            s.nis,
            s.nama_siswa,
            s.kelas,
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
        JOIN siswa s
            ON p.id_siswa = s.id_siswa
        JOIN detail_peminjaman dp
            ON p.id_peminjaman = dp.id_peminjaman
        JOIN barang b
            ON dp.id_barang = b.id_barang
        WHERE p.status_peminjaman = 'Dipinjam'
        GROUP BY p.id_peminjaman
        ORDER BY p.tanggal_rencana_kembali ASC"
    );
}


/* =====================================================
   DATA PETUGAS LAB
===================================================== */

if ($role == 'Petugas Lab') {

    // Barang yang kondisinya bukan Baik
    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM barang
         WHERE kondisi != 'Baik'"
    );
    $perlu_dicek = mysqli_fetch_assoc($q)['total'];

    // Barang habis
    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM barang
         WHERE jumlah = 0"
    );
    $barang_habis = mysqli_fetch_assoc($q)['total'];

    // Kembali hari ini
    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM peminjaman
         WHERE status_peminjaman = 'Dipinjam'
         AND tanggal_rencana_kembali = CURDATE()"
    );
    $kembali_hari_ini = mysqli_fetch_assoc($q)['total'];

    // Terlambat
    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM peminjaman
         WHERE status_peminjaman = 'Dipinjam'
         AND tanggal_rencana_kembali < CURDATE()"
    );
    $terlambat = mysqli_fetch_assoc($q)['total'];

    // Barang yang sedang dipinjam
    $barang_dipinjam = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM detail_peminjaman dp
         JOIN peminjaman p
            ON dp.id_peminjaman = p.id_peminjaman
         WHERE p.status_peminjaman = 'Dipinjam'"
    );

    $total_barang_dipinjam =
        mysqli_fetch_assoc($barang_dipinjam)['total'];
}


/* =====================================================
   DATA SISWA
===================================================== */

if ($role == 'Siswa') {

    $id_siswa = $_SESSION['id_siswa'];

    $q = mysqli_query($koneksi,
        "SELECT COUNT(*) AS total
         FROM peminjaman
         WHERE id_siswa = '$id_siswa'
         AND status_peminjaman = 'Dipinjam'"
    );

    $jumlah_pinjam =
        mysqli_fetch_assoc($q)['total'];

    $pinjaman_saya = mysqli_query($koneksi,
        "SELECT
            p.id_peminjaman,
            p.tanggal_pinjam,
            p.tanggal_rencana_kembali,
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
        JOIN detail_peminjaman dp
            ON p.id_peminjaman = dp.id_peminjaman
        JOIN barang b
            ON dp.id_barang = b.id_barang
        WHERE p.id_siswa = '$id_siswa'
        AND p.status_peminjaman = 'Dipinjam'
        GROUP BY p.id_peminjaman
        ORDER BY p.tanggal_rencana_kembali ASC"
    );

    $barang_tersedia = mysqli_query($koneksi,
        "SELECT *
         FROM barang
         WHERE jumlah > 0
         ORDER BY nama_barang ASC
         LIMIT 6"
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Laptop Peminjaman</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<div class="navbar">

    <b>💻 LAPTOP PEMINJAMAN</b>

    <div>

        <?php if ($role == 'Admin') { ?>

            <a href="index.php">Dashboard</a>
            <a href="barang.php">Barang</a>
            <a href="siswa.php">Siswa</a>
            <a href="peminjaman.php">Peminjaman</a>
            <a href="pengembalian.php">Pengembalian</a>
            <a href="laporan.php">Laporan</a>

        <?php } ?>


        <?php if ($role == 'Petugas Lab') { ?>

            <a href="index.php">Dashboard</a>

            <a href="cek_barang.php">
                Cek Kondisi
            </a>

            <a href="peminjaman.php">
                Pantau Peminjaman
            </a>

            <a href="pengembalian.php">
                Pengembalian
            </a>

        <?php } ?>


        <?php if ($role == 'Siswa') { ?>

            <a href="index.php">
                Dashboard
            </a>

            <a href="barang.php">
                Barang
            </a>

            <a href="peminjaman.php">
                Peminjaman
            </a>

            <a href="pengembalian.php">
                Pengembalian
            </a>

        <?php } ?>


        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<div class="container">


<!-- =====================================================
     ADMIN
===================================================== -->

<?php if ($role == 'Admin') { ?>

    <div class="welcome">

        <h2>
            Dashboard Admin 👑
        </h2>

        <p>
            Halo, <?= htmlspecialchars($nama) ?> 👋
        </p>

        <span class="role-badge">
            Pengelola Sistem
        </span>

    </div>


    <?php if ($total_terlambat > 0) { ?>

        <div class="notif notif-danger">

            <div class="notif-icon">
                ⚠️
            </div>

            <div class="notif-content">

                <h4>
                    Perhatian Admin
                </h4>

                <p>
                    Ada
                    <b><?= $total_terlambat ?></b>
                    peminjaman yang terlambat.
                </p>

            </div>

        </div>

    <?php } else { ?>

        <div class="notif notif-success">

            <div class="notif-icon">
                ✅
            </div>

            <div class="notif-content">

                <h4>
                    Semua Aman
                </h4>

                <p>
                    Tidak ada peminjaman yang terlambat.
                </p>

            </div>

        </div>

    <?php } ?>


    <div class="grid">

        <div class="stat">

            <h3>📦 Total Barang</h3>

            <h1>
                <?= $total_barang ?>
            </h1>

        </div>

        <div class="stat">

            <h3>👨‍🎓 Total Siswa</h3>

            <h1>
                <?= $total_siswa ?>
            </h1>

        </div>

        <div class="stat">

            <h3>🧑‍🔧 Total Petugas</h3>

            <h1>
                <?= $total_petugas ?>
            </h1>

        </div>

        <div class="stat">

            <h3>📋 Total Transaksi</h3>

            <h1>
                <?= $total_transaksi ?>
            </h1>

        </div>

    </div>


    <div class="card">

        <h3>
            👨‍🎓 Siswa yang Sedang Meminjam
        </h3>

        <p>
            Pantauan seluruh peminjaman barang.
        </p>

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th>Barang</th>
                    <th>Pinjam</th>
                    <th>Kembali</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            while (
                $data =
                mysqli_fetch_assoc(
                    $siswa_meminjam
                )
            ) {

            ?>

                <tr>

                    <td>
                        <?= $no++ ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $data['nis']
                        ) ?>
                    </td>

                    <td>
                        <b>
                            <?= htmlspecialchars(
                                $data['nama_siswa']
                            ) ?>
                        </b>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $data['kelas']
                        ) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $data['barang']
                        ) ?>
                    </td>

                    <td>
                        <?= date(
                            'd-m-Y',
                            strtotime(
                                $data['tanggal_pinjam']
                            )
                        ) ?>
                    </td>

                    <td>
                        <?= date(
                            'd-m-Y',
                            strtotime(
                                $data[
                                    'tanggal_rencana_kembali'
                                ]
                            )
                        ) ?>
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

<?php } ?>


<!-- =====================================================
     PETUGAS LAB
===================================================== -->

<?php if ($role == 'Petugas Lab') { ?>

    <div class="welcome">

        <h2>
            Dashboard Petugas Lab 🧑‍🔧
        </h2>

        <p>
            Halo, <?= htmlspecialchars($nama) ?> 👋
        </p>

        <span class="role-badge">
            Pemeriksaan & Operasional Laboratorium
        </span>

    </div>


    <div class="notif notif-info">

        <div class="notif-icon">
            🔎
        </div>

        <div class="notif-content">

            <h4>
                Tugas Petugas Lab
            </h4>

            <p>
                Periksa kondisi barang, pantau
                peminjaman, dan periksa barang
                setelah dikembalikan.
            </p>

        </div>

    </div>


    <!-- STATISTIK PETUGAS -->

    <div class="grid">

        <div class="stat">

            <h3>
                💻 Sedang Dipinjam
            </h3>

            <h1>
                <?= $total_barang_dipinjam ?>
            </h1>

        </div>


        <div class="stat">

            <h3>
                🔎 Kondisi Perlu Dicek
            </h3>

            <h1>
                <?= $perlu_dicek ?>
            </h1>

        </div>


        <div class="stat">

            <h3>
                ⏰ Kembali Hari Ini
            </h3>

            <h1>
                <?= $kembali_hari_ini ?>
            </h1>

        </div>


        <div class="stat">

            <h3>
                ⚠️ Terlambat
            </h3>

            <h1>
                <?= $terlambat ?>
            </h1>

        </div>

    </div>


    <!-- PEKERJAAN PETUGAS -->

    <div class="card">

        <h3>
            🧑‍🔧 Pekerjaan Petugas
        </h3>

        <p>
            Pilih pekerjaan sesuai pemeriksaan
            yang ingin dilakukan.
        </p>


        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Pekerjaan</th>
                    <th>Fungsi</th>
                    <th>Aksi</th>

                </tr>

            </thead>


            <tbody>


                <!-- CEK KONDISI -->

                <tr>

                    <td>
                        1
                    </td>

                    <td>
                        🔎
                        <b>
                            Cek Kondisi Barang
                        </b>
                    </td>

                    <td>
                        Mengecek barang yang
                        sedang dipinjam, siapa
                        yang meminjam, serta
                        kondisi barang.
                    </td>

                    <td>

                        <a
                            href="cek_barang.php"
                            class="btn"
                        >
                            🔎 Periksa
                        </a>

                    </td>

                </tr>


                <!-- PANTAU PEMINJAMAN -->

                <tr>

                    <td>
                        2
                    </td>

                    <td>
                        👀
                        <b>
                            Pantau Peminjaman
                        </b>
                    </td>

                    <td>
                        Melihat barang yang
                        sedang digunakan siswa.
                    </td>

                    <td>

                        <a
                            href="peminjaman.php"
                            class="btn"
                        >
                            👀 Pantau
                        </a>

                    </td>

                </tr>


                <!-- PENGEMBALIAN -->

                <tr>

                    <td>
                        3
                    </td>

                    <td>
                        🔄
                        <b>
                            Periksa Pengembalian
                        </b>
                    </td>

                    <td>
                        Mengecek kondisi barang
                        setelah dikembalikan.
                    </td>

                    <td>

                        <a
                            href="pengembalian.php"
                            class="btn"
                        >
                            🔄 Periksa
                        </a>

                    </td>

                </tr>


            </tbody>

        </table>

    </div>


    <!-- INFORMASI KERJA PETUGAS -->

    <div class="card">

        <h3>
            📌 Alur Kerja Petugas
        </h3>

        <div class="notif notif-info">

            <div class="notif-icon">
                1️⃣
            </div>

            <div class="notif-content">

                <h4>
                    Cek Kondisi Barang
                </h4>

                <p>
                    Lihat barang yang dipinjam
                    dan siapa yang menggunakannya.
                </p>

            </div>

        </div>


        <div class="notif notif-warning">

            <div class="notif-icon">
                2️⃣
            </div>

            <div class="notif-content">

                <h4>
                    Pantau Peminjaman
                </h4>

                <p>
                    Pastikan barang masih tercatat
                    sebagai barang yang dipinjam.
                </p>

            </div>

        </div>


        <div class="notif notif-success">

            <div class="notif-icon">
                3️⃣
            </div>

            <div class="notif-content">

                <h4>
                    Periksa Pengembalian
                </h4>

                <p>
                    Setelah barang dikembalikan,
                    periksa kondisinya sebelum
                    barang digunakan kembali.
                </p>

            </div>

        </div>

    </div>

<?php } ?>


<!-- =====================================================
     SISWA
===================================================== -->

<?php if ($role == 'Siswa') { ?>

    <div class="welcome">

        <h2>
            Dashboard Siswa 👨‍🎓
        </h2>

        <p>
            Halo, <?= htmlspecialchars($nama) ?> 👋
        </p>

        <span class="role-badge">
            Peminjam Barang
        </span>

    </div>


    <div class="grid">

        <div class="stat">

            <h3>
                💻 Sedang Dipinjam
            </h3>

            <h1>
                <?= $jumlah_pinjam ?>
            </h1>

        </div>

    </div>


    <div class="card">

        <h3>
            📋 Pinjaman Saya
        </h3>

        <?php if (
            mysqli_num_rows(
                $pinjaman_saya
            ) > 0
        ) { ?>

            <table>

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Barang</th>
                        <th>Dipinjam</th>
                        <th>Harus Kembali</th>
                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                <?php

                $no = 1;

                while (
                    $data =
                    mysqli_fetch_assoc(
                        $pinjaman_saya
                    )
                ) {

                ?>

                    <tr>

                        <td>
                            <?= $no++ ?>
                        </td>

                        <td>
                            <b>
                                <?= htmlspecialchars(
                                    $data['barang']
                                ) ?>
                            </b>
                        </td>

                        <td>
                            <?= date(
                                'd-m-Y',
                                strtotime(
                                    $data[
                                        'tanggal_pinjam'
                                    ]
                                )
                            ) ?>
                        </td>

                        <td>
                            <?= date(
                                'd-m-Y',
                                strtotime(
                                    $data[
                                        'tanggal_rencana_kembali'
                                    ]
                                )
                            ) ?>
                        </td>

                        <td>

                            <a
                                href="pengembalian.php?id=<?= $data['id_peminjaman'] ?>"
                                class="return-btn"
                            >
                                🔄 Kembalikan
                            </a>

                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <div class="empty">

                📭

                <br><br>

                Kamu belum meminjam barang.

                <br><br>

                <a
                    href="peminjaman.php"
                    class="btn"
                >
                    📦 Pinjam Barang
                </a>

            </div>

        <?php } ?>

    </div>


    <div class="card">

        <h3>
            📦 Barang yang Tersedia
        </h3>

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Barang</th>
                    <th>Jenis</th>
                    <th>Tersedia</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

            <?php

            $no = 1;

            while (
                $data =
                mysqli_fetch_assoc(
                    $barang_tersedia
                )
            ) {

            ?>

                <tr>

                    <td>
                        <?= $no++ ?>
                    </td>

                    <td>
                        <b>
                            <?= htmlspecialchars(
                                $data['nama_barang']
                            ) ?>
                        </b>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $data['jenis_barang']
                        ) ?>
                    </td>

                    <td>
                        <?= $data['jumlah'] ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $data['kondisi']
                        ) ?>
                    </td>

                    <td>

                        <a
                            href="peminjaman.php?barang=<?= $data['id_barang'] ?>"
                            class="btn"
                        >
                            📥 Pinjam
                        </a>

                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

<?php } ?>


</div>

</body>

</html>