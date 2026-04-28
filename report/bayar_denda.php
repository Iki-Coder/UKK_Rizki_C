<?php
require '../config/Database.php';

$db = new Database();
$koneksi = $db->koneksi;

$id = $_GET['id'];

$koneksi->query("
    UPDATE transaksi 
    SET denda_bayar = 1 
    WHERE id = '$id'
");

header("Location: index.php");