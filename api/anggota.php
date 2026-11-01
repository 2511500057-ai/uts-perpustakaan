<?php

require_once "../config.php";
require_once "../helpers/response.php";

//id
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $q = "SELECT
            id,
            kd_anggota,
            nama,
            jenis_kelamin,
            no_hp,
            alamat,
            email
        FROM anggota
        WHERE id = '$id'";


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

    if (!$data) { //jika data ang tdk ad
        sendResponse(false, "anggota tidak ditemukan",
        null, 404);
    }

    sendResponse(true, "berhasil", $data, 200);
}


//search
if (isset($_GET['search'])) {

$search = $_GET['search'];

$q = "SELECT
            id,
            kd_anggota,
            nama,
            jenis_kelamin,
            no_hp,
            alamat,
            email
        FROM anggota
        WHERE nama LIKE '%$search%'
        OR kd_anggota LIKE '%$search%'
        ORDER BY id DESC";

        $r = mysqli_query($koneksi, $q);

        if (!$r) {
        sendResponse(
            false,
            "query gagal: " . mysqli_error($koneksi),
            null,
            500
        );
}

    //tampung data, karena data lebih dari 1
    $data = [];

    //masukkan data kedalam $data
    while ($row = mysqli_fetch_assoc($r)) {
        $data[] = $row;
}

//kirim response
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

        //page dan limit boleh kurang dari 1
    if ($page < 1) {
        $page = 1; //nilai default
    }

    if ($limit < 1) {
        $limit = 10;
    }

    $offset = ($page - 1) * $limit;

   $q = "SELECT
            id,
            kd_anggota,
            nama,
            jenis_kelamin,
            no_hp,
            alamat,
            email
        FROM anggota
        ORDER BY id DESC
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
            200);
}

$q = "SELECT
            id,
            kd_anggota,
            nama,
            jenis_kelamin,
            no_hp,
            alamat,
            email
        FROM anggota
        ORDER BY id DESC";

$r = mysqli_query($koneksi, $q);

if (!$r) sendResponse(false, "query gagal: " . mysqli_error($koneksi), null, 500);

$data = [];
while ($row = mysqli_fetch_assoc($r)) {
    $data[] = $row;
}

sendResponse(true, "berhasil", $data, 200);