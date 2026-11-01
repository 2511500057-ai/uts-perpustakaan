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