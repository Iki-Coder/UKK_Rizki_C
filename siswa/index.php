<?php
session_start();

require '../config/Database.php';
require '../models/Pengguna.php';

$db = new Database();
$koneksi = $db->koneksi;

$pengguna = new Pengguna($koneksi);

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_SESSION['id'];
$user = $pengguna->getById($id);

// Query history
$data = $koneksi->query("
    SELECT t.*, b.nama_barang, d.jumlah
    FROM transaksi t
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    WHERE t.id_pengguna='$id'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Space | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #080b14;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="min-h-screen">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[30%] h-[30%] bg-purple-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="flex flex-col lg:flex-row relative z-10">

        <aside class="w-full lg:w-[350px] lg:h-screen lg:sticky lg:top-0 glass-card p-10 flex flex-col items-center justify-between">
            <div class="w-full text-center">
                <div class="flex justify-center mb-8">
                    <h1 class="text-xl font-black tracking-tighter uppercase">Biblio<span class="text-blue-500">Tech</span></h1>
                </div>

                <div class="relative inline-block mb-6">
                    <div class="w-24 h-24 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-[2rem] flex items-center justify-center shadow-2xl shadow-blue-500/20 rotate-3 group-hover:rotate-0 transition-transform">
                        <span class="text-3xl font-black text-white -rotate-3"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
                    </div>
                </div>

                <h2 class="text-2xl font-extrabold tracking-tight"><?= $user['username'] ?></h2>
                <div class="inline-flex items-center px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full mt-2 border border-blue-500/10">
                    ID: <?= $user['barcode'] ?>
                </div>

                <div class="mt-10 bg-white p-5 rounded-[2.5rem] shadow-2xl shadow-black/50 transition-all">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= $user['barcode'] ?>" alt="QR Code" class="mx-auto rounded-xl">
                </div>
            </div>

            <a href="../auth/logout.php" class="w-full mt-10 py-4 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all border border-rose-500/10 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar Sesi
            </a>
        </aside>

        <main class="flex-1 p-6 md:p-16">
            <header class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
                <div>
                    <h1 class="text-4xl font-black tracking-tighter">Halo, <?= explode(' ', $user['username'])[0] ?> 👋</h1>
                    <p class="text-slate-500 mt-2 font-medium">Selamat datang, ingin meminjam buku apa?</p>
                </div>
                
                <div class="flex gap-4 w-full md:w-auto">
                    <a href="pinjam.php" class="flex-1 md:flex-none btn-gradient px-8 py-4 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-blue-600/20 text-center">
                        Pinjam Buku
                    </a>
                    <a href="kembali.php" class="flex-1 md:flex-none bg-slate-800 hover:bg-slate-700 px-8 py-4 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all text-center">
                        Pengembalian
                    </a>
                </div>
            </header>

            <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
                <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/[0.02]">
                    <div>
                        <h3 class="font-bold text-lg tracking-tight">Riwayat Transaksi</h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-bold mt-1">Daftar buku yang dipinjam & dikembalikan</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black border-b border-white/5">
                                <th class="px-8 py-6">Informasi Buku</th>
                                <th class="px-8 py-6">Status Pinjam</th>
                                <th class="px-8 py-6 text-center">Estimasi Kembali</th>
                                <th class="px-8 py-6 text-right">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php while($d = $data->fetch_assoc()) { 
                                $today = date('Y-m-d');
                                $denda = 0;
                                if ($d['status'] == 'dipinjam' && $today > $d['tanggal_kembali']) {
                                    $selisih = (strtotime($today) - strtotime($d['tanggal_kembali'])) / (60*60*24);
                                    $denda += $selisih * 1000;
                                }
                                if ($d['kondisi'] == 'rusak') { $denda += 5000; } 
                                elseif ($d['kondisi'] == 'hilang') { $denda += 20000; }
                            ?>
                            <tr class="hover:bg-white/[0.03] transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-white group-hover:text-blue-400 transition-colors"><?= $d['nama_barang'] ?></span>
                                        <span class="text-[9px] text-slate-500 mt-1 uppercase font-black tracking-widest italic">Kondisi: <?= $d['kondisi'] ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <?php if ($d['status'] == 'menunggu'): ?>
                                        <div class="flex items-center gap-2 text-slate-500 font-black text-[10px] uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-500 animate-pulse"></span> Menunggu
                                        </div>
                                    <?php elseif ($d['status'] == 'dipinjam'): ?>
                                        <div class="flex items-center gap-2 text-amber-500 font-black text-[10px] uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Dipinjam
                                        </div>
                                    <?php else: ?>
                                        <div class="flex items-center gap-2 text-emerald-500 font-black text-[10px] uppercase tracking-widest">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Kembali
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 text-center font-bold text-xs text-slate-400">
                                    <?= date('d M Y', strtotime($d['tanggal_kembali'])) ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <?php if($denda > 0): ?>
                                        <span class="px-4 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-[10px] font-black border border-rose-500/10 uppercase tracking-widest">
                                            Rp <?= number_format($denda, 0, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-slate-700 font-black text-xs">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php } ?>

                            <?php if($data->num_rows == 0): ?>
                            <tr>
                                <td colspan="4" class="px-8 py-24 text-center">
                                    <p class="text-slate-500 font-black uppercase tracking-[0.3em] text-[10px]">Belum ada riwayat aktivitas</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

</body>
</html>