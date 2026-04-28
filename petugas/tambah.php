<?php
session_start();
require '../config/Database.php';
require '../models/Transaksi.php';
require '../models/Pengguna.php';

$db = new Database();
$koneksi = $db->koneksi;
$transaksi = new Transaksi($koneksi);
$pengguna = new Pengguna($koneksi);

if (!isset($_SESSION['login']) || ($_SESSION['role'] != 'petugas' && $_SESSION['role'] != 'admin')) {
    header("Location: ../auth/login.php");
    exit;
}

$id_siswa = $_GET['id'];
$siswa = $pengguna->getById($id_siswa);

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

if (isset($_GET['tambah'])) {
    $id_barang = $_GET['tambah'];
    $cek = $koneksi->query("SELECT stok FROM barang WHERE id='$id_barang'")->fetch_assoc();
    if ($cek['stok'] > 0) {
        if (isset($_SESSION['keranjang'][$id_barang])) {
            $_SESSION['keranjang'][$id_barang]++;
        } else {
            $_SESSION['keranjang'][$id_barang] = 1;
        }
    }
}

if (isset($_GET['kurang'])) {
    $id_barang = $_GET['kurang'];
    if (isset($_SESSION['keranjang'][$id_barang])) {
        if ($_SESSION['keranjang'][$id_barang] > 1) {
            $_SESSION['keranjang'][$id_barang]--;
        } else {
            unset($_SESSION['keranjang'][$id_barang]);
        }
    }
}

if (isset($_POST['simpan'])) {
    $tgl_kembali = $_POST['kembali'];
    $id_transaksi = $transaksi->buatTransaksi($id_siswa, $tgl_kembali);
    foreach ($_SESSION['keranjang'] as $id_barang => $jumlah) {
        $transaksi->tambahBarang($id_transaksi, $id_barang, $jumlah);
    }
    $_SESSION['keranjang'] = [];
    header("Location: scan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Pinjam | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #0b0e1a;
            color: #f1f5f9;
        }
        .card-glass {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.5);
            border-radius: 10px;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</head>
<body class="min-h-screen p-6 lg:p-12">

    <div class="max-w-7xl mx-auto">
        <div class="bg-blue-600 rounded-[2.5rem] p-8 mb-10 shadow-2xl shadow-blue-600/20 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
            
            <div class="flex items-center gap-6 relative z-10">
                <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center backdrop-blur-xl border border-white/20 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tighter uppercase"><?= $siswa['username'] ?></h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-3 py-1 bg-black/20 rounded-full text-[10px] font-black text-blue-100 uppercase tracking-widest border border-white/10">Siswa ID: #<?= $siswa['id'] ?></span>
                    </div>
                </div>
            </div>
            
            <a href="scan.php" class="relative z-10 px-8 py-4 bg-black/20 hover:bg-black/30 rounded-2xl text-white text-[11px] font-black uppercase tracking-[0.2em] transition-all border border-white/10 backdrop-blur-md flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Batal
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                <div class="card-glass rounded-[3rem] overflow-hidden shadow-2xl">
                    <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/5">
                        <h2 class="text-xl font-bold text-white tracking-tight">Daftar Inventaris</h2>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-[0.3em]">Klik + Untuk Memasukkan Keranjang</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-white/5 text-slate-500 text-[10px] font-black uppercase tracking-widest">
                                <tr>
                                    <th class="px-8 py-5">Nama Barang</th>
                                    <th class="px-8 py-5 text-center">Stok</th>
                                    <th class="px-8 py-5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <?php 
                                $barang = $koneksi->query("SELECT * FROM barang ORDER BY nama_barang ASC");
                                while($b = $barang->fetch_assoc()) { ?>
                                <tr class="hover:bg-white/5 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-white uppercase tracking-tight"><?= $b['nama_barang'] ?></div>
                                        <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mt-1"><?= $b['jenis'] ?></div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="px-4 py-2 bg-slate-900 rounded-xl text-xs font-bold border border-white/5 text-blue-400"><?= $b['stok'] ?></span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="?id=<?= $id_siswa ?>&tambah=<?= $b['id'] ?>" 
                                           class="inline-flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl shadow-xl shadow-blue-600/30 transition-all active:scale-90">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="sticky top-12">
                    <div class="card-glass rounded-[3rem] overflow-hidden shadow-2xl flex flex-col border border-blue-500/10">
                        <div class="p-8 bg-blue-600/10 border-b border-white/5 flex items-center justify-between">
                            <h3 class="font-black text-white text-xs uppercase tracking-widest flex items-center gap-3">
                                <span class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></span>
                                Keranjang Pinjam
                            </h3>
                            <span class="text-[10px] font-black text-blue-400 bg-blue-400/10 px-3 py-1 rounded-full border border-blue-400/20">
                                <?= count($_SESSION['keranjang']) ?> Items
                            </span>
                        </div>
                        
                        <div class="p-6 space-y-4 max-h-[50vh] overflow-y-auto custom-scrollbar">
                            <?php if (empty($_SESSION['keranjang'])) : ?>
                                <div class="py-16 text-center">
                                    <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 opacity-20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Keranjang Kosong</p>
                                </div>
                            <?php endif; ?>
                            
                            <?php foreach ($_SESSION['keranjang'] as $id_barang => $jumlah) { 
                                $b = $koneksi->query("SELECT nama_barang FROM barang WHERE id='$id_barang'")->fetch_assoc();
                            ?>
                            <div class="flex items-center justify-between bg-white/5 p-5 rounded-[2rem] border border-white/5 hover:border-blue-500/30 transition-all group">
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate uppercase tracking-tight"><?= $b['nama_barang'] ?></h4>
                                    <p class="text-[10px] text-blue-500 font-black mt-1 uppercase tracking-widest">Qty: <?= $jumlah ?></p>
                                </div>
                                <div class="flex gap-2 ml-4">
                                    <a href="?id=<?= $id_siswa ?>&kurang=<?= $id_barang ?>" class="w-9 h-9 flex items-center justify-center bg-slate-800 hover:bg-rose-500 text-white rounded-xl transition-all border border-white/5 font-black">-</a>
                                    <a href="?id=<?= $id_siswa ?>&tambah=<?= $id_barang ?>" class="w-9 h-9 flex items-center justify-center bg-slate-800 hover:bg-emerald-500 text-white rounded-xl transition-all border border-white/5 font-black">+</a>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <?php if (!empty($_SESSION['keranjang'])) : ?>
                        <div class="p-8 bg-black/20 border-t border-white/5">
                            <form method="POST" class="space-y-6">
                                <div>
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3 block px-1">Estimasi Tanggal Kembali</label>
                                    <input type="date" name="kembali" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                        class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none transition-all">
                                </div>
                                <button name="simpan" class="w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-[11px] uppercase tracking-[0.2em] rounded-2xl shadow-2xl shadow-emerald-600/30 transition-all transform active:scale-95 flex items-center justify-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Konfirmasi Pinjaman
                                </button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>