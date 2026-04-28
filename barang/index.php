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
    <title>Inventory | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.3s ease; }
        :root {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --border-color: #e2e8f0;
        }
        .dark {
            --bg-body: #080b14;
            --bg-card: #111827;
            --text-main: #ffffff;
            --border-color: rgba(255, 255, 255, 0.05);
        }
        body { background-color: var(--bg-body); color: var(--text-main); }
        .custom-card { background-color: var(--bg-card); border: 1px solid var(--border-color); }
    </style>
</head>
<body class="min-h-screen pb-12">

    <nav class="border-b border-[var(--border-color)] bg-[var(--bg-card)]/80 backdrop-blur-md sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="../admin/index.php" class="p-2.5 rounded-xl border border-[var(--border-color)] hover:bg-slate-500/10 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold tracking-tighter uppercase">Inventory</h1>
            </div>
            <a href="tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg shadow-blue-600/20 transition active:scale-95">
                + TAMBAH KOLEKSI
            </a>
        </div>
    </nav>

    <main class="container mx-auto px-6 mt-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-6">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight">Inventory Buku</h2>
                <p class="opacity-50 text-sm font-medium italic">Total <?= $data->num_rows ?> title buku terdaftar.</p>
            </div>
            
            <div class="relative w-full md:w-80">
                <input type="text" placeholder="Cari buku..." class="w-full bg-[var(--bg-card)] border border-[var(--border-color)] rounded-2xl px-5 py-3 pl-12 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition shadow-sm text-[var(--text-main)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-4 top-3.5 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="custom-card rounded-[2rem] overflow-hidden shadow-2xl shadow-black/5">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-[var(--border-color)] bg-slate-500/5 text-[10px] font-black uppercase tracking-[0.2em] opacity-60">
                            <th class="px-8 py-5 text-center">Cover</th>
                            <th class="px-6 py-5">Informasi Buku</th>
                            <th class="px-6 py-5">Kategori</th>
                            <th class="px-6 py-5 text-center">Persediaan</th>
                            <th class="px-8 py-5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]">
                        <?php $no=1; while($d = $data->fetch_assoc()) { ?>
                        <tr class="hover:bg-slate-500/5 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="w-16 h-24 rounded-xl overflow-hidden border border-[var(--border-color)] shadow-sm">
                                    <?php if(!empty($d['cover'])): ?>
                                        <img src="../uploads/<?= $d['cover'] ?>" alt="Cover" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold opacity-30">NO COVER</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-base tracking-tight"><?= $d['nama_barang'] ?></span>
                                    <span class="text-[10px] font-black opacity-30 italic mt-1 uppercase tracking-widest">ID: #<?= str_pad($no++, 3, '0', STR_PAD_LEFT) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <span class="px-4 py-1.5 bg-blue-600/10 text-blue-600 dark:text-blue-400 rounded-lg text-[10px] font-extrabold uppercase tracking-widest border border-blue-600/10">
                                    <?= $d['jenis'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-sm font-black <?= $d['stok'] ?>">
                                    <?= $d['stok'] ?> Unit
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-end items-center gap-3">
                                    <a href="edit.php?id=<?= $d['id'] ?>" class="h-9 w-9 flex items-center justify-center rounded-xl bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('hapus.php?id=<?= $d['id'] ?>')" class="h-9 w-9 flex items-center justify-center rounded-xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php if($data->num_rows == 0) { ?>
                    <div class="py-20 text-center">
                        <div class="text-5xl mb-4 opacity-20">📦</div>
                        <p class="text-sm font-bold opacity-30 uppercase tracking-[0.3em]">Gudang Masih Kosong</p>
                    </div>
                <?php } ?>
            </div>
        </div>

    </main>

    <script>
    function confirmDelete(deleteUrl) {
        Swal.fire({
            title: 'Hapus Barang?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        })
    }
    </script>

</body>
</html> 