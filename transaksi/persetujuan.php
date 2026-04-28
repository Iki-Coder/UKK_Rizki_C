<?php
session_start();

require '../config/Database.php';
require '../models/Transaksi.php';

$db = new Database();
$koneksi = $db->koneksi;

$transaksi = new Transaksi($koneksi);

if (!isset($_SESSION['login']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'petugas')) {
    header("Location: ../auth/login.php");
    exit;
}

$data = $koneksi->query("
    SELECT t.*, p.username, b.nama_barang, d.jumlah
    FROM transaksi t
    JOIN pengguna p ON t.id_pengguna = p.id
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    WHERE t.status='menunggu'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #080b14;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="min-h-screen p-6 md:p-12 relative overflow-x-hidden">

<div class="max-w-7xl mx-auto">

    <div class="mb-10 flex justify-between items-center">
        <h2 class="text-3xl font-bold text-white">Antrean Persetujuan</h2>
        <a href="../transaksi/index.php" class="bg-slate-700 px-4 py-2 rounded-xl text-sm">Kembali</a>
    </div>

    <div class="glass-card rounded-3xl overflow-hidden">
        <table class="w-full">
            <thead class="text-xs text-slate-500 uppercase">
                <tr>
                    <th class="p-4">No</th>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th>Tanggal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php if($data->num_rows > 0): ?>
                <?php $no=1; while($d = $data->fetch_assoc()): ?>
                <tr class="border-t border-white/5">
                    <td class="p-4"><?= $no++ ?></td>
                    <td><?= $d['username'] ?></td>
                    <td><?= $d['nama_barang'] ?> (<?= $d['jumlah'] ?>)</td>
                    <td><?= $d['tanggal_pinjam'] ?></td>
                    <td class="text-center">
                        <div class="flex justify-center gap-2">

                            <!-- APPROVE -->
                            <form action="proses_persetujuan.php" method="GET">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <button type="submit"
                                    onclick="return confirm('Setujui peminjaman ini?')"
                                    class="bg-green-600 hover:bg-green-500 px-3 py-2 rounded-lg">
                                    ✔
                                </button>
                            </form>

                            <!-- TOLAK -->
                            <form action="tolak.php" method="GET">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <button type="submit"
                                    onclick="return confirm('Tolak peminjaman ini?')"
                                    class="bg-red-600 hover:bg-red-500 px-3 py-2 rounded-lg">
                                    ✖
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-10 text-slate-500">
                        Tidak ada antrean
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>

</body>
</html>