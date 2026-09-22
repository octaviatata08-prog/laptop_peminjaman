<?php
session_start();
require 'koneksi.php';

/* =========================
   CEK LOGIN PETUGAS
========================= */
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'Petugas Lab') {
    header("Location: login.php?error=Silakan login sebagai Petugas Lab");
    exit;
}

$pesan = "";
$tipe = "";

/* =========================
   SIMPAN HASIL PEMERIKSAAN
========================= */
if (isset($_POST['simpan_cek'])) {

    $id_detail = (int) $_POST['id_detail'];
    $kondisi = mysqli_real_escape_string($koneksi, $_POST['kondisi']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);

    if ($kondisi == "") {
        $pesan = "Silakan pilih kondisi barang terlebih dahulu.";
        $tipe = "error";
    } else {

        $query_detail = mysqli_query($koneksi, "
            SELECT id_barang
            FROM detail_peminjaman
            WHERE id_detail = '$id_detail'
            LIMIT 1
        ");

        if (mysqli_num_rows($query_detail) == 1) {

            $data_detail = mysqli_fetch_assoc($query_detail);
            $id_barang = $data_detail['id_barang'];

            $update = mysqli_query($koneksi, "
                UPDATE barang
                SET kondisi = '$kondisi'
                WHERE id_barang = '$id_barang'
            ");

            if ($update) {
                $pesan = "Hasil pemeriksaan barang berhasil disimpan! 💾";
                $tipe = "success";
            } else {
                $pesan = "Gagal menyimpan hasil pemeriksaan.";
                $tipe = "error";
            }

        } else {
            $pesan = "Data pemeriksaan tidak ditemukan.";
            $tipe = "error";
        }
    }
}

/* =========================
   DATA BARANG YANG SEDANG DIPINJAM
========================= */
$query = mysqli_query($koneksi, "
    SELECT
        dp.id_detail,
        dp.id_peminjaman,
        dp.id_barang,
        dp.jumlah_pinjam,
        dp.kondisi_saat_pinjam,

        p.tanggal_rencana_kembali,
        p.status_peminjaman,

        s.nama_siswa,
        s.nis,
        s.kelas,

        b.nama_barang,
        b.kode_barang,
        b.jenis_barang,
        b.kondisi

    FROM detail_peminjaman dp

    JOIN peminjaman p
        ON dp.id_peminjaman = p.id_peminjaman

    JOIN siswa s
        ON p.id_siswa = s.id_siswa

    JOIN barang b
        ON dp.id_barang = b.id_barang

    WHERE p.status_peminjaman = 'Dipinjam'

    ORDER BY dp.id_detail ASC
");

/* =========================
   DATA UNTUK MODAL
========================= */
$detail = null;

if (isset($_GET['periksa'])) {

    $id_periksa = (int) $_GET['periksa'];

    $query_detail = mysqli_query($koneksi, "
        SELECT
            dp.id_detail,
            dp.jumlah_pinjam,
            dp.kondisi_saat_pinjam,

            p.tanggal_rencana_kembali,
            p.status_peminjaman,

            s.nama_siswa,
            s.nis,
            s.kelas,

            b.nama_barang,
            b.kode_barang,
            b.kondisi

        FROM detail_peminjaman dp

        JOIN peminjaman p
            ON dp.id_peminjaman = p.id_peminjaman

        JOIN siswa s
            ON p.id_siswa = s.id_siswa

        JOIN barang b
            ON dp.id_barang = b.id_barang

        WHERE dp.id_detail = '$id_periksa'
        LIMIT 1
    ");

    if (mysqli_num_rows($query_detail) == 1) {
        $detail = mysqli_fetch_assoc($query_detail);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cek Kondisi Barang</title>

<link rel="stylesheet" href="style.css">

<style>

/* =========================
   TABEL
========================= */

.check-table {
    width: 100%;
    border-collapse: collapse;
}

.check-table th {
    background: #f9d5e6;
    padding: 14px;
}

.check-table td {
    padding: 14px;
}

/* =========================
   MODAL
========================= */

.modal-bg {

    position: fixed;

    top: 0;
    left: 0;

    width: 100%;
    height: 100%;

    background: rgba(50, 30, 45, 0.55);

    display: flex;

    justify-content: center;
    align-items: center;

    z-index: 9999;

    padding: 20px;
}

.modal-box {

    width: 500px;
    max-width: 100%;

    background: white;

    border-radius: 22px;

    padding: 28px;

    box-shadow: 0 20px 50px rgba(0,0,0,.25);

    animation: muncul .25s ease;

}

@keyframes muncul {

    from {
        opacity: 0;
        transform: scale(.9) translateY(10px);
    }

    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }

}

.modal-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

}

.modal-header h2 {

    margin: 0;

    color: #d34d91;

}

.close-btn {

    text-decoration: none;

    font-size: 25px;

    color: #888;

    font-weight: bold;

}

.close-btn:hover {
    color: #d34d91;
}

/* =========================
   INFO BARANG
========================= */

.info-box {

    background: #fff5fa;

    border-radius: 14px;

    padding: 17px;

    margin-bottom: 18px;

}

.info-box p {

    margin: 6px 0;

    color: #594753;

    font-size: 14px;

}

/* =========================
   NOTIF
========================= */

.notif-popup {

    position: fixed;

    top: 25px;

    right: 25px;

    z-index: 10000;

    min-width: 320px;

    max-width: 400px;

    padding: 17px 20px;

    border-radius: 14px;

    box-shadow: 0 10px 30px rgba(0,0,0,.15);

    font-weight: bold;

    animation: notifMasuk .3s ease;

}

.notif-success {

    background: #e4f8eb;

    color: #258044;

    border-left: 5px solid #42a866;

}

.notif-error {

    background: #ffe7e7;

    color: #b52b2b;

    border-left: 5px solid #df5555;

}

@keyframes notifMasuk {

    from {
        opacity: 0;
        transform: translateX(40px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }

}

/* =========================
   TOMBOL
========================= */

.btn-simpan {

    width: 100%;

    margin-top: 15px;

    padding: 13px;

    background: #d95796;

    border: none;

    color: white;

    border-radius: 10px;

    font-weight: bold;

    cursor: pointer;

}

.btn-simpan:hover {

    background: #c74384;

}

.btn-batal {

    display: block;

    text-align: center;

    margin-top: 10px;

    padding: 11px;

    background: #f1f1f1;

    color: #666;

    text-decoration: none;

    border-radius: 10px;

    font-weight: bold;

}

.btn-batal:hover {

    background: #e7e7e7;

}

</style>

</head>

<body>


<!-- =========================
     NAVBAR
========================= -->

<div class="navbar">

    <b>💻 Laptop Peminjaman</b>

    <div>

        <a href="index.php">🏠 Dashboard</a>

        <a href="cek_barang.php">🔎 Cek Kondisi</a>

        <a href="peminjaman.php">📋 Peminjaman</a>

        <a href="pengembalian.php">↩ Pengembalian</a>

        <a href="logout.php">🚪 Logout</a>

    </div>

</div>


<!-- =========================
     NOTIFIKASI
========================= -->

<?php if ($pesan != "") { ?>

<div class="notif-popup <?= $tipe == 'success' ? 'notif-success' : 'notif-error' ?>">

    <?= $tipe == 'success' ? '✅ ' : '⚠️ ' ?>

    <?= htmlspecialchars($pesan) ?>

</div>

<script>

setTimeout(function(){

    const notif = document.querySelector('.notif-popup');

    if(notif){

        notif.style.opacity = '0';

        notif.style.transform = 'translateX(40px)';

        notif.style.transition = '.3s';

        setTimeout(() => notif.remove(), 300);

    }

}, 3000);

</script>

<?php } ?>


<!-- =========================
     CONTENT
========================= -->

<div class="container">

    <h2>🔎 Cek Kondisi Barang</h2>

    <p>
        Periksa kondisi laptop yang sedang dipinjam oleh siswa.
    </p>


    <div class="card">

        <h3>💻 Daftar Barang Sedang Dipinjam</h3>

        <p>
            Klik tombol <b>🔎 Periksa</b> untuk memeriksa kondisi barang.
        </p>


        <table class="check-table">

            <tr>

                <th>No</th>

                <th>Barang</th>

                <th>Peminjam</th>

                <th>Kelas</th>

                <th>Jumlah</th>

                <th>Kondisi</th>

                <th>Batas Kembali</th>

                <th>Aksi</th>

            </tr>


            <?php

            $no = 1;

            if (mysqli_num_rows($query) > 0) {

                while ($data = mysqli_fetch_assoc($query)) {

            ?>

            <tr>

                <td>
                    <?= $no++ ?>
                </td>

                <td>

                    <b>
                        <?= htmlspecialchars($data['nama_barang']) ?>
                    </b>

                    <br>

                    <small>
                        <?= htmlspecialchars($data['jenis_barang']) ?>
                    </small>

                    <br>

                    <small>
                        <?= htmlspecialchars($data['kode_barang']) ?>
                    </small>

                </td>


                <td>

                    <b>
                        <?= htmlspecialchars($data['nama_siswa']) ?>
                    </b>

                    <br>

                    <small>
                        NIS: <?= htmlspecialchars($data['nis']) ?>
                    </small>

                </td>


                <td>

                    <?= htmlspecialchars($data['kelas']) ?>

                </td>


                <td>

                    <?= $data['jumlah_pinjam'] ?>

                </td>


                <td>

                    <span class="status-active">

                        <?= htmlspecialchars($data['kondisi']) ?>

                    </span>

                </td>


                <td>

                    <?= date(
                        'd-m-Y',
                        strtotime($data['tanggal_rencana_kembali'])
                    ) ?>

                </td>


                <td>

                    <a
                        href="cek_barang.php?periksa=<?= $data['id_detail'] ?>"
                        class="borrow-btn"
                    >

                        🔎 Periksa

                    </a>

                </td>

            </tr>

            <?php

                }

            } else {

            ?>

            <tr>

                <td colspan="8">

                    <div class="empty">

                        🎉 Tidak ada barang yang sedang dipinjam.

                    </div>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>


<!-- =========================
     MODAL PEMERIKSAAN
========================= -->

<?php if ($detail != null) { ?>

<div class="modal-bg">

    <div class="modal-box">


        <div class="modal-header">

            <h2>
                🔎 Pemeriksaan Barang
            </h2>

            <a
                href="cek_barang.php"
                class="close-btn"
            >
                ×
            </a>

        </div>


        <!-- INFO BARANG -->

        <div class="info-box">

            <p>

                💻 <b>Barang:</b>

                <?= htmlspecialchars($detail['nama_barang']) ?>

            </p>

            <p>

                🔖 <b>Kode:</b>

                <?= htmlspecialchars($detail['kode_barang']) ?>

            </p>

            <p>

                👨‍🎓 <b>Peminjam:</b>

                <?= htmlspecialchars($detail['nama_siswa']) ?>

            </p>

            <p>

                🪪 <b>NIS:</b>

                <?= htmlspecialchars($detail['nis']) ?>

            </p>

            <p>

                📦 <b>Jumlah:</b>

                <?= $detail['jumlah_pinjam'] ?>

            </p>

            <p>

                📌 <b>Kondisi Awal:</b>

                <?= htmlspecialchars($detail['kondisi_saat_pinjam']) ?>

            </p>

        </div>


        <!-- FORM -->

        <form method="POST">

            <input
                type="hidden"
                name="id_detail"
                value="<?= $detail['id_detail'] ?>"
            >


            <label>
                🔎 Kondisi Barang Saat Dicek
            </label>

            <select
                name="kondisi"
                required
            >

                <option value="">
                    -- Pilih Kondisi --
                </option>

                <option value="Baik">
                    ✅ Baik
                </option>

                <option value="Rusak Ringan">
                    ⚠️ Rusak Ringan
                </option>

                <option value="Rusak Berat">
                    ❌ Rusak Berat
                </option>

            </select>


            <label>
                📝 Keterangan Pemeriksaan
            </label>

            <textarea
                name="keterangan"
                rows="4"
                placeholder="Contoh: Laptop masih normal dan tidak ada kerusakan."
            ></textarea>


            <button
                type="submit"
                name="simpan_cek"
                class="btn-simpan"
            >

                💾 SIMPAN HASIL PEMERIKSAAN

            </button>


            <a
                href="cek_barang.php"
                class="btn-batal"
            >

                ↩ Batal

            </a>

        </form>


    </div>

</div>

<?php } ?>

</body>

</html>