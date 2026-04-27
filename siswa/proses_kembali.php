<?php
session_start();

require '../config/Database.php';
require '../models/Transaksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$transaksi = new Transaksi($koneksi);

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

$detail = $koneksi->query("
    SELECT d.*, b.id as id_barang 
    FROM detail_transaksi d
    JOIN barang b ON d.id_barang = b.id
    WHERE d.id_transaksi='$id'
");

while ($d = $detail->fetch_assoc()) {
    $koneksi->query("
        UPDATE barang 
        SET stok = stok + {$d['jumlah']} 
        WHERE id='{$d['id_barang']}'
    ");
}

$koneksi->query("
    UPDATE transaksi 
    SET status='kembali' 
    WHERE id='$id'
");

header("Location: kembali.php");