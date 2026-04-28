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

$data = $koneksi->query("
    SELECT t.*, b.nama_barang, b.cover, d.jumlah
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
            background-color: #0b0e1a;
            color: #f1f5f9;
        }
        .sidebar-glass {
            background: rgba(13, 17, 33, 0.7);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.03);
        }
        .card-glass {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .table-row-custom {
            background: rgba(255, 255, 255, 0.01);
            transition: all 0.2s ease;
        }
        .table-row-custom:hover {
            background: rgba(255, 255, 255, 0.03);
            transform: scale(1.002);
        }
    </style>
</head>
<body class="min-h-screen flex overflow-hidden">

    <aside class="w-80 sidebar-glass flex flex-col items-center py-12 px-8">
        <div class="mb-16 w-full text-center">
            <h1 class="text-sm font-black tracking-[0.4em] uppercase text-blue-500">Bibliotech</h1>
        </div>

        <div class="flex flex-col items-center mb-10">
            <div class="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center text-3xl font-black shadow-2xl shadow-blue-600/40 mb-6 text-white">
                <?= strtoupper(substr($user['username'] ?? 'U', 0, 1)) ?>
            </div>
            <h2 class="text-xl font-bold tracking-tight text-white"><?= $user['username'] ?? 'User' ?></h2>
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2 bg-slate-800/50 px-4 py-1.5 rounded-full border border-white/5">ID: <?= $user['barcode'] ?? '-' ?></span>
        </div>

        <div class="w-full p-5 bg-white rounded-[2.5rem] shadow-2xl shadow-black/20 mb-10 text-center">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= $user['barcode'] ?? 'empty' ?>" class="w-full rounded-2xl mb-4">
        </div>

        <div class="mt-auto w-full">
            <a href="../auth/logout.php" class="flex items-center justify-center gap-3 w-full py-4 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-rose-500 transition-colors border border-white/5 rounded-2xl bg-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                Keluar Sesi
            </a>
        </div>
    </aside>

    <main class="flex-1 h-screen overflow-y-auto p-12 lg:p-16">
        
        <header class="flex justify-between items-end mb-16">
            <div>
                <h1 class="text-4xl font-black tracking-tighter mb-3 uppercase">Halo, <span class="text-blue-500"><?= explode(' ', $user['username'])[0] ?></span> 👋</h1>
                <p class="text-slate-500 text-sm font-medium">Selamat datang, ingin meminjam buku apa?</p>
            </div>
            <div class="flex gap-4">
                <a href="pinjam.php" class="bg-blue-600 hover:bg-blue-500 px-10 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all shadow-xl shadow-blue-600/30 text-white">Pinjam Buku</a>
                <a href="kembali.php" class="bg-slate-800 hover:bg-slate-700 px-10 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all border border-white/5 text-white">Pengembalian</a>
            </div>
        </header>

        <div class="card-glass rounded-[3rem] p-12">
            <div class="mb-12">
                <h3 class="text-xl font-bold tracking-tight text-white">Riwayat Transaksi</h3>
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mt-2">Daftar buku yang dipinjam & dikembalikan</p>
            </div>

            <div class="w-full">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-600 uppercase tracking-widest">
                            <th class="px-8 py-2">Buku</th>
                            <th class="px-8 py-2">Informasi Buku</th>
                            <th class="px-8 py-2 text-center">Status Pinjam</th>
                            <th class="px-8 py-2">Estimasi Kembali</th>
                            <th class="px-8 py-2 text-right">Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($d = $data->fetch_assoc()) { 
                            $today = date('Y-m-d');
                            $denda = 0;

                            if ($d['status'] == 'dipinjam' && $today > $d['tanggal_kembali']) {
                                $telat = (strtotime($today) - strtotime($d['tanggal_kembali'])) / 86400;
                                $denda += $telat * 1000;
                            }
                            if ($d['kondisi'] == 'rusak') $denda += 5000;
                            if ($d['kondisi'] == 'hilang') $denda += 20000;

                            $is_lunas = (isset($d['denda_bayar']) && $d['denda_bayar'] == 1);
                            $display_denda = $is_lunas ? 0 : $denda;

                            $label = ''; $dot = ''; $text = '';
                            if ($d['status'] == 'menunggu') {
                                $label = 'Menunggu Persetujuan'; $dot = 'bg-slate-500'; $text = 'text-slate-400';
                            } elseif ($d['status'] == 'dipinjam') {
                                $label = 'Dipinjam'; $dot = 'bg-amber-500'; $text = 'text-amber-500';
                            } elseif ($d['status'] == 'menunggu pengecekan') {
                                $label = 'Menunggu Verifikasi'; $dot = 'bg-blue-500'; $text = 'text-blue-400';
                            } elseif (in_array($d['status'], ['kembali', 'rusak', 'hilang'])) {
                                if ($d['kondisi'] == 'rusak' || $d['status'] == 'rusak') {
                                    $label = 'Kembali (Rusak)'; $dot = 'bg-amber-400'; $text = 'text-amber-400';
                                } elseif ($d['kondisi'] == 'hilang' || $d['status'] == 'hilang') {
                                    $label = 'Hilang'; $dot = 'bg-rose-500'; $text = 'text-rose-500';
                                } else {
                                    $label = 'Kembali (Normal)'; $dot = 'bg-emerald-500'; $text = 'text-emerald-400';
                                }
                            }
                        ?>
                        <tr class="table-row-custom">
                            <td class="px-8 py-7 rounded-l-[2rem] border-y border-l border-white/5">
                                <div class="w-14 h-20 rounded-xl overflow-hidden bg-slate-800 border border-white/5">
                                    <?php if(!empty($d['cover'])): ?>
                                        <img src="../uploads/<?= $d['cover'] ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-[8px] font-black opacity-20 uppercase">No Cover</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5">
                                <div class="font-bold text-lg mb-1 uppercase tracking-tight text-white"><?= $d['nama_barang'] ?></div>
                                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic">jumlah: <?= $d['jumlah'] ?></div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5">  
                                <div class="flex items-center justify-center gap-2.5 <?= $text ?> text-[10px] font-black uppercase tracking-[0.1em]">
                                    <span class="w-2 h-2 rounded-full <?= $dot ?> shadow-[0_0_8px_rgba(0,0,0,0.5)]"></span>
                                    <?= $label ?>
                                </div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5">
                                <div class="text-sm font-bold text-slate-400"><?= date('d M Y', strtotime($d['tanggal_kembali'])) ?></div>
                            </td>
                            <td class="px-8 py-7 rounded-r-[2rem] border-y border-r border-white/5 text-right">
                                <?php if($display_denda > 0): ?>
                                    <span class="text-rose-500 text-sm font-black tracking-tight">RP <?= number_format($display_denda) ?></span>
                                <?php elseif($is_lunas): ?>
                                    <span class="text-emerald-500 font-black text-[10px] uppercase tracking-widest bg-emerald-500/10 px-4 py-1.5 rounded-full border border-emerald-500/20">Lunas</span>
                                <?php else: ?>
                                    <span class="text-slate-700 text-xs font-black">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>