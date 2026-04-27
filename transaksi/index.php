<?php
session_start();
require '../config/Database.php';
require '../models/Barang.php';

$db = new Database();
$koneksi = $db->koneksi;
$barangModel = new Barang($koneksi);

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$data = $barangModel->getAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koleksi Buku | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
            </style>
</head>
<body class="bg-[#0b0f1a] text-slate-200 min-h-screen p-6 md:p-12">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] right-[-5%] w-[30%] h-[30%] bg-blue-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="max-w-6xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div>
                <nav class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-[0.2em] mb-3">
                    <a href="../admin/index.php" class="hover:text-blue-500 transition-colors">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-300">Koleksi Buku</span>
                </nav>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Koleksi Buku</h1>
            </div>
            
            <a href="tambah.php" class="inline-flex items-center justify-center px-8 py-3.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl transition-all shadow-xl shadow-blue-600/20 active:scale-95 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2.5 group-hover:rotate-90 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Buku Baru
            </a>
        </div>

        <div class="bg-slate-900/40 backdrop-blur-xl border border-slate-800/50 rounded-[2rem] shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800/50 text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black italic">
                            <th class="px-8 py-6 text-center">No</th>
                            <th class="px-8 py-6">Judul & Kategori</th>
                            <th class="px-8 py-6 text-center">Persediaan</th>
                            <th class="px-8 py-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/30">
                        <?php $no=1; while($d = $data->fetch_assoc()) { ?>
                        <tr class="hover:bg-blue-500/[0.03] transition-colors group">
                            <td class="px-8 py-6 text-center text-slate-600 font-bold text-xs opacity-50 italic">
                                #<?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-white text-base group-hover:text-blue-400 transition-colors tracking-tight"><?= $d['nama_barang'] ?></span>
                                    <span class="text-[10px] text-slate-500 mt-1 uppercase tracking-[0.15em] font-bold"><?= $d['jenis'] ?></span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black tracking-widest <?= $d['stok'] > 5 ? 'text-white' : 'text-rose-500 animate-pulse' ?>">
                                        <?= $d['stok'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="edit.php?id=<?= $d['id'] ?>" class="p-3 bg-slate-800/50 text-slate-400 hover:text-amber-400 hover:bg-amber-400/10 rounded-xl transition-all border border-transparent hover:border-amber-400/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('hapus.php?id=<?= $d['id'] ?>')" class="p-3 bg-slate-800/50 text-slate-400 hover:text-rose-500 hover:bg-rose-500/10 rounded-xl transition-all border border-transparent hover:border-rose-500/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        
                        <?php if($data->num_rows == 0) { ?>
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-4xl mb-4 opacity-10">📦</span>
                                    <p class="text-slate-600 text-xs font-bold uppercase tracking-widest">Database Kosong</p>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Hapus Buku?',
            text: "Data koleksi akan dihapus permanen dari sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#1e293b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        })
    }
    </script>

</body>
</html>