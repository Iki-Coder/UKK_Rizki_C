<?php
session_start();
require '../config/Database.php';

$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_transaksi <= 0) {
    header("Location: index.php");
    exit;
}

$query = "
    SELECT t.*, p.username, b.nama_barang, d.id_barang, d.jumlah 
    FROM transaksi t 
    JOIN pengguna p ON t.id_pengguna = p.id 
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    WHERE t.id = '$id_transaksi'
";

$result = $koneksi->query($query);
$data = $result->fetch_assoc();

if (!$data || strtolower($data['status']) != 'menunggu pengecekan') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 🔥 kondisi dari form
    $kondisi = $_POST['kondisi'];
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $id_barang = $data['id_barang'];
    $jumlah = $data['jumlah'];

    // 🔥 status SELALU kembali (karena proses selesai)
    $status = 'kembali';

    $update = "
        UPDATE transaksi SET 
            status = '$status',
            kondisi = '$kondisi',
            catatan_petugas = '$catatan'
        WHERE id = '$id_transaksi'
    ";

    if ($koneksi->query($update)) {

        // 🔥 stok balik cuma kalau kondisi normal
        if ($kondisi == 'normal') {
            $koneksi->query("
                UPDATE barang 
                SET stok = stok + $jumlah 
                WHERE id = '$id_barang'
            ");
        }

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Fisik | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #05070a;
            color: #f1f5f9;
        }
        .glass-panel {
            background: rgba(17, 24, 39, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[20%] left-[30%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="max-w-lg w-full relative z-10">
        <div class="glass-panel p-10 rounded-[3rem] shadow-2xl">
            
            <div class="mb-10">
                <div class="flex items-center gap-3 mb-4">
                    <div class="h-[2px] w-8 bg-blue-500"></div>
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.4em]">Inspector Panel</span>
                </div>
                <h2 class="text-3xl font-black tracking-tighter text-white uppercase">Verifikasi Barang <span class="text-blue-500">.</span></h2>
                <p class="text-slate-500 text-xs mt-2 font-medium">Periksa kondisi fisik barang sebelum masuk kembali ke gudang.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Peminjam</p>
                    <p class="text-sm font-bold text-white uppercase"><?= $data['username'] ?></p>
                </div>
                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-1">Nama Barang</p>
                    <p class="text-sm font-bold text-blue-400 uppercase"><?= $data['nama_barang'] ?></p>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Kondisi Pengembalian</label>
                    <select name="kondisi" required class="w-full bg-slate-800/50 border border-white/10 p-4 rounded-2xl focus:outline-none focus:border-blue-500 transition-all font-bold text-sm">
                        <option value="normal">BAIK</option>
                        <option value="rusak">RUSAK</option>
                        <option value="hilang">HILANG</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Catatan Pemeriksaan</label>
                    <textarea name="catatan" class="w-full bg-slate-800/50 border border-white/10 p-4 rounded-2xl focus:outline-none focus:border-blue-500 transition-all text-sm h-32"></textarea>
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <a href="index.php" class="flex-1 text-center py-4 rounded-2xl bg-white/5 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-white/10 transition-all border border-white/5">
                        Batal
                    </a>
                    <button type="submit" class="flex-[2] py-4 rounded-2xl bg-blue-600 font-black text-[10px] uppercase tracking-[0.2em] hover:bg-blue-500 shadow-xl shadow-blue-600/20 transition-all text-white">
                        Selesaikan Verifikasi
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center mt-8 text-[9px] font-black text-slate-700 uppercase tracking-[0.5em]">Bibliotech Digital Inspector • V.3.0</p>
    </div>

</body>
</html>