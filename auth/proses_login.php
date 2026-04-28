<?php
session_start();
require '../config/Database.php';
require '../models/Log.php';

$db = new Database();
$koneksi = $db->koneksi;
$log = new Log($koneksi);

if (isset($_POST['login'])) {
    $identifier = mysqli_real_escape_string($koneksi, $_POST['identifier']);
    $password = $_POST['password'];

    $query = "SELECT * FROM pengguna WHERE username = '$identifier' OR email = '$identifier'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id']    = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role']  = $row['role'];

            $log->add($row['id'], "Berhasil login ke sistem", $row['role']);

            if ($row['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } elseif ($row['role'] == 'petugas') {
                header("Location: ../petugas/index.php");
            } else {
                header("Location: ../siswa/index.php");
            }
            exit;
        } else {
            $_SESSION['error'] = "Password salah!";
        }
    } else {
        $_SESSION['error'] = "Akun tidak ditemukan!";
    }
    
    header("Location: login.php");
    exit;
}