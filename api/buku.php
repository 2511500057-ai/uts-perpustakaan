<?php

require_once "../config.php";
require_once "../helpers/response.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // id
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            $q = "SELECT
                    b.id,
                    b.kd_buku,
                    b.judul,
                    b.penulis,
                    b.penerbit,
                    b.tahun_terbit,
                    b.kategori,
                    b.stok
                FROM buku b
                WHERE b.id = '$id'";

            $r = mysqli_query($koneksi, $q);

            if (!$r) {
                sendResponse(
                    false,
                    "query gagal: " . mysqli_error($koneksi),
                    null,
                    500
                );
            }

            $data = mysqli_fetch_assoc($r);

            if (!$data) {
                sendResponse(
                    false,
                    "buku tidak ditemukan",
                    null,
                    404
                );
            }

            sendResponse(true, "berhasil", $data, 200);
        }

        // search
        if (isset($_GET['search'])) {
            $search = $_GET['search'];

            $q = "SELECT
                    b.id,
                    b.kd_buku,
                    b.judul,
                    b.penulis,
                    b.penerbit,
                    b.tahun_terbit,
                    b.kategori,
                    b.stok
                FROM buku b
                WHERE b.judul LIKE '%$search%'
                OR b.kd_buku LIKE '%$search%'
                OR b.penulis LIKE '%$search%'
                OR b.penerbit LIKE '%$search%'
                OR b.kategori LIKE '%$search%'
                ORDER BY b.id DESC";

            $r = mysqli_query($koneksi, $q);

            if (!$r) {
                sendResponse(
                    false,
                    "query gagal: " . mysqli_error($koneksi),
                    null,
                    500
                );
            }

            // tampung data, karena data lebih dari 1
            $data = [];

            // masukkan data kedalam $data
            while ($row = mysqli_fetch_assoc($r)) {
                $data[] = $row;
            }

            // kirim response
            sendResponse(
                true,
                "berhasil",
                $data,
                200
            );
        }

        // page
        if (isset($_GET['page']) || isset($_GET['limit'])) {
            $page = isset($_GET['page'])
                ? (int) $_GET['page']
                : 1;

            $limit = isset($_GET['limit'])
                ? (int) $_GET['limit']
                : 10;

            // page dan limit boleh kurang dari 1
            if ($page < 1) {
                $page = 1; // nilai default
            }

            if ($limit < 1) {
                $limit = 10;
            }

            $offset = ($page - 1) * $limit;

            $q = "SELECT
                    b.id,
                    b.kd_buku,
                    b.judul,
                    b.penulis,
                    b.penerbit,
                    b.tahun_terbit,
                    b.kategori,
                    b.stok
                FROM buku b
                ORDER BY b.id DESC
                LIMIT $limit OFFSET $offset";

            $r = mysqli_query($koneksi, $q);

            if (!$r) {
                sendResponse(
                    false,
                    "query gagal: " . mysqli_error($koneksi),
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
                200
            );
        }

        // get all
        $q = "SELECT b.id, b.kd_buku, b.judul, b.penulis, b.penerbit, b.tahun_terbit, b.kategori, b.stok
              FROM buku b
              ORDER BY b.id DESC";

        $r = mysqli_query($koneksi, $q);

        if (!$r) sendResponse(false, "query gagal: " . mysqli_error($koneksi), null, 500);

        $data = [];
        while ($row = mysqli_fetch_assoc($r)) {
            $data[] = $row;
        }

        sendResponse(true, "berhasil", $data, 200);
        break;

    case 'POST':
        // post
        $input = json_decode(file_get_contents("php://input"), true);

        // cek apakah input ada dan field kode buku, judul, penulis, penerbit, tahun terbit, kategori, stok ada
        if (!$input || !isset($input['kd_buku']) || !isset($input['judul']) || !isset($input['penulis']) || !isset($input['penerbit']) || !isset($input['tahun_terbit']) || !isset($input['kategori']) || !isset($input['stok'])) {
            sendResponse(false, "field kd_buku, judul, penulis, penerbit, tahun_terbit, kategori, stok wajib", null, 400);
        }

        $kd_buku = $input['kd_buku'];
        $judul = $input['judul'];
        $penulis = $input['penulis'];
        $penerbit = $input['penerbit'];
        $tahun_terbit = $input['tahun_terbit'];
        $kategori = $input['kategori'];
        $stok = $input['stok'];

        // cek kode buku apakah ada atau tidak
        $cek_kd_buku = mysqli_query($koneksi, "SELECT kd_buku FROM buku WHERE kd_buku='$kd_buku'");
        if (mysqli_num_rows($cek_kd_buku) > 0) {
            sendResponse(false, "kode buku sudah terdaftar", null, 409);
        }

        // insert data
        $q = "INSERT INTO buku (kd_buku, judul, penulis, penerbit, tahun_terbit, kategori, stok) VALUES ('$kd_buku','$judul', '$penulis', '$penerbit','$tahun_terbit', '$kategori','$stok')";

        if (mysqli_query($koneksi, $q)) {
            sendResponse(true, "data tersimpan", ["id" => mysqli_insert_id($koneksi)], 201);
        } else {
            sendResponse(false, mysqli_error($koneksi, null, 500));
        }

        break;

    // put & delete
    default:
        sendResponse(false, "method tidak diijinkan", null, 405);
        break;
}