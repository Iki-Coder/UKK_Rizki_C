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
    <title>Persetujuan | Inventaris Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen p-4 md:p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-3xl font-bold text-white tracking-tight">Antrean Persetujuan</h1>
                    <span class="px-2.5 py-0.5 bg-amber-500/20 text-amber-500 rounded-full text-xs font-black uppercase border border-amber-500/30">
                        <?= $data->num_rows ?> Permintaan
                    </span>
                </div>
                <p class="text-slate-400 text-sm italic">Verifikasi permintaan peminjaman barang dari siswa di sini.</p>
            </div>
            <a href="../transaksi/index.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl transition-all border border-slate-700 text-sm font-semibold group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Transaksi
            </a>
        </div>

        <div class="bg-slate-800/40 border border-slate-700 rounded-3xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900/40 text-slate-400 text-[10px] uppercase tracking-[0.2em]">
                            <th class="px-8 py-5 font-bold">No</th>
                            <th class="px-8 py-5 font-bold">Informasi Peminjam</th>
                            <th class="px-8 py-5 font-bold">Barang & Qty</th>
                            <th class="px-8 py-5 font-bold">Tanggal Pengajuan</th>
                            <th class="px-8 py-5 font-bold text-center">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        <?php if($data->num_rows > 0): ?>
                            <?php $no=1; while($d = $data->fetch_assoc()) { ?>
                            <tr class="hover:bg-slate-700/20 transition-all group">
                                <td class="px-8 py-6 text-slate-600 font-mono text-sm"><?= $no++ ?></td>
                                <td class="px-8 py-6">
                                    <div class="text-white font-bold text-base"><?= $d['username'] ?></div>
                                    <div class="text-[10px] text-slate-500 uppercase tracking-widest mt-0.5">ID: #<?= $d['id_pengguna'] ?></div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-blue-400 font-semibold text-sm"><?= $d['nama_barang'] ?></div>
                                    <div class="text-xs text-slate-400 italic"><?= $d['jumlah'] ?> Unit</div>
                                </td>
                                <td class="px-8 py-6 text-xs text-slate-400 font-medium">
                                    <?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="proses_persetujuan.php?id=<?= $d['id'] ?>" 
                                           onclick="return confirm('Setujui peminjaman ini?')"
                                           class="p-2.5 bg-emerald-600/10 hover:bg-emerald-600 text-emerald-500 hover:text-white rounded-xl transition-all border border-emerald-500/20 shadow-lg shadow-emerald-600/5 group/btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </a>

                                        <a href="tolak.php?id=<?= $d['id'] ?>" 
                                           onclick="return confirm('Tolak peminjaman ini?')"
                                           class="p-2.5 bg-rose-600/10 hover:bg-rose-600 text-rose-500 hover:text-white rounded-xl transition-all border border-rose-500/20 shadow-lg shadow-rose-600/5 group/btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-8 py-32 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-20 h-20 bg-slate-700/20 rounded-full flex items-center justify-center mb-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-slate-500 font-medium tracking-wide italic">Kopi dulu, Pak! Belum ada antrean peminjaman baru.</p>
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