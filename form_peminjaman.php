<?php
session_start();
require 'koneksi.php';

/* =====================================================
   CEK LOGIN
===================================================== */

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

/* =====================================================
   KHUSUS SISWA
===================================================== */

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Siswa') {
    header("Location: index.php");
    exit;
}

/* =====================================================
   DATA SESSION
===================================================== */

$id_siswa = $_SESSION['id_siswa'];
$nama     = $_SESSION['nama'];

/* =====================================================
   AMBIL ID BARANG
===================================================== */

$id_barang = isset($_GET['id_barang'])
    ? intval($_GET['id_barang'])
    : 0;

if ($id_barang <= 0) {
    header("Location: index.php");
    exit;
}

/* =====================================================
   AMBIL DATA BARANG
===================================================== */

$query_barang = mysqli_query(
    $koneksi,
    "SELECT *
     FROM barang
     WHERE id_barang = '$id_barang'
     LIMIT 1"
);

if (!$query_barang) {
    die("Query barang error: " . mysqli_error($koneksi));
}

if (mysqli_num_rows($query_barang) == 0) {
    die("Barang tidak ditemukan.");
}

$barang = mysqli_fetch_assoc($query_barang);

/* =====================================================
   PROSES PEMINJAMAN
===================================================== */

$error = '';
$sukses = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $jumlah = isset($_POST['jumlah'])
        ? intval($_POST['jumlah'])
        : 0;

    $tanggal_rencana_kembali =
        isset($_POST['tanggal_rencana_kembali'])
        ? $_POST['tanggal_rencana_kembali']
        : '';

    /* -----------------------------------------------
       VALIDASI
    ------------------------------------------------ */

    if ($jumlah <= 0) {

        $error = "Jumlah peminjaman harus lebih dari 0.";

    } elseif ($jumlah > $barang['jumlah']) {

        $error = "Jumlah barang tidak mencukupi.";

    } elseif (empty($tanggal_rencana_kembali)) {

        $error = "Tanggal rencana kembali wajib diisi.";

    } elseif (
        $tanggal_rencana_kembali < date('Y-m-d')
    ) {

        $error = "Tanggal kembali tidak boleh sebelum hari ini.";

    } else {

        /* -------------------------------------------
           CEK APAKAH SISWA MASIH MEMINJAM
        -------------------------------------------- */

        $cek_pinjam = mysqli_query(
            $koneksi,
            "SELECT id_peminjaman
             FROM peminjaman
             WHERE id_siswa = '$id_siswa'
             AND status_peminjaman = 'Dipinjam'
             LIMIT 1"
        );

        if (!$cek_pinjam) {

            $error =
                "Gagal mengecek peminjaman: "
                . mysqli_error($koneksi);

        } elseif (mysqli_num_rows($cek_pinjam) > 0) {

            $error =
                "Kamu masih memiliki barang yang sedang dipinjam. "
                . "Kembalikan terlebih dahulu sebelum meminjam lagi.";

        } else {

            /* ---------------------------------------
               MULAI TRANSAKSI DATABASE
            ---------------------------------------- */

            mysqli_begin_transaction($koneksi);

            try {

                $tanggal_pinjam = date('Y-m-d');

                /* -----------------------------------
                   INSERT PEMINJAMAN
                ------------------------------------ */

                $sql_peminjaman = "
                    INSERT INTO peminjaman
                    (
                        id_siswa,
                        tanggal_pinjam,
                        tanggal_rencana_kembali,
                        status_peminjaman
                    )
                    VALUES
                    (
                        '$id_siswa',
                        '$tanggal_pinjam',
                        '$tanggal_rencana_kembali',
                        'Dipinjam'
                    )
                ";

                if (!mysqli_query($koneksi, $sql_peminjaman)) {
                    throw new Exception(
                        "Gagal menyimpan peminjaman: "
                        . mysqli_error($koneksi)
                    );
                }

                /* -----------------------------------
                   AMBIL ID PEMINJAMAN
                ------------------------------------ */

                $id_peminjaman =
                    mysqli_insert_id($koneksi);

                /* -----------------------------------
                   INSERT DETAIL PEMINJAMAN
                ------------------------------------ */

                $sql_detail = "
                    INSERT INTO detail_peminjaman
                    (
                        id_peminjaman,
                        id_barang,
                        jumlah_pinjam
                    )
                    VALUES
                    (
                        '$id_peminjaman',
                        '$id_barang',
                        '$jumlah'
                    )
                ";

                if (!mysqli_query($koneksi, $sql_detail)) {
                    throw new Exception(
                        "Gagal menyimpan detail peminjaman: "
                        . mysqli_error($koneksi)
                    );
                }

                /* -----------------------------------
                   KURANGI JUMLAH BARANG
                ------------------------------------ */

                $sql_update_barang = "
                    UPDATE barang
                    SET jumlah = jumlah - $jumlah
                    WHERE id_barang = '$id_barang'
                    AND jumlah >= $jumlah
                ";

                if (!mysqli_query(
                    $koneksi,
                    $sql_update_barang
                )) {

                    throw new Exception(
                        "Gagal mengurangi stok barang: "
                        . mysqli_error($koneksi)
                    );
                }

                /* -----------------------------------
                   CEK APAKAH STOK BENAR-BENAR BERUBAH
                ------------------------------------ */

                if (mysqli_affected_rows($koneksi) == 0) {

                    throw new Exception(
                        "Stok barang tidak mencukupi."
                    );
                }

                /* -----------------------------------
                   SIMPAN SEMUA
                ------------------------------------ */

                mysqli_commit($koneksi);

                /* -----------------------------------
                   REDIRECT KE PEMINJAMAN SAYA
                ------------------------------------ */

                header(
                    "Location: peminjaman.php?status=sukses"
                );
                exit;

            } catch (Exception $e) {

                mysqli_rollback($koneksi);

                $error = $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Pinjam Barang</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .form-card {
            max-width: 650px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .form-card h2 {
            color: #d4498d;
            margin-bottom: 10px;
        }

        .barang-info {
            background: #fff1f7;
            padding: 20px;
            border-radius: 14px;
            margin: 20px 0;
        }

        .barang-info p {
            margin: 8px 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
        }

        .btn-submit {
            display: inline-block;
            border: none;
            background: #d4498d;
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
        }

        .btn-submit:hover {
            background: #bd3977;
        }

        .btn-back {
            display: inline-block;
            margin-left: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            background: #eee;
            color: #555;
            text-decoration: none;
        }

        .alert-error {
            background: #ffe1e1;
            color: #a00000;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .info {
            color: #666;
            font-size: 14px;
        }

    </style>

</head>

<body>

<!-- =====================================================
     NAVBAR
===================================================== -->

<div class="navbar">

    <b>💻 LAPTOP PEMINJAMAN</b>

    <div>

        <a href="index.php">
            Dashboard
        </a>

        <a href="barang.php">
            Barang
        </a>

        <a href="peminjaman.php">
            Peminjaman Saya
        </a>

        <a href="pengembalian.php">
            Pengembalian
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<!-- =====================================================
     FORM
===================================================== -->

<div class="container">

    <div class="form-card">

        <h2>
            📥 Pinjam Barang
        </h2>

        <p class="info">
            Silakan isi data peminjaman barang.
        </p>


        <!-- ERROR -->

        <?php if (!empty($error)) { ?>

            <div class="alert-error">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>

        <?php } ?>


        <!-- INFORMASI BARANG -->

        <div class="barang-info">

            <h3>
                📦 Informasi Barang
            </h3>

            <p>
                <b>Nama Barang:</b>
                <?= htmlspecialchars(
                    $barang['nama_barang']
                ) ?>
            </p>

            <p>
                <b>Jenis:</b>
                <?= htmlspecialchars(
                    $barang['jenis_barang']
                ) ?>
            </p>

            <p>
                <b>Jumlah Tersedia:</b>
                <?= htmlspecialchars(
                    $barang['jumlah']
                ) ?>
            </p>

            <p>
                <b>Kondisi:</b>
                <?= htmlspecialchars(
                    $barang['kondisi']
                ) ?>
            </p>

        </div>


        <!-- FORM -->

        <form
            method="POST"
            action=""
        >

            <div class="form-group">

                <label>
                    Jumlah yang Dipinjam
                </label>

                <input
                    type="number"
                    name="jumlah"
                    min="1"
                    max="<?= $barang['jumlah'] ?>"
                    value="1"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Tanggal Rencana Kembali
                </label>

                <input
                    type="date"
                    name="tanggal_rencana_kembali"
                    min="<?= date('Y-m-d') ?>"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn-submit"
            >
                📥 Ajukan Peminjaman
            </button>


            <a
                href="index.php"
                class="btn-back"
            >
                Kembali
            </a>

        </form>

    </div>

</div>

</body>

</html>