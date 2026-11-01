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

    //get all
            $q = "SELECT p.id,
            p.kd_peminjaman,
            a.nama AS nama_anggota,
            b.judul AS judul_buku,
            p.tanggal_pinjam,
            p.tanggal_kembali,
            p.status
            FROM peminjaman p
            LEFT JOIN anggota a ON p.anggota_id = a.id
            LEFT JOIN buku b ON p.buku_id = b.id
            ORDER BY p.id DESC";

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

                sendResponse(true, "berhasil", $data, 200);

                break;

                case 'POST':
                    //post
                    $input = json_decode(file_get_contents("php://input"), true);

                    //cek apakah input dan field ada
                    if (!$input ||
                    !isset($input['kd_peminjaman']) ||
                    !isset($input['anggota_id']) ||
                    !isset($input['buku_id']) ||
                    !isset($input['tanggal_pinjam']) ||
                    !isset($input['tanggal_kembali']) ||
                    !isset($input['status'])
                    ) {
                        sendResponse(false, "field kd_peminjaman, anggota_id, buku_id, tanggal_pinjam, tanggal_kembali, status wajib ada", null, 400);
                    }

                    $kd_peminjaman = $input['kd_peminjaman'];
                    $anggota_id = (int) $input['anggota_id'];
                    $buku_id = (int) $input['buku_id'];
                    $tanggal_pinjam = $input['tanggal_pinjam'];
                    $tanggal_kembali = $input['tanggal_kembali'];
                    $status = $input['status'];

                    //cek anggota apakah ada
                    $cek = mysqli_query($koneksi, "SELECT id From anggota WHERE id='$anggota_id'");
                    if (mysqli_num_rows($cek) == 0) {
                        sendResponse(false, "Anggota_id tidak ditemukan", null, 404);
                    }

                    //cek buku apakah ada
                    $cek = mysqli_query($koneksi, "SELECT id From buku WHERE id='$buku_id'");
                    if (mysqli_num_rows($cek) == 0) {
                        sendResponse(false, "Buku_id tidak ditemukan", null, 404);
                    }

                    //cek kd_peminjaman apakah unik atau tidak
                    $cek_kd = mysqli_query($koneksi, "SELECT id From peminjaman WHERE kd_peminjaman='$kd_peminjaman'");
                    if (mysqli_num_rows($cek_kd) > 0) {
                        sendResponse(false, "Kd peminjaman sudah terdaftar", null, 409);
                    }

                    //insert data
                    $stmt = mysqli_prepare(
                    $koneksi,
                    "INSERT INTO peminjaman (kd_peminjaman, anggota_id, buku_id, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    mysqli_stmt_bind_param($stmt, "siisss", $kd_peminjaman, $anggota_id, $buku_id, $tanggal_pinjam, $tanggal_kembali, $status);

                    if (mysqli_stmt_execute($stmt)) {
                        sendResponse(true, "data tersimpan", ["id" => mysqli_insert_id($koneksi)], 201);
                    } else {
                        error_log(mysqli_error($koneksi));
                        sendResponse(false, "terjadi kesalahan pada server", null, 500);
                    }

                break;

                case 'PUT':
                    //put
                    $input = json_decode(file_get_contents("php://input"), true);

                    //cek apakah input ada dan field ada
                    if (!$input ||
                    !isset($input['kd_peminjaman']) ||
                    !isset($input['anggota_id']) ||
                    !isset($input['buku_id']) ||
                    !isset($input['tanggal_pinjam']) ||
                    !isset($input['tanggal_kembali']) ||
                    !isset($input['status'])
                    ) {
                        sendResponse(false, "field kd_peminjaman, anggota_id, buku_id, tanggal_pinjam, tanggal_kembali, status wajib ada", null, 400);
                    }
                    
                    $id = (int) $_GET['id'];

                    $kd_peminjaman = $input['kd_peminjaman'];
                    $anggota_id = (int) $input['anggota_id'];
                    $buku_id = (int) $input['buku_id'];
                    $tanggal_pinjam = $input['tanggal_pinjam'];
                    $tanggal_kembali = $input['tanggal_kembali'];
                    $status = $input['status'];

                    // cek apakah peminjaman dengan id ada?
                    $cek_peminjaman = mysqli_query($koneksi, "SELECT id FROM peminjaman WHERE id = '$id'");
                    //jika tidak ad error 404
                    if(mysqli_num_rows ($cek_peminjaman) == 0) {
                        sendResponse(false, "peminjaman tidak ditemukan", null, 404);
                    }

                    //cek anggota apakah ada
                    $cek = mysqli_query($koneksi, "SELECT id From anggota WHERE id='$anggota_id'");
                    if (mysqli_num_rows($cek) == 0) {
                        sendResponse(false, "Anggota_id tidak ditemukan", null, 404);
                    }

                    //cek buku apakah ada
                    $cek = mysqli_query($koneksi, "SELECT id From buku WHERE id='$buku_id'");
                    if (mysqli_num_rows($cek) == 0) {
                        sendResponse(false, "Buku_id tidak ditemukan", null, 404);
                    }

                    //cek kd_peminjaman apakah unik atau tidak
                    $cek_kd = mysqli_query($koneksi, "SELECT id From peminjaman WHERE kd_peminjaman='$kd_peminjaman' AND id != '$id'");
                    if (mysqli_num_rows($cek_kd) > 0) {
                        sendResponse(false, "Kd peminjaman sudah terdaftar", null, 409);
                    }

                    //update data
                    $stmt = mysqli_prepare(
                        $koneksi,
                        "UPDATE peminjaman SET
                        kd_peminjaman = ?,
                        anggota_id = ?,
                        buku_id = ?,
                        tanggal_pinjam = ?,
                        tanggal_kembali = ?,
                        status = ?
                        WHERE id = ?"
                    );

                    mysqli_stmt_bind_param($stmt, "siisssi", $kd_peminjaman, $anggota_id, $buku_id, $tanggal_pinjam, $tanggal_kembali, $status, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        sendResponse(true, "data berhasil diperbaharui", ["id" => $id], 200);
                    } else {
                        error_log(mysqli_error($koneksi));
                        sendResponse(false, "terjadi kesalahan pada server", null, 500);
                    }

                break;

            default:
            sendResponse(false, "method tidak diijinkan", null, 405);
            break;
}