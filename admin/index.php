<?php
session_start();

require '../config/Database.php';
require '../models/Pengguna.php';

$db = new Database();
$koneksi = $db->koneksi;
$pengguna = new Pengguna($koneksi);

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_SESSION['id'];
$user = $pengguna->getById($id);

$countBarang = $koneksi->query("SELECT SUM(stok) as total FROM barang")->fetch_assoc();
$totalBukuFisik = $countBarang['total'] ?? 0;

$countTransaksi = $koneksi->query("SELECT COUNT(*) as total FROM transaksi WHERE status='menunggu'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #080b14;
            color: #cbd5e1;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }
        .text-gradient {
            background: linear-gradient(to r, #3b82f6, #818cf8, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen">

    <nav class="border-b border-white/5 bg-[#080b14]/80 backdrop-blur-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-5 flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold text-white tracking-tighter">
                BIBLIO<span class="text-blue-500">TECH</span>
            </a>
            
            <div class="flex items-center gap-6">
                <div class="flex flex-col items-end hidden sm:flex">
                    <span class="text-xs font-bold text-white uppercase tracking-widest"><?= $user['username'] ?></span>
                    <span class="text-[9px] text-blue-500 font-black uppercase">Administrator</span>
                </div>
                <a href="../auth/logout.php" class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 border border-rose-500/20 hover:bg-rose-500 hover:text-white transition shadow-lg shadow-rose-500/5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </a>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-6 py-12">
<header class="mb-12">
            <h2 class="text-4xl font-extrabold text-white tracking-tight">Halo, <?= $user['username'] ?> 👋</h2>
            <p class="text-slate-500 mt-2 font-light italic">Pantau aktivitas perpustakaan dan kelola stok buku fisik sekolah hari ini.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <div class="glass-card p-10 rounded-[3rem] flex justify-between items-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-blue-600/5 blur-3xl rounded-full"></div>
                <div class="z-10">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Jumlah Buku</p>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-7xl font-black text-white"><?= $totalBukuFisik ?></h2>
                    </div>
                </div>
                <div class="h-24 w-24 rounded-[2rem] bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-blue-500 shadow-[0_0_50px_rgba(59,130,246,0.1)] z-10 group-hover:scale-110 transition-transform duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>

            <div class="glass-card p-10 rounded-[3rem] flex justify-between items-center relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-amber-600/5 blur-3xl rounded-full"></div>
                <div class="z-10">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Jumlah Antrean</p>
                    <div class="flex items-baseline gap-2">
                        <h2 class="text-7xl font-black text-white"><?= $countTransaksi['total'] ?></h2>
                    </div>
                </div>
                <div class="h-24 w-24 rounded-[2rem] bg-amber-600/20 border border-amber-500/30 flex items-center justify-center text-amber-500 shadow-[0_0_50px_rgba(245,158,11,0.1)] z-10 group-hover:scale-110 transition-transform duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="../barang/index.php" class="glass-card p-8 rounded-[2.5rem] border-blue-500/30 bg-blue-600/10 hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center group">
                <div class="h-16 w-16 rounded-2xl bg-blue-600/20 mb-5 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">📚</div>
                <span class="text-[11px] font-black text-blue-400 uppercase tracking-widest">Katalog Buku</span>
            </a>
            <a href="../transaksi/index.php" class="glass-card p-8 rounded-[2.5rem] border-indigo-500/30 bg-indigo-600/10 hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center group">
                <div class="h-16 w-16 rounded-2xl bg-indigo-600/20 mb-5 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">📄</div>
                <span class="text-[11px] font-black text-indigo-400 uppercase tracking-widest">Input Pinjam</span>
            </a>
            <a href="../report/index.php" class="glass-card p-8 rounded-[2.5rem] border-emerald-500/30 bg-emerald-600/10 hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center group">
                <div class="h-16 w-16 rounded-2xl bg-emerald-600/20 mb-5 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">📊</div>
                <span class="text-[11px] font-black text-emerald-400 uppercase tracking-widest">Cek Laporan</span>
            </a>
            <a href="../pengguna/index.php" class="glass-card p-8 rounded-[2.5rem] border-rose-500/30 bg-rose-600/10 hover:-translate-y-2 transition-all duration-300 text-center flex flex-col items-center group">
                <div class="h-16 w-16 rounded-2xl bg-rose-600/20 mb-5 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">👥</div>
                <span class="text-[11px] font-black text-rose-400 uppercase tracking-widest">Kelola Staff</span>
            </a>
        </div>
    </main>
</body>
</html>