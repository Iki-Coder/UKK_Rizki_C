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

$error_upload = '';

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $nama_file = $_FILES['cover']['name'];
    $ukuran_file = $_FILES['cover']['size'];
    $error_file = $_FILES['cover']['error'];
    $tmp_name = $_FILES['cover']['tmp_name'];

    if ($error_file === 4) {
        $error_upload = "Silakan pilih gambar cover terlebih dahulu.";
    } else {
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file = explode('.', $nama_file);
        $ekstensi_file = strtolower(end($ekstensi_file));

        if (!in_array($ekstensi_file, $ekstensi_valid)) {
            $error_upload = "Format file tidak valid. Gunakan JPG, JPEG, atau PNG.";
        } else {
            if ($ukuran_file > 2000000) {
                $error_upload = "Ukuran file terlalu besar. Maksimal 2MB.";
            } else {
                // Generate nama file baru agar unik
                $nama_file_baru = uniqid();
                $nama_file_baru .= '.';
                $nama_file_baru .= $ekstensi_file;

                if (move_uploaded_file($tmp_name, '../uploads/' . $nama_file_baru)) {
                    // Jika upload berhasil, simpan ke database
                    $query = "INSERT INTO barang (nama_barang, jenis, stok, deskripsi, cover) 
                              VALUES ('$nama', '$jenis', '$stok', '$deskripsi', '$nama_file_baru')";
                    
                    if (mysqli_query($koneksi, $query)) {
                        header("Location: index.php");
                        exit;
                    } else {
                        $error_upload = "Gagal menyimpan data ke database: " . mysqli_error($koneksi);
                    }
                } else {
                    $error_upload = "Gagal mengunggah file gambar.";
                }
            }
        }
    }
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
        
        /* Gaya kustom untuk input file */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        .file-input-btn {
            @apply input-style px-6 py-4 rounded-2xl font-medium inline-flex items-center gap-2 cursor-pointer transition-all;
        }
        .file-input-btn:hover { @apply bg-slate-200 dark:bg-slate-700; }
        .file-name-display { @apply text-sm opacity-60 ml-3 font-medium; }
    </style>
    
    <script>
        function updateFileName(input) {
            var fileName = input.files[0].name;
            document.getElementById('file-name').textContent = fileName;
        }
    </script>
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
        <div class="w-full max-w-2xl">
            
            <div class="custom-card rounded-[2.5rem] shadow-2xl overflow-hidden">
                <div class="p-8 md:p-12">
                    <header class="mb-10 flex items-center gap-6">
                        <div class="h-16 w-16 bg-blue-600 rounded-3xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-extrabold tracking-tight">Tambah Barang</h2>
                            <p class="opacity-50 font-medium mt-1 text-sm">Registrasi item baru ke dalam database sistem.</p>
                        </div>
                    </header>

                    <?php if ($error_upload): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6 font-medium text-sm" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline"><?= $error_upload ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Nama Buku</label>
                            <input type="text" name="nama" required placeholder="Judul Buku..."
                                class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Kategori</label>
                                <div class="relative">
                                    <select name="jenis" class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium appearance-none">
                                        <option value="akademik">Buku Akademik</option>
                                        <option value="fiksi">Buku Fiksi</option>
                                        <option value="non-fiksi">Buku Non-Fiksi</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none opacity-50">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Jumlah Stok</label>
                                <input type="number" name="stok" required placeholder="0"
                                    class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" rows="4" placeholder="Sinopsis atau deskripsi singkat..."
                                class="input-style w-full px-6 py-4 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition-all font-medium resize-none"></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] mb-2 opacity-60">Cover Buku (JPG, PNG, maks 2MB)</label>
                            <div class="flex items-center">
                                <div class="file-input-wrapper">
                                    <label class="file-input-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        Pilih Gambar
                                        <input type="file" name="cover" required onchange="updateFileName(this)" accept="image/png, image/jpeg, image/jpg">
                                    </label>
                                </div>
                                <span id="file-name" class="file-name-display">Belum ada file yang dipilih</span>
                            </div>
                        </div>

                        <div class="pt-6">
                            <button name="simpan" 
                                class="w-full py-5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center gap-3 text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Daftarkan Barang Baru
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