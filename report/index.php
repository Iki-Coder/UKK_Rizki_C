<?php       
session_start();
require '../config/Database.php'; 
$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$where = "WHERE 1=1";
if (isset($_GET['status']) && $_GET['status'] != "") {
    $status = $koneksi->real_escape_string($_GET['status']);
    
    if ($status == 'rusak') {
        $where = "WHERE t.kondisi='rusak' AND t.status='kembali'";
    } elseif ($status == 'hilang') {
        $where = "WHERE t.kondisi='hilang'";
    } elseif ($status == 'kembali') {
        $where = "WHERE t.status='kembali' AND t.kondisi='normal'";
    } else {
        $where = "WHERE t.status='$status'";
    }
}

$query = "
    SELECT t.*, p.username, b.nama_barang, d.jumlah
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #080b14;
            color: #cbd5e1;
        }
        .kartu-laporan {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 2rem;
        }
        .badge {
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            border: 1px solid;
        }
        /* Style Warna Status */
        .status-dipinjam { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
        .status-kembali  { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
        .status-rusak    { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
        .status-hilang   { background: rgba(244, 63, 94, 0.1); color: #f43f5e; border-color: rgba(244, 63, 94, 0.2); }
    </style>
</head>
<body class="min-h-screen p-6 md:p-12">

    <main class="container mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
            <div class="flex items-center gap-5">
                <a href="../admin/index.php" class="p-3 bg-slate-800/40 border border-white/5 rounded-2xl hover:bg-slate-700 transition group shadow-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tighter uppercase">Laporan Transaksi</h1>
                    <p class="text-slate-500 text-sm font-medium italic">Monitor riwayat sirkulasi & kondisi aset.</p>
                </div>
            </div>

            <form method="GET" class="flex items-center gap-3">
                <select name="status" class="bg-slate-900 border border-slate-700 text-slate-300 text-sm rounded-xl px-5 py-3 outline-none focus:border-blue-500 transition shadow-inner">
                    <option value="">Semua Status</option>
                    <option value="dipinjam" <?= @$_GET['status'] == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                    <option value="kembali" <?= @$_GET['status'] == 'kembali' ? 'selected' : '' ?>>Kembali (Bagus)</option>
                    <option value="rusak" <?= @$_GET['status'] == 'rusak' ? 'selected' : '' ?>>Kembali (Rusak)</option>
                    <option value="hilang" <?= @$_GET['status'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                </select>
                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg shadow-blue-600/20 active:scale-95">
                    Filter
                </button>
            </form>
        </div>

        <div class="kartu-laporan overflow-hidden shadow-2xl backdrop-blur-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-800/30 text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black">
                            <th class="px-8 py-6">No</th>
                            <th class="px-6 py-6">Peminjam</th>
                            <th class="px-6 py-6">Nama Barang</th>
                            <th class="px-6 py-6 text-center">Qty</th>
                            <th class="px-6 py-6">Tgl Pinjam</th>
                            <th class="px-8 py-6 text-right">Kondisi & Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <?php 
                        $no = 1; 
                        if(mysqli_num_rows($data) > 0) {
                            while($d = mysqli_fetch_assoc($data)) { 
                                $label = strtoupper($d['status']);
                                $class = "status-dipinjam";

                                if($d['kondisi'] == 'rusak') {
                                    $label = "Kembali (Rusak)";
                                    $class = "status-rusak";
                                } elseif($d['kondisi'] == 'hilang') {
                                    $label = "Hilang";
                                    $class = "status-hilang";
                                } elseif($d['status'] == 'kembali') {
                                    $label = "Kembali (Bagus)";
                                    $class = "status-kembali";
                                }
                        ?>
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-6 text-slate-600 font-medium"><?= $no++ ?></td>
                            <td class="px-6 py-6 font-bold text-white group-hover:text-blue-400 transition-colors uppercase tracking-tight italic"><?= $d['username'] ?></td>
                            <td class="px-6 py-6 font-medium"><?= $d['nama_barang'] ?></td>
                            <td class="px-6 py-6 text-center font-bold text-slate-500"><?= $d['jumlah'] ?></td>
                            <td class="px-6 py-6 text-xs font-semibold text-slate-400 tracking-tighter">
                                <?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="badge <?= $class ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                        </tr>
                        <?php 
                            } 
                        } else {
                            echo "<tr><td colspan='6' class='px-8 py-20 text-center text-slate-600 italic tracking-[0.2em] text-xs font-bold uppercase'>Data tidak ditemukan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex justify-between items-center px-4">
            <p class="text-[9px] text-slate-700 font-black uppercase tracking-[0.5em]">&copy; 2026 Bibliotech System • Laporan</p>
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest italic">Total: <?= mysqli_num_rows($data) ?> Transaksi Terdata</p>
        </div>
    </main>

</body>
</html>