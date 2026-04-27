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
$data = $barangModel->getById($id);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $stok = $_POST['stok'];

    $koneksi->query("UPDATE barang SET 
        nama_barang='$nama',
        jenis='$jenis',
        stok='$stok'
        WHERE id='$id'
    ");

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang | Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg">
        <a href="index.php" class="inline-flex items-center text-slate-400 hover:text-amber-400 mb-6 transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Batal & Kembali
        </a>

        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700 rounded-3xl shadow-2xl p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-500/10 rounded-full -mr-12 -mt-12 blur-2xl"></div>
            
            <div class="mb-8 relative">
                <h2 class="text-2xl font-bold text-white">Edit Data Barang</h2>
                <p class="text-slate-400 text-sm mt-1">ID Barang: <span class="text-amber-500 font-mono">#<?= $id ?></span></p>
            </div>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Nama Barang</label>
                    <input type="text" name="nama" value="<?= $data['nama_barang'] ?>" required
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Kategori/Jenis</label>
                    <div class="relative">
                        <select name="jenis" 
                            class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-xl text-white appearance-none focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all text-sm">
                            <option value="buku" <?= $data['jenis']=='buku'?'selected':'' ?>>Buku</option>
                            <option value="alat" <?= $data['jenis']=='alat'?'selected':'' ?>>Alat Elektronik</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Jumlah Stok</label>
                    <input type="number" name="stok" value="<?= $data['stok'] ?>" required
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-600 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                </div>

                <div class="pt-4">
                    <button name="update" 
                        class="w-full py-4 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl shadow-lg shadow-amber-500/20 transition-all transform active:scale-[0.98] flex items-center justify-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center mt-8 text-slate-600 text-[10px] tracking-widest uppercase italic">
            Perubahan data akan langsung tercatat di database
        </p>
    </div>

</body>
</html>