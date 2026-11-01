<?php

require_once "../config.php";
require_once "../helpers/response.php";

//id
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

    if (!$data) { //jika data buku tdk ad
        sendResponse(false, "buku tidak ditemukan",
        null, 404);
    }

    sendResponse(true, "berhasil", $data, 200);
}

//search
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
            200);
}

//get all
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