-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 01, 2026 at 07:39 AM
-- Server version: 5.7.33
-- PHP Version: 7.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int(11) NOT NULL,
  `kd_anggota` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `kd_anggota`, `nama`, `jenis_kelamin`, `no_hp`, `alamat`, `email`) VALUES
(1, 'AGT001', 'Galang Nurhafizd', 'Laki-laki', '083898773214', 'Jl. Ahmad Yani Pangkalpinang', 'galang@gmail.com'),
(2, 'AGT002', 'Muhammad Azka', 'Laki-laki', '081234567802', 'Jl. Sudirman Pangkalpinang', 'azka@gmail.com'),
(3, 'AGT003', 'Argya Zahran', 'Laki-laki', '081234567803', 'Jl. Ahmad Yani Pangkalpinang', 'argya@gmail.com'),
(4, 'AGT004', 'Steven Marcelino', 'Laki-laki', '081234567804', 'Jl. Depati Hamzah Pangkalpinang', 'marcelino@gmail.com'),
(5, 'AGT005', 'Eko Pratama', 'Laki-laki', '081234567805', 'Jl. Kampung Melayu Pangkalpinang', 'eko@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `kd_buku` varchar(10) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `penerbit` varchar(100) NOT NULL,
  `tahun_terbit` year(4) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `stok` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id`, `kd_buku`, `judul`, `penulis`, `penerbit`, `tahun_terbit`, `kategori`, `stok`) VALUES
(1, 'BK001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', 'Novel', 5),
(2, 'BK002', 'Bumi', 'Tere Liye', 'Gramedia Pustaka Utama', '2014', 'Fantasi', 4),
(3, 'BK003', 'Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia Pustaka Utama', '2009', 'Novel', 6),
(4, 'BK004', 'Laut Bercerita', 'Leila S. Chudori', 'Kepustakaan Populer Gramedia', '2017', 'Fiksi', 3),
(5, 'BK005', 'Filosofi Teras', 'Henry Manampiring', 'Kompas', '2018', 'Pengembangan Diri', 7);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `kd_peminjaman` varchar(10) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `status` enum('Dipinjam','Dikembalikan') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `kd_peminjaman`, `anggota_id`, `buku_id`, `tanggal_pinjam`, `tanggal_kembali`, `status`) VALUES
(1, 'PMJ001', 1, 1, '2026-10-01', '2026-10-08', 'Dikembalikan'),
(2, 'PMJ002', 2, 2, '2026-10-05', '2026-10-12', 'Dikembalikan'),
(3, 'PMJ003', 3, 3, '2026-10-10', '2026-10-17', 'Dikembalikan'),
(4, 'PMJ004', 4, 4, '2026-10-20', '2026-10-27', 'Dikembalikan'),
(5, 'PMJ005', 5, 5, '2026-10-25', '2026-11-01', 'Dipinjam');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kd_anggota` (`kd_anggota`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kd_buku` (`kd_buku`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kd_peminjaman` (`kd_peminjaman`),
  ADD KEY `fk_peminjaman_anggota` (`anggota_id`),
  ADD KEY `fk_peminjaman_buku` (`buku_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_peminjaman_anggota` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`),
  ADD CONSTRAINT `fk_peminjaman_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
