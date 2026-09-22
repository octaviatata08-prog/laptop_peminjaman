<?php
session_start();

require 'koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Laptop Peminjaman</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="login-page">


<div class="login-box">


    <div class="login-header">

        <div class="login-icon">
            💻
        </div>

        <h1>Laptop Peminjaman</h1>

        <p>
            Silakan login untuk masuk ke sistem
        </p>

    </div>


    <?php if (isset($_GET['error'])) { ?>

        <div class="error-box">

            ⚠️
            <?= htmlspecialchars($_GET['error']) ?>

        </div>

    <?php } ?>


    <form
        action="proses_login.php"
        method="POST"
    >


        <!-- PILIH ROLE -->

        <label>
            Login Sebagai
        </label>

        <select
            name="role"
            id="role"
            required
        >

            <option value="">
                -- Pilih Pengguna --
            </option>

            <option value="Admin">
                Admin
            </option>

            <option value="Petugas Lab">
                Petugas Lab
            </option>

            <option value="Siswa">
                Siswa
            </option>

        </select>


        <!-- LOGIN ADMIN / PETUGAS -->

        <div id="loginStaff">


            <label>
                Username
            </label>

            <input
                type="text"
                name="username"
                id="username"
                placeholder="Masukkan username"
            >


            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                id="password"
                placeholder="Masukkan password"
            >

        </div>


        <!-- LOGIN SISWA -->

        <div
            id="loginSiswa"
            style="display:none;"
        >


            <label>
                Pilih Nama Siswa
            </label>


            <select
                name="id_siswa"
                id="id_siswa"
            >

                <option value="">
                    -- Pilih Nama Siswa --
                </option>


                <?php

                $siswa = mysqli_query(
                    $koneksi,

                    "SELECT
                        id_siswa,
                        nis,
                        nama_siswa,
                        kelas
                     FROM siswa
                     ORDER BY nama_siswa ASC"
                );


                while (
                    $data =
                    mysqli_fetch_assoc($siswa)
                ) {

                ?>

                    <option
                        value="<?= $data['id_siswa'] ?>"
                    >

                        <?= htmlspecialchars(
                            $data['nama_siswa']
                        ) ?>

                        -
                        <?= htmlspecialchars(
                            $data['kelas']
                        ) ?>

                    </option>


                <?php

                }

                ?>

            </select>


            <p class="siswa-info">

                💡 Pilih nama kamu untuk masuk
                sebagai siswa.

            </p>


        </div>


        <button type="submit">

            🔐 MASUK

        </button>


    </form>


    <div class="login-info">

        <div class="info-title">
            💡 Informasi Login
        </div>


        <p>

            <b>Admin</b><br>

            Login menggunakan username
            dan password Admin.

        </p>


        <p>

            <b>Petugas Lab</b><br>

            Login menggunakan username
            dan password Petugas Lab.

        </p>


        <p>

            <b>Siswa</b><br>

            Cukup pilih nama siswa.
            Tidak perlu NIS atau password.

        </p>

    </div>


</div>


<script>


const role =
    document.getElementById("role");


const loginStaff =
    document.getElementById("loginStaff");


const loginSiswa =
    document.getElementById("loginSiswa");


const username =
    document.getElementById("username");


const password =
    document.getElementById("password");


const idSiswa =
    document.getElementById("id_siswa");


role.addEventListener(
    "change",
    function () {


        if (this.value === "Siswa") {


            // TAMPILKAN LOGIN SISWA

            loginStaff.style.display =
                "none";

            loginSiswa.style.display =
                "block";


            username.required =
                false;

            password.required =
                false;

            idSiswa.required =
                true;


        } else {


            // TAMPILKAN LOGIN ADMIN/PETUGAS

            loginStaff.style.display =
                "block";

            loginSiswa.style.display =
                "none";


            username.required =
                true;

            password.required =
                true;

            idSiswa.required =
                false;

        }

    }

);

</script>


</body>

</html>