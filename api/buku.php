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