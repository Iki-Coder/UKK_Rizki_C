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

// Statistik simpel
$countBarang = $koneksi->query("SELECT COUNT(*) as total FROM barang")->fetch_assoc();
$countTransaksi = $koneksi->query("SELECT COUNT(*) as total FROM transaksi WHERE status='menunggu'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0b0f1a] text-slate-200 min-h-screen">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[30%] h-[30%] bg-blue-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="flex flex-col md:flex-row min-h-screen relative z-10">
        <aside class="w-full md:w-72 bg-[#0b0f1a]/60 backdrop-blur-xl border-r border-slate-800/50 p-8 flex flex-col">
            <div class="mb-12">
                <a href="#" class="text-2xl font-bold tracking-tighter text-white">
                    BIBLIO<span class="text-blue-500">TECH</span>
                </a>
                <p class="text-[10px] text-slate-500 uppercase tracking-[0.2em] font-bold mt-1">Admin Panel</p>
            </div>

            <nav class="flex-1 space-y-3">
                <a href="index.php" class="flex items-center gap-3 bg-blue-600/10 text-blue-400 px-5 py-3.5 rounded-2xl border border-blue-500/20 font-semibold transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Dashboard
                </a>
                <a href="../barang/index.php" class="flex items-center gap-3 text-slate-400 hover:bg-slate-800/50 hover:text-white px-5 py-3.5 rounded-2xl transition-all border border-transparent hover:border-slate-700/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    Data Buku
                </a>
                <a href="../transaksi/index.php" class="flex items-center gap-3 text-slate-400 hover:bg-slate-800/50 hover:text-white px-5 py-3.5 rounded-2xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    Peminjaman
                </a>
                <a href="../report/index.php" class="flex items-center gap-3 text-slate-400 hover:bg-slate-800/50 hover:text-white px-5 py-3.5 rounded-2xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 2v-6m10 10V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2z" /></svg>
                    Laporan
                </a>
            </nav>

            <div class="mt-auto pt-8 border-t border-slate-800/50">
                <a href="../auth/logout.php" class="flex items-center gap-3 text-rose-400 hover:bg-rose-500/10 px-5 py-3.5 rounded-2xl transition-all font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar
                </a>
            </div>
        </aside>

        <main class="flex-1 p-6 md:p-12 overflow-y-auto">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight">Halo, <?= $user['username'] ?> 👋</h2>
                    <p class="text-slate-500 mt-1">Selamat datang kembali di pusat kendali Bibliotech.</p>
                </div>
                <div class="flex items-center gap-4 bg-slate-900/50 p-2 pl-4 rounded-2xl border border-slate-800/50">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Admin Access</span>
                    <div class="h-10 w-10 bg-blue-600 rounded-xl flex items-center justify-center font-bold text-white shadow-lg shadow-blue-600/20">
                        <?= strtoupper(substr($user['username'], 0, 1)) ?>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8 mb-12">
                <div class="relative overflow-hidden bg-slate-900/40 border border-slate-800/50 p-8 rounded-[2rem] backdrop-blur-sm group hover:border-blue-500/30 transition-all">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em] mb-4">Total Koleksi Buku</p>
                    <h3 class="text-6xl font-black text-white"><?= $countBarang['total'] ?></h3>
                    <p class="text-blue-500 text-xs font-bold mt-4 uppercase tracking-widest">Fisik di rak perpus</p>
                </div>

                <div class="relative overflow-hidden bg-slate-900/40 border border-slate-800/50 p-8 rounded-[2rem] backdrop-blur-sm group hover:border-amber-500/30 transition-all">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em] mb-4">Butuh Persetujuan</p>
                    <h3 class="text-6xl font-black text-white"><?= $countTransaksi['total'] ?></h3>
                    <p class="text-amber-500 text-xs font-bold mt-4 uppercase tracking-widest">Menunggu Validasi</p>
                </div>
            </div>

            <h3 class="text-slate-500 text-[10px] font-black uppercase tracking-[0.3em] mb-6 pl-2">Menu Pintas</h3>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="../barang/index.php" class="bg-slate-900/30 border border-slate-800/50 p-8 rounded-3xl text-center hover:bg-slate-800/50 transition-all group border-b-4 border-b-blue-600/50">
                    <div class="text-3xl mb-4 bg-blue-500/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto text-blue-400 group-hover:scale-110 transition-transform">📚</div>
                    <p class="text-xs font-bold text-slate-300 group-hover:text-white uppercase tracking-widest">Input Buku</p>
                </a>
                <a href="../transaksi/index.php" class="bg-slate-900/30 border border-slate-800/50 p-8 rounded-3xl text-center hover:bg-slate-800/50 transition-all group border-b-4 border-b-indigo-600/50">
                    <div class="text-3xl mb-4 bg-indigo-500/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto text-indigo-400 group-hover:scale-110 transition-transform">📋</div>
                    <p class="text-xs font-bold text-slate-300 group-hover:text-white uppercase tracking-widest">Transaksi</p>
                </a>
                <a href="../report/index.php" class="bg-slate-900/30 border border-slate-800/50 p-8 rounded-3xl text-center hover:bg-slate-800/50 transition-all group border-b-4 border-b-emerald-600/50">
                    <div class="text-3xl mb-4 bg-emerald-500/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto text-emerald-400 group-hover:scale-110 transition-transform">📈</div>
                    <p class="text-xs font-bold text-slate-300 group-hover:text-white uppercase tracking-widest">Report</p>
                </a>
                <a href="../transaksi/persetujuan.php" class="bg-slate-900/30 border border-slate-800/50 p-8 rounded-3xl text-center hover:bg-slate-800/50 transition-all group border-b-4 border-b-rose-600/50">
                    <div class="text-3xl mb-4 bg-rose-500/10 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto text-rose-400 group-hover:scale-110 transition-transform">✅</div>
                    <p class="text-xs font-bold text-slate-300 group-hover:text-white uppercase tracking-widest">Validasi</p>
                </a>
            </div>
        </main>
    </div>

</body>
</html>