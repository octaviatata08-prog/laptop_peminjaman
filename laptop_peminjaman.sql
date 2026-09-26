-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 26 Sep 2026 pada 02.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laptop_peminjaman`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `password`) VALUES
(1, 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun_siswa`
--

CREATE TABLE `akun_siswa` (
  `id_akun` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `akun_siswa`
--

INSERT INTO `akun_siswa` (`id_akun`, `id_siswa`, `username`, `password`) VALUES
(1, 1, 'gita', '39ffc7f8a25f8c55be3dd42ab7025d89');

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kode_barang` varchar(30) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jenis_barang` varchar(50) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `kondisi` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT 'Baik',
  `status` enum('Tersedia','Dipinjam') DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `kode_barang`, `nama_barang`, `jenis_barang`, `jumlah`, `kondisi`, `status`) VALUES
(1, 'LPT001', 'Laptop Lenovo', 'Laptop', 9, 'Baik', 'Tersedia'),
(2, 'LPT002', 'Laptop Asus', 'Laptop', 5, 'Baik', 'Tersedia'),
(3, 'PRJ001', 'Proyektor Epson', 'Proyektor', 3, 'Baik', 'Tersedia'),
(4, 'RTR001', 'Router TP-Link', 'Router', 5, 'Baik', 'Tersedia'),
(5, '11223', 'Laptop Lenovo', 'Laptop', 5, 'Baik', 'Tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_peminjaman`
--

CREATE TABLE `detail_peminjaman` (
  `id_detail` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_pinjam` int(11) NOT NULL,
  `kondisi_saat_pinjam` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_peminjaman`
--

INSERT INTO `detail_peminjaman` (`id_detail`, `id_peminjaman`, `id_barang`, `jumlah_pinjam`, `kondisi_saat_pinjam`) VALUES
(1, 1, 2, 1, 'Baik'),
(2, 2, 2, 1, 'Baik'),
(3, 3, 2, 1, 'Baik'),
(4, 4, 1, 1, 'Baik'),
(5, 5, 2, 1, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_rencana_kembali` date NOT NULL,
  `status_peminjaman` enum('Diajukan','Dipinjam','Dikembalikan','Ditolak') DEFAULT 'Diajukan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_siswa`, `id_admin`, `id_petugas`, `tanggal_pinjam`, `tanggal_rencana_kembali`, `status_peminjaman`) VALUES
(1, 1, NULL, NULL, '2026-09-22', '2026-09-23', 'Dikembalikan'),
(2, 11, NULL, NULL, '2026-09-22', '2026-09-23', 'Dipinjam'),
(3, 10, NULL, NULL, '2026-09-22', '2026-09-23', 'Dipinjam'),
(4, 10, NULL, NULL, '2026-09-22', '2026-09-23', 'Dipinjam'),
(5, 12, NULL, NULL, '2026-09-26', '2026-09-28', 'Dipinjam');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` int(11) NOT NULL,
  `id_petugas` int(11) DEFAULT NULL,
  `tanggal_kembali` date NOT NULL,
  `kondisi_saat_kembali` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `id_petugas`, `tanggal_kembali`, `kondisi_saat_kembali`, `keterangan`) VALUES
(1, 1, NULL, '2026-09-22', 'Rusak Ringan', 'maaf min');

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas_lab`
--

CREATE TABLE `petugas_lab` (
  `id_petugas` int(11) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas_lab`
--

INSERT INTO `petugas_lab` (`id_petugas`, `nama_petugas`, `username`, `password`) VALUES
(1, 'Petugas Laboratorium', 'petugas', '570c396b3fc856eceb8aa7357f32af1a');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nis` varchar(30) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `kelas` varchar(30) NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `status_akun` enum('Aktif','Diblokir') NOT NULL DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nis`, `nama_siswa`, `password`, `kelas`, `no_telp`, `status_akun`) VALUES
(1, '12345', 'Gita Octavia', 'gita123', 'XII RPL 1', '081234567890', 'Aktif'),
(10, '67890', 'Budi', 'budi123', '', NULL, 'Aktif'),
(11, '09876', 'Azzam', 'azzam123', '', NULL, 'Aktif'),
(12, '56478', 'Aziz', '', 'XII TKJ 2', '098765453456', 'Aktif');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `akun_siswa`
--
ALTER TABLE `akun_siswa`
  ADD PRIMARY KEY (`id_akun`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`);

--
-- Indeks untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_admin` (`id_admin`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD UNIQUE KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indeks untuk tabel `petugas_lab`
--
ALTER TABLE `petugas_lab`
  ADD PRIMARY KEY (`id_petugas`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `akun_siswa`
--
ALTER TABLE `akun_siswa`
  MODIFY `id_akun` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `petugas_lab`
--
ALTER TABLE `petugas_lab`
  MODIFY `id_petugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `akun_siswa`
--
ALTER TABLE `akun_siswa`
  ADD CONSTRAINT `akun_siswa_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_peminjaman`
--
ALTER TABLE `detail_peminjaman`
  ADD CONSTRAINT `detail_peminjaman_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_peminjaman_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL,
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`id_petugas`) REFERENCES `petugas_lab` (`id_petugas`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`),
  ADD CONSTRAINT `pengembalian_ibfk_2` FOREIGN KEY (`id_petugas`) REFERENCES `petugas_lab` (`id_petugas`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
