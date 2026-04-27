<?php
session_start();

require '../config/Database.php';
require '../models/Pengguna.php';

$db = new Database();
$koneksi = $db->koneksi;

$pengguna = new Pengguna($koneksi);

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = $pengguna->login($username);

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['login'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        if ($user['role'] == 'admin') {
            header("Location: ../admin/index.php");
        } elseif ($user['role'] == 'petugas') {
            header("Location: ../petugas/index.php");
        } else {
            header("Location: ../siswa/index.php");
        }

    } else {
        echo "<script>alert('Login gagal');window.location='login.php';</script>";
    }
}
?>