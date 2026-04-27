<?php
session_start();

require '../config/Database.php';
require '../models/Transaksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$transaksi = new Transaksi($koneksi);

if (!isset($_SESSION['login']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

$data = $koneksi->query("
    SELECT t.*, p.username, b.nama_barang, d.jumlah
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    WHERE t.status='menunggu'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan | Bibliotech</title>
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
    </style>
</head>
<body class="min-h-screen p-6 md:p-12 relative overflow-x-hidden">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[30%] bg-amber-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-1 w-12 bg-amber-500 rounded-full"></div>
                    <h1 class="text-xs font-black text-amber-500 uppercase tracking-[0.4em]">Approval Queue</h1>
                </div>
                <h2 class="text-4xl font-black tracking-tighter text-white">Antrean Persetujuan <span class="text-amber-500">.</span></h2>
                <div class="flex items-center gap-3 mt-3">
                    <span class="px-3 py-1 bg-amber-500/10 text-amber-500 rounded-lg text-[10px] font-black uppercase tracking-widest border border-amber-500/20">
                        <?= $data->num_rows ?> PERMINTAAN MASUK
                    </span>
                </div>
            </div>
            
            <a href="../transaksi/index.php" class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all border border-white/5 flex items-center gap-2 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black border-b border-white/5">
                            <th class="px-8 py-6 text-center">No</th>
                            <th class="px-8 py-6">Informasi Peminjam</th>
                            <th class="px-8 py-6">Barang & Kuantitas</th>
                            <th class="px-8 py-6">Waktu Pengajuan</th>
                            <th class="px-8 py-6 text-center">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if($data->num_rows > 0): ?>
                            <?php $no=1; while($d = $data->fetch_assoc()) { ?>
                            <tr class="hover:bg-white/[0.03] transition-all group">
                                <td class="px-8 py-8 text-center font-black text-[10px] text-slate-600">
                                    <?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="text-white font-bold group-hover:text-amber-500 transition-colors uppercase tracking-tight"><?= $d['username'] ?></div>
                                    <div class="text-[9px] text-slate-600 mt-1 uppercase font-black tracking-widest">ID Anggota: #<?= $d['id_pengguna'] ?></div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="text-blue-400 font-bold text-sm uppercase tracking-tight"><?= $d['nama_barang'] ?></div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-black uppercase tracking-widest"><?= $d['jumlah'] ?> UNIT</div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="text-xs font-bold text-slate-300"><?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?></div>
                                    <div class="text-[9px] text-slate-600 mt-1 uppercase font-black tracking-widest">Digital Request</div>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="proses_persetujuan.php?id=<?= $d['id'] ?>" 
                                           onclick="return confirm('Konfirmasi: Setujui peminjaman ini?')"
                                           class="h-11 w-11 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white rounded-xl flex items-center justify-center transition-all border border-emerald-500/20 shadow-lg shadow-emerald-500/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </a>

                                        <a href="tolak.php?id=<?= $d['id'] ?>" 
                                           onclick="return confirm('Peringatan: Tolak peminjaman ini?')"
                                           class="h-11 w-11 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-xl flex items-center justify-center transition-all border border-rose-500/20 shadow-lg shadow-rose-600/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-32 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-white/5 rounded-[2rem] flex items-center justify-center mb-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <h3 class="text-white font-bold uppercase tracking-[0.2em] mb-2">Semua Beres</h3>
                                        <p class="text-[10px] text-slate-600 font-black uppercase tracking-[0.4em]">BELUM ADA ANTREAN BARU SAAT INI</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>