<?php
session_start();

require '../config/Database.php';
require '../models/Barang.php';
require '../models/Transaksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$barangModel = new Barang($koneksi);
$transaksi = new Transaksi($koneksi);

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id_pengguna = $_SESSION['id'];
$barang = $barangModel->getAll();

$error = "";
if (isset($_POST['pinjam'])) {
    $id_barang = $_POST['barang'];
    $jumlah = $_POST['jumlah'];
    $tgl_kembali = $_POST['kembali'];
    $tgl_pinjam = date('Y-m-d');

    if ($tgl_kembali < $tgl_pinjam) {
        $error = "Tanggal pengembalian tidak valid!";
    } else {
        $cek = $barangModel->getById($id_barang);

        if ($jumlah > $cek['stok']) {
            $error = "Stok tidak mencukupi!";
        } else {
            $koneksi->query("INSERT INTO transaksi (id_pengguna, tanggal_pinjam, tanggal_kembali, status, kondisi) VALUES ('$id_pengguna', '$tgl_pinjam', '$tgl_kembali', 'menunggu', 'normal')");
            $id_transaksi = $koneksi->insert_id;
            $koneksi->query("INSERT INTO detail_transaksi VALUES('', '$id_transaksi', '$id_barang', '$jumlah')");

            header("Location: index.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Koleksi | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #080b14; color: #f1f5f9; }
        .glass-card { background: rgba(17, 24, 39, 0.4); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .input-style { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .input-style:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }
        input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(0.5); cursor: pointer; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[30%] h-[30%] bg-indigo-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="w-full max-w-4xl relative z-10 flex flex-col md:flex-row gap-8 items-center">
        <div class="hidden md:flex w-64 h-96 glass-card rounded-[2rem] overflow-hidden items-center justify-center relative group">
            <img id="book-cover" src="../assets/img/default-cover.jpg" class="w-full h-full object-cover opacity-40 transition-opacity" alt="Cover">
            <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                <p id="cover-text" class="text-[10px] font-black uppercase tracking-widest text-slate-500">Preview Cover</p>
            </div>
        </div>

        <div class="flex-1 w-full">
            <div class="flex items-center justify-between mb-8">
                <a href="index.php" class="p-3 bg-white/5 hover:bg-white/10 rounded-2xl border border-white/5 transition-all group">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-white group-hover:-translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                </a>
                <h1 class="text-lg font-extrabold tracking-[0.2em] uppercase">Biblio<span class="text-blue-500">Tech</span></h1>
            </div>

            <div class="glass-card rounded-[2.5rem] p-10 shadow-2xl">
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3 px-1">Pilih Koleksi Buku</label>
                        <div class="relative group">
                            <select name="barang" id="barang-select" required class="w-full input-style rounded-2xl px-5 py-4 text-white appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih salah satu...</option>
                                <?php while($b = $barang->fetch_assoc()) { ?>
                                    <option value="<?= $b['id'] ?>" data-cover="<?= $b['cover'] ?>" class="bg-slate-900">
                                        <?= $b['nama_barang'] ?> (Tersedia: <?= $b['stok'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3 px-1">Jumlah Pinjam</label>
                            <input type="number" name="jumlah" min="1" placeholder="" required class="w-full input-style rounded-2xl px-5 py-4 text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-500 mb-3 px-1">Tgl Pengembalian</label>
                            <input type="date" name="kembali" id="tgl_kembali" min="<?= date('Y-m-d') ?>" required class="w-full input-style rounded-2xl px-5 py-4 text-white font-bold">
                        </div>
                    </div>

                    <div class="pt-4">
                        <button name="pinjam" class="w-full py-5 bg-gradient-to-r from-blue-600 to-blue-500 hover:to-blue-400 text-white font-black text-xs uppercase tracking-[0.2em] rounded-2xl shadow-xl shadow-blue-600/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            Proses Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const select = document.getElementById('barang-select');
        const coverImg = document.getElementById('book-cover');
        const coverText = document.getElementById('cover-text');

        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const coverPath = selectedOption.getAttribute('data-cover');
            
            if (coverPath) {
                coverImg.src = '../uploads/' + coverPath;
                coverImg.classList.remove('opacity-40');
                coverImg.classList.add('opacity-100');
                coverText.classList.add('hidden');
            } else {
                coverImg.src = '../assets/img/default-cover.jpg';
                coverImg.classList.add('opacity-40');
                coverText.classList.remove('hidden');
            }
        });

        <?php if($error): ?>
        Swal.fire({ icon: 'error', title: 'Opps!', text: '<?= $error ?>', background: '#111827', color: '#fff', confirmButtonColor: '#2563eb' });
        <?php endif; ?>
    </script>
</body>
</html>