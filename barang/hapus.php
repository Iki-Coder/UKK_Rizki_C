<?php
session_start();

require '../config/Database.php';
require '../models/Barang.php';

$db = new Database();
$koneksi = $db->koneksi;

$barangModel = new Barang($koneksi);

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];

$koneksi->query("DELETE FROM barang WHERE id='$id'");

header("Location: index.php");