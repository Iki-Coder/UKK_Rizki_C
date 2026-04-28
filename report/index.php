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
$where = "WHERE 1=1";

if (isset($_GET['status']) && $_GET['status'] != "") {
    $status = $koneksi->real_escape_string($_GET['status']);
    if ($status == 'rusak') {
        $where = "WHERE t.kondisi='rusak'";
    } elseif ($status == 'hilang') {
        $where = "WHERE t.kondisi='hilang'";
    } elseif ($status == 'kembali') {
        $where = "WHERE t.status='kembali' AND t.kondisi='normal'";
    } elseif ($status == 'dipinjam') {
        $where = "WHERE t.status='dipinjam'";
    } elseif ($status == 'menunggu') {
        $where = "WHERE t.status='menunggu'";
    } elseif ($status == 'cek') {
        $where = "WHERE t.status='menunggu pengecekan'";
    }
}

$query = "
    SELECT t.*, p.username, b.nama_barang
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    $where
    ORDER BY t.id DESC
";
$data = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi | Bibliotech</title>
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
        select option { background-color: #1e293b; color: white; }
    </style>
</head>

<body class="min-h-screen p-8 lg:p-16">

    <div class="max-w-full mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
            <div>
                <h1 class="text-4xl font-black tracking-tighter mb-3 uppercase">Laporan <span class="text-blue-500">Transaksi.</span></h1>
                <p class="text-slate-500 text-sm font-medium">Monitoring peminjaman dan pengembalian barang secara detail.</p>
            </div>
            
            <div class="flex gap-3">
                <a href="<?= $dashboard_url ?>" class="bg-slate-800 hover:bg-slate-700 px-6 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all border border-white/5">
                    Dashboard
                </a>
                <button onclick="window.print()" class="bg-white/5 hover:bg-white/10 px-6 py-3.5 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all border border-white/5">
                    Cetak Laporan
                </button>
            </div>
        </header>

        <div class="card-glass rounded-3xl p-6 mb-8">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-1 mb-2 block">Filter Status</label>
                    <select name="status" class="w-full bg-white/5 border border-white/10 px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-blue-500 transition-all">
                        <option value="">Semua Data</option>
                        <option value="dipinjam" <?= @$_GET['status'] == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                        <option value="kembali" <?= @$_GET['status'] == 'kembali' ? 'selected' : '' ?>>Kembali (Normal)</option>
                        <option value="rusak" <?= @$_GET['status'] == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                        <option value="hilang" <?= @$_GET['status'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                        <option value="cek" <?= @$_GET['status'] == 'cek' ? 'selected' : '' ?>>Menunggu Pengecekan</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full md:w-auto bg-blue-600 hover:bg-blue-500 px-8 py-3 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-600/20">
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="card-glass rounded-[2.5rem] p-6 lg:p-10 shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">
                            <th class="px-6 py-2">No</th>
                            <th class="px-6 py-2">Peminjam</th>
                            <th class="px-6 py-2">Nama Barang</th>
                            <th class="px-6 py-2 text-center">Status</th>
                            <th class="px-6 py-2 text-center">Waktu Pinjam</th>
                            <th class="px-6 py-2 text-center">Waktu Kembali</th>
                            <th class="px-6 py-2 text-right">Denda</th>
                            <th class="px-6 py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no=1; 
                        while($d = mysqli_fetch_assoc($data)) { 
                            $today = date('Y-m-d');
                            $denda = 0;
                            
                            // Hitung denda keterlambatan
                            if ($today > $d['tanggal_kembali'] && $d['status'] == 'dipinjam') {
                                $telat = (strtotime($today) - strtotime($d['tanggal_kembali'])) / 86400;
                                $denda += $telat * 1000;
                            }
                            if ($d['kondisi'] == 'rusak') $denda += 5000;
                            if ($d['kondisi'] == 'hilang') $denda += 20000;
                            if ($d['denda_bayar'] == 1) $denda = 0;

                            $label = ''; $dot = ''; $text_color = '';
                            if ($d['status'] == 'menunggu') {
                                $label = 'Menunggu'; $dot = 'bg-slate-500'; $text_color = 'text-slate-400';
                            } elseif ($d['status'] == 'dipinjam') {
                                $label = 'Dipinjam'; $dot = 'bg-amber-500'; $text_color = 'text-amber-500';
                            } elseif ($d['status'] == 'menunggu pengecekan') {
                                $label = 'Cek'; $dot = 'bg-blue-500'; $text_color = 'text-blue-400';
                            } elseif ($d['status'] == 'kembali' && $d['kondisi'] == 'normal') {
                                $label = 'Kembali'; $dot = 'bg-emerald-500'; $text_color = 'text-emerald-400';
                            } elseif ($d['kondisi'] == 'rusak') {
                                $label = 'Rusak'; $dot = 'bg-amber-400'; $text_color = 'text-amber-400';
                            } elseif ($d['kondisi'] == 'hilang') {
                                $label = 'Hilang'; $dot = 'bg-rose-500'; $text_color = 'text-rose-500';
                            }
                        ?>
                        <tr class="table-row-custom">
                            <td class="px-6 py-6 rounded-l-2xl border-y border-l border-white/5 text-slate-500 font-bold text-xs">
                                <?= $no++ ?>
                            </td>
                            <td class="px-6 py-6 border-y border-white/5">
                                <div class="font-bold text-white uppercase tracking-tight text-sm"><?= $d['username'] ?></div>
                            </td>
                            <td class="px-6 py-6 border-y border-white/5">
                                <div class="font-medium text-slate-300 text-sm"><?= $d['nama_barang'] ?></div>
                            </td>
                            <td class="px-6 py-6 border-y border-white/5">
                                <div class="flex items-center justify-center gap-2 <?= $text_color ?> text-[10px] font-black uppercase tracking-widest">
                                    <span class="w-2 h-2 rounded-full <?= $dot ?> shadow-[0_0_8px_rgba(0,0,0,0.5)]"></span>
                                    <?= $label ?>
                                </div>
                            </td>
                            <td class="px-6 py-6 border-y border-white/5 text-center">
                                <span class="text-xs font-bold text-slate-300"><?= date('d/m/Y', strtotime($d['tanggal_pinjam'])) ?></span>
                            </td>
                            <td class="px-6 py-6 border-y border-white/5 text-center">
                                <?php if($d['status'] == 'dipinjam'): ?>
                                    <span class="text-[9px] text-amber-500/50 font-black uppercase italic">Batas: <?= date('d/m/Y', strtotime($d['tanggal_kembali'])) ?></span>
                                <?php elseif(in_array($d['status'], ['kembali', 'rusak', 'hilang'])): ?>
                                    <span class="text-xs font-bold text-emerald-400"><?= date('d/m/Y', strtotime($d['tanggal_kembali'])) ?></span>
                                <?php else: ?>
                                    <span class="text-slate-700 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-6 border-y border-white/5 text-right">
                                <?php if (in_array($d['status'], ['kembali', 'rusak', 'hilang']) || ($d['status'] == 'dipinjam' && $denda > 0)): ?>
                                    <?php if($denda > 0): ?>
                                        <span class="text-rose-500 font-black text-xs tracking-tight">Rp <?= number_format($denda,0,',','.') ?></span>
                                    <?php else: ?>
                                        <span class="text-emerald-500 font-black text-[10px] uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">Lunas</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-slate-700 text-xs font-black uppercase tracking-widest">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-6 rounded-r-2xl border-y border-r border-white/5 text-right">
                                <?php if($denda > 0 && $d['denda_bayar'] == 0 && in_array($d['status'], ['kembali', 'rusak', 'hilang'])): ?>
                                    <a href="bayar_denda.php?id=<?= $d['id'] ?>" onclick="return confirm('Tandai sudah bayar?')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">
                                       ✔ Bayar
                                    </a>
                                <?php else: ?>
                                    <span class="text-slate-700 text-xs">—</span>
                                <?php endif; ?>
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