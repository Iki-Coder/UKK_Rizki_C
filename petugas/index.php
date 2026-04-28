<?php
session_start();

require '../config/Database.php';
require '../models/Pengguna.php';

$db = new Database();
$koneksi = $db->koneksi;

$pengguna = new Pengguna($koneksi);

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_SESSION['id'];
$user = $pengguna->getById($id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petugas Central | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #05070a;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(59, 130, 246, 0.2);
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }
        .nav-glass {
            background: rgba(5, 7, 10, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }
        .text-glow {
            text-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="min-h-screen relative overflow-x-hidden">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-15%] left-[-10%] w-[50%] h-[50%] bg-blue-600/10 blur-[150px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[40%] h-[40%] bg-indigo-600/5 blur-[120px] rounded-full"></div>
    </div>

    <nav class="nav-glass sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-8 lg:px-12">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-extrabold text-white tracking-[0.2em] uppercase hidden sm:block">Biblio<span class="text-blue-500 text-glow">tech</span></span>
                </div>
                
                <div class="flex items-center gap-8">
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-bold text-white"><?= $user['username'] ?></span>
                    </div>
                    <a href="../auth/logout.php" class="px-6 py-2.5 bg-white/5 hover:bg-rose-600 text-slate-300 hover:text-white rounded-xl transition-all text-xs font-black uppercase tracking-widest border border-white/5">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 lg:px-12 py-16 relative z-10">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-16 gap-8">
            <div class="max-w-2xl">
                <h2 class="text-5xl font-black tracking-tighter text-white mb-4 leading-tight">Kelola Sirkulasi</h2>
                <p class="text-slate-400 font-medium leading-relaxed">Selamat datang kembali. Sistem siap untuk memverifikasi peminjaman, mengelola pengembalian, dan menyusun laporan.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <a href="../transaksi/index.php" class="group glass-card p-10 rounded-[3rem] relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5 transform group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div class="w-16 h-16 bg-blue-600/10 rounded-2xl flex items-center justify-center text-blue-500 mb-8 group-hover:bg-blue-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-blue-600/30 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-white tracking-tight mb-2 uppercase">Transaksi</h3>
                <p class="text-slate-500 text-[11px] font-black uppercase tracking-widest">Log Peminjaman & Kembali</p>
            </a>

            <a href="../report/index.php" class="group glass-card p-10 rounded-[3rem] relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5 transform group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="w-16 h-16 bg-purple-600/10 rounded-2xl flex items-center justify-center text-purple-500 mb-8 group-hover:bg-purple-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-purple-600/30 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-white tracking-tight mb-2 uppercase">Laporan</h3>
                <p class="text-slate-500 text-[11px] font-black uppercase tracking-widest">Rekapitulasi & Export PDF</p>
            </a>

            <a href="scan.php" class="group glass-card p-10 rounded-[3rem] relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5 transform group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 4v1m0 11v1m5-12l-.5 1m4.5 4.5h-1m1 5l-1-.5M6.707 6.707l.707.707m10.606 10.606l.707.707M6.707 17.293l.707-.707M17.293 6.707l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="w-16 h-16 bg-amber-600/10 rounded-2xl flex items-center justify-center text-amber-500 mb-8 group-hover:bg-amber-600 group-hover:text-white group-hover:shadow-lg group-hover:shadow-amber-600/30 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M12 4v1m0 11v1m5-12l-.5 1m4.5 4.5h-1m1 5l-1-.5M6.707 6.707l.707.707m10.606 10.606l.707.707M6.707 17.293l.707-.707M17.293 6.707l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-white tracking-tight mb-2 uppercase">Scan Barcode</h3>
                <p class="text-slate-500 text-[11px] font-black uppercase tracking-widest">Verifikasi Barang Instan</p>
            </a>
        </div>
    </main>

</body>
</html>