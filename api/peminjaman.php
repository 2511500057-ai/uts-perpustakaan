<?php

require_once "../config.php";
require_once "../helpers/response.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
        case 'GET':

            //id
            if (isset($_GET['id'])) {

        //pertahanan pertama: cast (int) membuang krakter jahat
        $id = (int) ($_GET['id'] ?? 0);

        //pertahanan kedua: prepare-data dikirim sebagai '?'
        //db menganggapnya murni data, bkn perintah sql

        $stmt = mysqli_prepare($koneksi,
            "SELECT
                p.id,
                p.kd_peminjaman,
                a.nama AS nama_anggota,
                b.judul AS judul_buku,
                p.tanggal_pinjam,
                p.tanggal_kembali,
                p.status
            FROM peminjaman p
            LEFT JOIN anggota a ON p.anggota_id = a.id
            LEFT JOIN buku b ON p.buku_id = b.id
            WHERE p.id = ?");

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);

        if (!$r) {
            error_log(mysqli_error($koneksi));

            sendResponse(
                false,
                "terjadi kesalahan pada server",
                null,
                500
            );
    }

        $data = mysqli_fetch_assoc($r);

        if (!$data) {
            sendResponse(false, "Peminjaman tidak ditemukan",
            null, 404);
        }

        sendResponse(true, "berhasil", $data, 200);
    }

    //search
    if (isset($_GET['search'])) {

    $search = $_GET['search'] ?? "";

    $term = "%" . $search . "%";

    $stmt = mysqli_prepare($koneksi,
            "SELECT
                p.id,
                p.kd_peminjaman,
                a.nama AS nama_anggota,
                b.judul AS judul_buku,
                p.tanggal_pinjam,
                p.tanggal_kembali,
                p.status
            FROM peminjaman p
            LEFT JOIN anggota a ON p.anggota_id = a.id
            LEFT JOIN buku b ON p.buku_id = b.id
            WHERE p.kd_peminjaman LIKE ?
            OR a.nama LIKE ?
            OR b.judul LIKE ?
            ORDER BY p.id DESC");

            mysqli_stmt_bind_param($stmt, "sss", $term, $term, $term);
            mysqli_stmt_execute($stmt);

            $r = mysqli_stmt_get_result($stmt);

            if (!$r) {
            error_log(mysqli_error($koneksi));

            sendResponse(
                false,
                "terjadi kesalahan pada server",
                null,
                500
            );
    }

        $data = [];

        while ($row = mysqli_fetch_assoc($r)) {
            $data[] = $row;
    }

        sendResponse(
            true,
            "berhasil",
            $data,
            200);
        }

        //page
    if (isset($_GET['page']) || isset($_GET['limit'])) {

        $page = isset($_GET['page'])
            ? (int) $_GET['page']
            : 1;

        $limit = isset($_GET['limit'])
            ? (int) $_GET['limit']
            : 10;

        if ($page < 1) {
            $page = 1;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $offset = ($page - 1) * $limit;

        $q = "SELECT
                p.id,
                p.kd_peminjaman,
                a.nama AS nama_anggota,
                b.judul AS judul_buku,
                p.tanggal_pinjam,
                p.tanggal_kembali,
                p.status
            FROM peminjaman p
            LEFT JOIN anggota a ON p.anggota_id = a.id
            LEFT JOIN buku b ON p.buku_id = b.id
            ORDER BY p.id DESC
            LIMIT $limit OFFSET $offset";

            $r = mysqli_query($koneksi, $q);

            if (!$r) {
            error_log(mysqli_error($koneksi));

            sendResponse(
                false,
                "terjadi kesalahan pada server",
                null,
                500
            );
            }

            $data = [];

            while ($row = mysqli_fetch_assoc($r)) {
            $data[] = $row;
    }

            sendResponse(
                true,
                "berhasil",
                $data,
                200);
    }

            default:
            sendResponse(false, "method tidak diijinkan", null, 405);
            break;
}