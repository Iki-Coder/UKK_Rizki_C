<?php
session_start();
require '../config/Database.php';

$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_transaksi = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : null;

if ($id_transaksi) {
    $id_user = $_SESSION['id'];
    
    $cek = $koneksi->query("SELECT * FROM transaksi WHERE id='$id_transaksi' AND id_pengguna='$id_user' AND status='dipinjam'");

    if ($cek->num_rows > 0) {
        $query = "UPDATE transaksi SET status='menunggu pengecekan' WHERE id='$id_transaksi'";
        
        if ($koneksi->query($query)) {
            // DIUBAH: Mengarah ke kembali.php sesuai nama file yang kamu punya
            header("Location: kembali.php?status=pending");
            exit;
        }
    }
}

// DIUBAH: Mengarah ke kembali.php
header("Location: kembali.php");
exit;