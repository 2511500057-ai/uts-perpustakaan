
<?php

require_once "../config.php";
require_once "../helpers/response.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':

        // GET berdasarkan ID
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
                sendResponse(false, "query gagal: " . mysqli_error($koneksi), null, 500);
            }

            $data = mysqli_fetch_assoc($r);

            if (!$data) {
                sendResponse(false, "anggota tidak ditemukan", null, 404);
            }

            sendResponse(true, "berhasil", $data, 200);

        }

        // GET berdasarkan pencarian
        elseif (isset($_GET['search'])) {

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
                sendResponse(false, "query gagal: " . mysqli_error($koneksi), null, 500);
            }

            $data = [];

            while ($row = mysqli_fetch_assoc($r)) {
                $data[] = $row;
            }

            sendResponse(true, "berhasil", $data, 200);

        }

        // GET dengan pagination
        elseif (isset($_GET['page']) || isset($_GET['limit'])) {

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
                sendResponse(false, "query gagal: " . mysqli_error($koneksi), null, 500);
            }

            $data = [];

            while ($row = mysqli_fetch_assoc($r)) {
                $data[] = $row;
            }

            sendResponse(true, "berhasil", $data, 200);

        }

        // GET semua data
        else {

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

            if (!$r) {
                sendResponse(false, "query gagal: " . mysqli_error($koneksi), null, 500);
            }

            $data = [];

            while ($row = mysqli_fetch_assoc($r)) {
                $data[] = $row;
            }

            sendResponse(true, "berhasil", $data, 200);

        }

    break;


    case 'POST':

        // POST
        $input = json_decode(file_get_contents("php://input"), true);

        // cek field wajib
        if (
            !$input ||
            !isset($input['kd_anggota']) ||
            !isset($input['nama']) ||
            !isset($input['jenis_kelamin']) ||
            !isset($input['no_hp']) ||
            !isset($input['alamat']) ||
            !isset($input['email'])
        ) {
            sendResponse(false, "field kd_anggota, nama, jenis_kelamin, no_hp, alamat, email wajib", null, 400);
        }

        $kd_anggota = $input['kd_anggota'];
        $nama = $input['nama'];
        $jenis_kelamin = $input['jenis_kelamin'];
        $no_hp = $input['no_hp'];
        $alamat = $input['alamat'];
        $email = $input['email'];

        // cek jenis kelamin
        if (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
            sendResponse(false, "jenis_kelamin harus Laki-laki atau Perempuan", null, 400);
        }

        // cek kd_anggota
        $cek = mysqli_query($koneksi, "SELECT id FROM anggota WHERE kd_anggota='$kd_anggota'");

        if (mysqli_num_rows($cek) > 0) {
            sendResponse(false, "kd_anggota sudah terdaftar", null, 409);
        }

        // cek email
        $cek_email = mysqli_query($koneksi, "SELECT id FROM anggota WHERE email='$email'");

        if (mysqli_num_rows($cek_email) > 0) {
            sendResponse(false, "email sudah terdaftar", null, 409);
        }

        // insert data
        $q = "INSERT INTO anggota
              (kd_anggota, nama, jenis_kelamin, no_hp, alamat, email)
              VALUES
              ('$kd_anggota', '$nama', '$jenis_kelamin', '$no_hp', '$alamat', '$email')";

        if (mysqli_query($koneksi, $q)) {
            sendResponse(true, "data tersimpan", ["id" => mysqli_insert_id($koneksi)], 201);
        } else {
            sendResponse(false, mysqli_error($koneksi), null, 500);
        }

    break;


    // method tidak diijinkan
    default:

        sendResponse(false, "method tidak diijinkan", null, 405);

    break;

}
