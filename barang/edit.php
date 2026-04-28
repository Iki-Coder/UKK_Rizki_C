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

$id = $_GET['id'];
$data = $barangModel->getById($id);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis = $_POST['jenis'];
    $stok = $_POST['stok'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $nama_file = $data['cover'];

    if ($_FILES['cover']['error'] === 0) {
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));

        if (in_array($ekstensi_file, $ekstensi_valid) && $_FILES['cover']['size'] <= 2000000) {
            if ($data['cover'] && file_exists('../uploads/' . $data['cover'])) {
                unlink('../uploads/' . $data['cover']);
            }
            $nama_file = uniqid() . '.' . $ekstensi_file;
            move_uploaded_file($_FILES['cover']['tmp_name'], '../uploads/' . $nama_file);
        }
    }

    $koneksi->query("UPDATE barang SET 
        nama_barang='$nama',
        jenis='$jenis',
        stok='$stok',
        deskripsi='$deskripsi',
        cover='$nama_file'
        WHERE id='$id'
    ");

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koleksi | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #0b0e1a;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .input-style {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-style:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        select option {
            background-color: #ffffff;
            color: #000000;
        }
    </style>
</head>
<body class="min-h-screen py-12 px-4 flex items-center justify-center">

    <div class="w-full max-w-4xl">
        <a href="index.php" class="inline-flex items-center text-slate-500 hover:text-white mb-8 transition-all group">
            <div class="p-2 rounded-lg bg-white/5 mr-3 group-hover:bg-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span class="font-bold text-sm tracking-widest uppercase">Kembali</span>
        </a>

        <div class="glass-card rounded-[2.5rem] shadow-2xl overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                
                <div class="lg:w-1/3 bg-white/5 p-10 flex flex-col items-center justify-center border-b lg:border-b-0 lg:border-r border-white/5">
                    <div class="relative group w-full max-w-[200px]">
                        <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-slate-900 shadow-2xl border border-white/10">
                            <?php if($data['cover']): ?>
                                <img id="preview" src="../uploads/<?= $data['cover'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div id="placeholder" class="w-full h-full flex flex-col items-center justify-center opacity-20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] font-black uppercase tracking-widest">No Cover</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="lg:w-2/3 p-8 md:p-12">
                    <header class="mb-10">
                        <h2 class="text-3xl font-black text-white tracking-tighter uppercase">Edit <span class="text-blue-500">Koleksi.</span></h2>
                    </header>

                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 px-1">Nama Barang / Judul</label>
                            <input type="text" name="nama" value="<?= $data['nama_barang'] ?>" required
                                class="input-style w-full px-6 py-4 rounded-2xl font-semibold">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 px-1">Kategori</label>
                                <select name="jenis" class="input-style w-full px-6 py-4 rounded-2xl font-semibold appearance-none">
                                    <option value="fiksi" <?= $data['jenis']=='fiksi'?'selected':'' ?>>Buku Fiksi</option>
                                    <option value="non-fiksi" <?= $data['jenis']=='non-fiksi'?'selected':'' ?>>Buku Non-Fiksi</option>
                                    <option value="akademik" <?= $data['jenis']=='akademik'?'selected':'' ?>>Buku Akademik</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 px-1">Jumlah Stok</label>
                                <input type="number" name="stok" value="<?= $data['stok'] ?>" required
                                    class="input-style w-full px-6 py-4 rounded-2xl font-bold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-2 px-1">Ganti Cover (Opsional)</label>
                            <input type="file" name="cover" accept="image/*" onchange="previewImage(this)"
                                class="w-full text-xs text-slate-400 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-600 file:text-white hover:file:bg-blue-500 transition-all cursor-pointer">
                        </div>

                        <div class="pt-6">
                            <button name="update" 
                                class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-600/20 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const placeholder = document.getElementById('placeholder');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if(preview) {
                        preview.src = e.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'preview';
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';
                        placeholder.parentNode.appendChild(img);
                        placeholder.remove();
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>
</html>