<?php
session_start();

require '../config/Database.php';
require '../models/Transaksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$transaksi = new Transaksi($koneksi);

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$data = $koneksi->query("
    SELECT t.*, p.username 
    FROM transaksi t 
    JOIN pengguna p ON t.id_pengguna = p.id
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transaksi | Bibliotech</title>
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
        <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[30%] h-[30%] bg-purple-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>

                <h2 class="text-4xl font-black tracking-tighter text-white">Manajemen Peminjaman</h2>
                <p class="text-slate-500 mt-2 font-medium">Kelola persetujuan dan lihat riwayat</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="../admin/index.php" class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all border border-white/5 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Dashboard
                </a>
                <a href="tambah.php" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all shadow-xl shadow-blue-600/20 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Input Manual
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <a href="persetujuan.php" class="glass-card p-6 rounded-[2rem] flex items-center justify-between group hover:bg-white/[0.05] transition-all border-amber-500/10">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm tracking-tight text-white uppercase">Butuh Persetujuan</h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black mt-0.5">Cek Permintaan Baru</p>
                    </div>
                </div>
                <div class="h-8 w-8 rounded-full bg-white/5 flex items-center justify-center text-slate-500 group-hover:text-amber-500 group-hover:translate-x-1 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                </div>
            </a>
        </div>

        <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black border-b border-white/5">
                            <th class="px-8 py-6">ID Log</th>
                            <th class="px-8 py-6">User / Peminjam</th>
                            <th class="px-8 py-6">Timeline</th>
                            <th class="px-8 py-6 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php $no=1; while($d = $data->fetch_assoc()) { 
                            $status_ui = [
                                'menunggu' => 'text-slate-400 bg-slate-500/10 border-slate-500/10',
                                'dipinjam' => 'text-amber-500 bg-amber-500/10 border-amber-500/10',
                                'kembali'  => 'text-emerald-500 bg-emerald-500/10 border-emerald-500/10'
                            ];
                            $current_status = $status_ui[$d['status']] ?? 'text-slate-400';
                        ?>
                        <tr class="hover:bg-white/[0.03] transition-all group">
                            <td class="px-8 py-6 font-mono text-xs text-slate-600">#<?= $d['id'] ?></td>
                            <td class="px-8 py-6">
                                <div class="text-white font-bold group-hover:text-blue-400 transition-colors uppercase tracking-tight"><?= $d['username'] ?></div>
                                <div class="text-[9px] text-slate-600 mt-1 uppercase font-black tracking-widest">Siswa Terdaftar</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-xs font-bold text-slate-300"><?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?></div>
                                <div class="text-[9px] text-slate-600 mt-1 uppercase font-black tracking-widest">Est. Kembali: <?= date('d M Y', strtotime($d['tanggal_kembali'])) ?></div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border <?= $current_status ?>">
                                    <?= $d['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>