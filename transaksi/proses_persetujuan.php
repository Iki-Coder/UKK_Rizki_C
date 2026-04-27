<?php
session_start();

require '../config/Database.php';
require '../models/Transaksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$transaksi = new Transaksi($koneksi);

if (!isset($_SESSION['login']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

$detail = $koneksi->query("
    SELECT * FROM detail_transaksi WHERE id_transaksi='$id'
");

while ($d = $detail->fetch_assoc()) {
    $koneksi->query("
        UPDATE barang 
        SET stok = stok - {$d['jumlah']} 
        WHERE id='{$d['id_barang']}'
    ");
}

$koneksi->query("
    UPDATE transaksi 
    SET status='dipinjam' 
    WHERE id='$id'
");

header("Location: persetujuan.php");