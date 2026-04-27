<?php
session_start();

require '../config/Database.php';

$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_SESSION['id'];

// Query hanya barang yang statusnya masih 'dipinjam'
$data = $koneksi->query("
    SELECT t.*, b.nama_barang, d.jumlah
    FROM transaksi t
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    WHERE t.id_pengguna='$id' AND t.status='dipinjam'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #080b14;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen p-6 md:p-12 relative overflow-x-hidden">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[30%] h-[30%] bg-emerald-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="max-w-6xl mx-auto relative z-10">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-1 w-12 bg-blue-600 rounded-full"></div>
                    <h1 class="text-xs font-black text-blue-500 uppercase tracking-[0.4em]">Sistem Pengembalian</h1>
                </div>
                <h2 class="text-4xl font-black tracking-tighter text-white">Buku Dipinjam <span class="text-blue-500">.</span></h2>
                <p class="text-slate-500 mt-2 font-medium">Daftar koleksi yang sedang Anda bawa saat ini.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <h1 class="hidden md:block text-sm font-extrabold tracking-[0.2em] uppercase mr-4">
                    Biblio<span class="text-blue-500">Tech</span>
                </h1>
                <a href="index.php" class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-white rounded-2xl font-bold text-xs uppercase tracking-widest transition-all border border-white/5 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>

        <div class="glass-card border-blue-500/20 p-6 rounded-[2rem] mb-10 flex items-center gap-5">
            <div class="h-12 w-12 bg-blue-600/20 rounded-2xl flex items-center justify-center text-blue-400 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed font-medium">
                Gunakan tombol <span class="text-white font-bold">Kembalikan</span> untuk memproses. Pastikan fisik buku diserahkan kembali ke petugas untuk verifikasi akhir kondisi barang.
            </p>
        </div>

        <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/[0.02] text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black border-b border-white/5">
                            <th class="px-10 py-6">Informasi Koleksi</th>
                            <th class="px-10 py-6 text-center">Jumlah</th>
                            <th class="px-10 py-6">Batas Waktu</th>
                            <th class="px-10 py-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if($data->num_rows > 0): ?>
                            <?php while($d = $data->fetch_assoc()) { 
                                $is_late = strtotime(date('Y-m-d')) > strtotime($d['tanggal_kembali']);
                            ?>
                            <tr class="hover:bg-white/[0.03] transition-all group">
                                <td class="px-10 py-8">
                                    <div class="text-white font-bold text-lg group-hover:text-blue-400 transition-colors tracking-tight"><?= $d['nama_barang'] ?></div>
                                    <div class="text-[10px] text-slate-500 mt-1 font-bold uppercase tracking-widest">Pinjam: <?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?></div>
                                </td>
                                <td class="px-10 py-8 text-center">
                                    <span class="inline-block px-4 py-1.5 bg-slate-900/80 rounded-xl border border-white/5 font-black text-xs text-blue-400">
                                        <?= str_pad($d['jumlah'], 2, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold <?= $is_late ? 'text-rose-500' : 'text-slate-300' ?>">
                                            <?= date('d M Y', strtotime($d['tanggal_kembali'])) ?>
                                        </span>
                                        <?php if($is_late): ?>
                                            <span class="text-[9px] text-rose-500 font-black uppercase tracking-widest mt-1">Status: Terlambat</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <button onclick="confirmReturn('proses_kembali.php?id=<?= $d['id'] ?>')" 
                                       class="inline-flex items-center gap-2 px-7 py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all transform active:scale-95 shadow-xl shadow-emerald-600/20">
                                        Kembalikan
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-8 py-28 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-20 h-20 bg-slate-800/30 rounded-full flex items-center justify-center mb-6 border border-white/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <h4 class="text-white font-bold text-lg mb-1">Semua Koleksi Aman</h4>
                                        <p class="text-slate-600 text-xs font-bold uppercase tracking-widest">Tidak ada tanggungan pengembalian</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="mt-12 text-center">
            <p class="text-[9px] font-black text-slate-700 uppercase tracking-[0.5em]">&copy; 2026 Bibliotech Management • V.3.0</p>
        </footer>
    </div>

    <script>
    function confirmReturn(url) {
        Swal.fire({
            title: 'Konfirmasi Kembali',
            text: "Apakah buku sudah siap diserahkan ke petugas?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669', // emerald-600
            cancelButtonColor: '#1e293b',  // slate-800
            confirmButtonText: 'Ya, Kembalikan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            background: '#111827',
            color: '#fff',
            customClass: {
                popup: 'rounded-[2.5rem] border border-white/10'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
    </script>

</body>
</html>