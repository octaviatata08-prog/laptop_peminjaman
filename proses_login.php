<?php
session_start();
require 'koneksi.php';

$role = $_POST['role'] ?? '';
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$id_siswa = $_POST['id_siswa'] ?? '';

/* =====================================================
   LOGIN ADMIN
===================================================== */

if ($role == 'Admin') {

    if ($username == '' || $password == '') {
        header("Location: login.php?error=Username dan password wajib diisi");
        exit;
    }

    $password_md5 = md5($password);

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM admin
         WHERE username = '$username'
         AND password = '$password_md5'
         LIMIT 1"
    );

    if (mysqli_num_rows($query) == 1) {

        $data = mysqli_fetch_assoc($query);

        $_SESSION['login'] = true;
        $_SESSION['role'] = 'Admin';
        $_SESSION['id_user'] = $data['id_admin'];
        $_SESSION['nama'] = $data['nama_admin'];

        header("Location: index.php");
        exit;
    }

    header("Location: login.php?error=Username atau password Admin salah");
    exit;
}


/* =====================================================
   LOGIN PETUGAS LAB
===================================================== */

if ($role == 'Petugas Lab') {

    if ($username == '' || $password == '') {
        header("Location: login.php?error=Username dan password wajib diisi");
        exit;
    }

    $password_md5 = md5($password);

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM petugas_lab
         WHERE username = '$username'
         AND password = '$password_md5'
         LIMIT 1"
    );

    if (mysqli_num_rows($query) == 1) {

        $data = mysqli_fetch_assoc($query);

        $_SESSION['login'] = true;
        $_SESSION['role'] = 'Petugas Lab';
        $_SESSION['id_user'] = $data['id_petugas'];
        $_SESSION['nama'] = $data['nama_petugas'];

        header("Location: index.php");
        exit;
    }

    header("Location: login.php?error=Username atau password Petugas salah");
    exit;
}


/* =====================================================
   LOGIN SISWA
===================================================== */

if ($role == 'Siswa') {

    if ($id_siswa == '') {
        header("Location: login.php?error=Silakan pilih nama siswa");
        exit;
    }

    $query = mysqli_query(
        $koneksi,
        "SELECT * FROM siswa
         WHERE id_siswa = '$id_siswa'
         LIMIT 1"
    );

    if (mysqli_num_rows($query) == 1) {

        $data = mysqli_fetch_assoc($query);

        /* CEK STATUS SISWA */

        if ($data['status_akun'] == 'Diblokir') {

            header(
                "Location: login.php?error=Akun siswa diblokir karena terlambat mengembalikan barang"
            );
            exit;
        }

        $_SESSION['login'] = true;
        $_SESSION['role'] = 'Siswa';
        $_SESSION['id_user'] = $data['id_siswa'];
        $_SESSION['id_siswa'] = $data['id_siswa'];
        $_SESSION['nama'] = $data['nama_siswa'];
        $_SESSION['nis'] = $data['nis'];

        header("Location: index.php");
        exit;
    }

    header("Location: login.php?error=Data siswa tidak ditemukan");
    exit;
}


/* =====================================================
   ROLE TIDAK DIPILIH
===================================================== */

header("Location: login.php?error=Silakan pilih jenis pengguna");
exit;
?>