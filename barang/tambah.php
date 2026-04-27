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

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $stok = $_POST['stok'];
    
    $koneksi->query("INSERT INTO barang VALUES('', '$nama', '$jenis', '$stok', '')");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            --input-bg: #f1f5f9;
            --border-color: #e2e8f0;
        }

        .dark {
            --bg-body: #080b14;
            --bg-card: #111827;
            --text-main: #f1f5f9;
            --input-bg: #1e293b;
            --border-color: rgba(255, 255, 255, 0.05);
        }

        body { background-color: var(--bg-body); color: var(--text-main); }
        .custom-card { background-color: var(--bg-card); border: 1px solid var(--border-color); }
        .input-style { background-color: var(--input-bg); border: 1px solid var(--border-color); color: var(--text-main); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <nav class="border-b border-[var(--border-color)] py-5">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <a href="../admin/index.php" class="text-xl font-bold tracking-tighter">
                BIBLIO<span class="text-blue-600">TECH</span>
            </a>
            <a href="index.php" class="text-xs font-bold uppercase tracking-widest opacity-50 hover:opacity-100 transition">Batal & Kembali</a>
        </div>
    </nav>

    <div class="flex-1 flex items-center justify-center p-6">
        <div class="w-full max-w-xl">
            
            <div class="custom-card rounded-[2.5rem] shadow-2xl overflow-hidden">
                <div class="p-8 md:p-12">
                    <header class="mb-10">
                        <div class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-blue-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h2 class="text-3xl font-extrabold tracking-tight">Tambah Barang</h2>
                        <p class="opacity-50 font-medium mt-1 text-sm">Registrasi item baru ke dalam database sistem.</p>
                    </header>

                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Nama Buku</label>
                            <input type="text" name="nama" required placeholder=""
                                class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Kategori</label>
                                <select name="jenis" class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium appearance-none">
                                    <option value="akademik">Buku Akademik</option>
                                    <option value="fiksi">Buku Fiksi</option>
                                    <option value="non-fiksi">Buku Non-Fiksi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Jumlah Stok</label>
                                <input type="number" name="stok" required placeholder="0"
                                    class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            </div>
                        </div>

                        <div class="pt-6">
                            <button name="simpan" 
                                class="w-full py-5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Daftarkan Barang
                            </button>
                        </div>
                    </form>
                </div>
                <div class="bg-slate-500/5 py-4 text-center border-t border-[var(--border-color)]">
                    <p class="text-[9px] font-black uppercase tracking-[0.4em] opacity-30">Bibliotech System • Management Portal</p>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>