<?php
session_start();
require '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header("Location: kembali.php");
    exit;
}

$db = new Database();
$koneksi = $db->koneksi;

$id_transaksi = $_POST['id'];
$jumlah_kembali = (int)$_POST['jumlah_kembali'];

// 1. Cek data transaksi awal
$query = $koneksi->query("SELECT d.jumlah, d.id_barang FROM detail_transaksi d WHERE d.id_transaksi = '$id_transaksi'");
$detail = $query->fetch_assoc();
$jumlah_awal = (int)$detail['jumlah'];

if ($jumlah_kembali >= $jumlah_awal) {
    // JIKA KEMBALI SEMUA: Ubah status transaksi jadi menunggu pengecekan
    $koneksi->query("UPDATE transaksi SET status = 'menunggu pengecekan' WHERE id = '$id_transaksi'");
} else {
    $jumlah_sisa = $jumlah_awal - $jumlah_kembali;
    $koneksi->query("UPDATE detail_transaksi SET jumlah = '$jumlah_sisa' WHERE id_transaksi = '$id_transaksi'");

    $trx = $koneksi->query("SELECT * FROM transaksi WHERE id = '$id_transaksi'")->fetch_assoc();
    $id_user = $trx['id_pengguna'];
    $tgl_pinjam = $trx['tanggal_pinjam'];
    $tgl_kembali = $trx['tanggal_kembali'];
    $id_barang = $detail['id_barang'];

    $koneksi->query("INSERT INTO transaksi (id_pengguna, tanggal_pinjam, tanggal_kembali, status) 
                     VALUES ('$id_user', '$tgl_pinjam', '$tgl_kembali', 'menunggu pengecekan')");
    $new_id = $koneksi->insert_id;
    
    $koneksi->query("INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah) 
                     VALUES ('$new_id', '$id_barang', '$jumlah_kembali')");
}

header("Location: index.php?status=success");
exit;