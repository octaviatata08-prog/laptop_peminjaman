<?php

session_start();

function wajib_login()
{
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit;
    }
}

function aman($data)
{
    global $koneksi;
    return mysqli_real_escape_string($koneksi, trim($data));
}

function redirect($halaman)
{
    header("Location: " . $halaman);
    exit;
}

?>