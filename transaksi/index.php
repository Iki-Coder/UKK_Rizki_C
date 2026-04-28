<?php
session_start();

require '../config/Database.php';

$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$dashboard_url = ($_SESSION['role'] == 'admin') ? '../admin/index.php' : '../petugas/index.php';

// Query diperbarui untuk mengambil nama_barang dan cover via JOIN
$data = $koneksi->query("
    SELECT t.*, p.username, b.nama_barang, b.cover 
    FROM transaksi t 
    JOIN pengguna p ON t.id_pengguna = p.id
    JOIN detail_transaksi dt ON t.id = dt.id_transaksi
    JOIN barang b ON dt.id_barang = b.id
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
            background-color: #0b0e1a;
            color: #f1f5f9;
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
<body class="min-h-screen p-8 lg:p-16">

    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h1 class="text-4xl font-black tracking-tighter mb-3 uppercase">Manajemen <span class="text-blue-500">Transaksi.</span></h1>
                <p class="text-slate-500 text-sm font-medium">Kelola persetujuan peminjaman dan verifikasi pengembalian barang.</p>
            </div>
            
            <div class="flex gap-3">
                <a href="<?= $dashboard_url ?>" class="bg-slate-800 hover:bg-slate-700 px-6 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all border border-white/5">
                    Dashboard
                </a>
                <a href="persetujuan.php" class="bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-white px-6 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all border border-amber-500/20">
                    Persetujuan
                </a>
                <a href="tambah.php" class="bg-blue-600 hover:bg-blue-500 px-8 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all shadow-xl shadow-blue-600/20">
                    Tambah Transaksi
                </a>
            </div>
        </header>

        <div class="card-glass rounded-[3rem] p-10 lg:p-12 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">
                            <th class="px-8 py-2 text-center">Cover</th>
                            <th class="px-8 py-2">Informasi Pinjam</th>
                            <th class="px-8 py-2">Tanggal Pinjam</th>
                            <th class="px-8 py-2 text-center">Status</th>
                            <th class="px-8 py-2 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($d = $data->fetch_assoc()) { 
                            $status = strtolower(trim($d['status']));
                            $kondisi = strtolower(trim($d['kondisi'] ?? 'normal'));

                            $label = ''; $dot = ''; $text = '';
                            if($status == 'menunggu') {
                                $label = 'Menunggu'; $dot = 'bg-slate-500'; $text = 'text-slate-400';
                            } elseif($status == 'dipinjam') {
                                $label = 'Dipinjam'; $dot = 'bg-amber-500'; $text = 'text-amber-500';
                            } elseif($status == 'menunggu pengecekan') {
                                $label = 'Verifikasi'; $dot = 'bg-blue-500'; $text = 'text-blue-400';
                            } elseif($status == 'kembali') {
                                if($kondisi == 'rusak') {
                                    $label = 'Kembali (Rusak)'; $dot = 'bg-orange-500'; $text = 'text-orange-400';
                                } elseif($kondisi == 'hilang') {
                                    $label = 'Hilang'; $dot = 'bg-red-500'; $text = 'text-red-500';
                                } else {
                                    $label = 'Kembali'; $dot = 'bg-emerald-500'; $text = 'text-emerald-400';
                                }
                            }
                        ?>
                        <tr class="table-row-custom">
                            <td class="px-8 py-7 rounded-l-[2rem] border-y border-l border-white/5">
                                <div class="w-16 h-20 bg-slate-800 rounded-xl overflow-hidden border border-white/5 mx-auto">
                                    <?php if(!empty($d['cover'])): ?>
                                        <img src="../uploads/<?= $d['cover'] ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-[8px] font-black opacity-20">NO IMAGE</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5">
                                <div class="font-black text-blue-500 text-[10px] mb-1">#<?= $d['id'] ?></div>
                                <div class="font-bold text-white uppercase tracking-tight text-base leading-tight"><?= $d['username'] ?></div>
                                <div class="text-[10px] text-slate-300 font-medium italic mb-2"><?= $d['nama_barang'] ?></div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5 text-sm font-bold text-slate-400">
                                <?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5">
                                <div class="flex items-center justify-center gap-2.5 <?= $text ?> text-[10px] font-black uppercase tracking-widest">
                                    <span class="w-2 h-2 rounded-full <?= $dot ?> shadow-[0_0_8px_rgba(0,0,0,0.5)]"></span>
                                    <?= $label ?>
                                </div>
                            </td>
                            <td class="px-8 py-7 rounded-r-[2rem] border-y border-r border-white/5 text-right">
                                <div class="flex justify-end gap-2">
                                    <?php if($status == 'menunggu pengecekan'): ?>
                                        <a href="verifikasi_kembali.php?id=<?= $d['id'] ?>" 
                                           class="bg-blue-600 hover:bg-blue-500 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                           🔍 Verifikasi
                                        </a>
                                    <?php endif; ?>
                                </div>
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