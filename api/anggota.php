<?php

require_once "../config.php";
require_once "../helpers/response.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
        case 'GET':

            //id
            if (isset($_GET['id'])) {

        //pertahanan pertama: cast (int) membuang karakter jahat
        $id = (int) ($_GET['id'] ?? 0);

        //pertahanan kedua: prepare-data dikirim sebagai '?'
        //db menganggapnya murni data, bukan perintah sql

        $stmt = mysqli_prepare($koneksi,
            "SELECT
                id,
                kd_anggota,
                nama,
                jenis_kelamin,
                no_hp,
                alamat,
                email
            FROM anggota
            WHERE id = ?");

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
            sendResponse(false, "anggota tidak ditemukan", null, 404);
        }

        sendResponse(true, "berhasil", $data, 200);
    }

    //search
    if (isset($_GET['search'])) {

        $search = $_GET['search'] ?? "";

        $term = "%" . $search . "%";

        $stmt = mysqli_prepare($koneksi,
            "SELECT
                id,
                kd_anggota,
                nama,
                jenis_kelamin,
                no_hp,
                alamat,
                email
            FROM anggota
            WHERE kd_anggota LIKE ?
            OR nama LIKE ?
            ORDER BY id DESC");

        mysqli_stmt_bind_param($stmt, "ss", $term, $term);
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
            200
        );
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

        $stmt = mysqli_prepare($koneksi,
            "SELECT
                id,
                kd_anggota,
                nama,
                jenis_kelamin,
                no_hp,
                alamat,
                email
            FROM anggota
            ORDER BY id DESC
            LIMIT ? OFFSET ?");

        mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
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
            200
        );
    }

    //get all
    $stmt = mysqli_prepare($koneksi,
        "SELECT
            id,
            kd_anggota,
            nama,
            jenis_kelamin,
            no_hp,
            alamat,
            email
        FROM anggota
        ORDER BY id DESC");

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

    sendResponse(true, "berhasil", $data, 200);

    break;


    case 'POST':

        //post
        $input = json_decode(file_get_contents("php://input"), true);

        //cek apakah input dan field ada
        if (
            !$input ||
            !isset($input['kd_anggota']) ||
            !isset($input['nama']) ||
            !isset($input['jenis_kelamin']) ||
            !isset($input['no_hp']) ||
            !isset($input['alamat']) ||
            !isset($input['email'])
        ) {
            sendResponse(false, "field kd_anggota, nama, jenis_kelamin, no_hp, alamat, email wajib ada", null, 400);
        }

        $kd_anggota = $input['kd_anggota'];
        $nama = $input['nama'];
        $jenis_kelamin = $input['jenis_kelamin'];
        $no_hp = $input['no_hp'];
        $alamat = $input['alamat'];
        $email = $input['email'];

        //cek kd_anggota apakah unik atau tidak
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM anggota WHERE kd_anggota = ?");
        mysqli_stmt_bind_param($stmt, "s", $kd_anggota);
        mysqli_stmt_execute($stmt);
        $cek_kd = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek_kd) > 0) {
            sendResponse(false, "kd_anggota sudah terdaftar", null, 409);
        }

        //cek email apakah unik atau tidak
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM anggota WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $cek_email = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek_email) > 0) {
            sendResponse(false, "email sudah terdaftar", null, 409);
        }

        //insert data
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO anggota (kd_anggota, nama, jenis_kelamin, no_hp, alamat, email)
            VALUES (?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "ssssss", $kd_anggota, $nama, $jenis_kelamin, $no_hp, $alamat, $email);

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
        if (
            !$input ||
            !isset($input['kd_anggota']) ||
            !isset($input['nama']) ||
            !isset($input['jenis_kelamin']) ||
            !isset($input['no_hp']) ||
            !isset($input['alamat']) ||
            !isset($input['email'])
        ) {
            sendResponse(false, "field kd_anggota, nama, jenis_kelamin, no_hp, alamat, email wajib ada", null, 400);
        }

        //validasi id ketika id tidak ditambahkan di url
        if (!isset($_GET['id']) || !is_numeric($_GET['id']) || (int) $_GET['id'] <= 0) {
            sendResponse(false, "id wajib diisi", null, 400);
        }

        $id = (int) $_GET['id'];

        $kd_anggota = $input['kd_anggota'];
        $nama = $input['nama'];
        $jenis_kelamin = $input['jenis_kelamin'];
        $no_hp = $input['no_hp'];
        $alamat = $input['alamat'];
        $email = $input['email'];

        //cek apakah anggota dengan id ada?
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM anggota WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $cek_anggota = mysqli_stmt_get_result($stmt);

        //jika tidak ada error 404
        if (mysqli_num_rows($cek_anggota) == 0) {
            sendResponse(false, "anggota tidak ditemukan", null, 404);
        }

        //cek kd_anggota apakah unik atau tidak
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM anggota WHERE kd_anggota = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $kd_anggota, $id);
        mysqli_stmt_execute($stmt);
        $cek_kd = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek_kd) > 0) {
            sendResponse(false, "kd_anggota sudah terdaftar", null, 409);
        }

        //cek email apakah unik atau tidak
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM anggota WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $email, $id);
        mysqli_stmt_execute($stmt);
        $cek_email = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($cek_email) > 0) {
            sendResponse(false, "email sudah terdaftar", null, 409);
        }

        //update data
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE anggota SET
            kd_anggota = ?,
            nama = ?,
            jenis_kelamin = ?,
            no_hp = ?,
            alamat = ?,
            email = ?
            WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ssssssi", $kd_anggota, $nama, $jenis_kelamin, $no_hp, $alamat, $email, $id);

        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            sendResponse(true, "data berhasil diperbaharui", ["id" => $id], 200);
        } else {
            error_log(mysqli_error($koneksi));
            sendResponse(false, "terjadi kesalahan pada server", null, 500);
        }

    break;


    case 'DELETE':

        //validasi id ketika id tidak ditambahkan di url
        if (!isset($_GET['id']) || !is_numeric($_GET['id']) || (int) $_GET['id'] <= 0) {
            sendResponse(false, "id wajib diisi", null, 400);
        }

        //delete disini
        $id = (int) $_GET['id'];

        //cek apakah anggota dengan id ada?
        $stmt = mysqli_prepare($koneksi, "SELECT id FROM anggota WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $cek_anggota = mysqli_stmt_get_result($stmt);

        //jika tidak ada error 404
        if (mysqli_num_rows($cek_anggota) == 0) {
            sendResponse(false, "anggota tidak ditemukan", null, 404);
        }

        //query delete
        $stmt = mysqli_prepare($koneksi, "DELETE FROM anggota WHERE id = ?");

        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            sendResponse(true, "data berhasil dihapus", ["id" => $id], 200);
        } else {
            error_log(mysqli_error($koneksi));
            sendResponse(false, "terjadi kesalahan pada server", null, 500);
        }

    break;

    default:
        sendResponse(false, "method tidak diijinkan", null, 405);
    break;
}