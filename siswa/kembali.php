<?php
session_start();
require '../config/Database.php';

$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_SESSION['id'];

$data = $koneksi->query("
    SELECT t.*, b.nama_barang, b.cover, d.jumlah
    FROM transaksi t
    JOIN detail_transaksi d ON t.id = d.id_transaksi
    JOIN barang b ON d.id_barang = b.id
    WHERE t.id_pengguna='$id' 
    AND t.status='dipinjam'
    ORDER BY t.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0e1a; color: #f1f5f9; }
        .card-glass { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .table-row-custom { background: rgba(255, 255, 255, 0.01); transition: all 0.2s ease; }
        .swal2-input-custom {
            background: #1e293b !important;
            color: white !important;
            border-radius: 12px !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            font-family: 'Plus Jakarta Sans' !important;
        }
    </style>
</head>
<body class="min-h-screen p-8 lg:p-16">
    <div class="max-w-6xl mx-auto">
        <header class="flex justify-between items-end mb-12">
            <div>
                <h1 class="text-4xl font-black tracking-tighter mb-3 uppercase">Pengembalian <span class="text-blue-500">Buku.</span></h1>
                <p class="text-slate-500 text-sm font-medium">Pilih jumlah buku yang ingin dikembalikan.</p>
            </div>
            <a href="index.php" class="bg-slate-800 px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest border border-white/5">Kembali</a>
        </header>

        <div class="card-glass rounded-[3rem] p-10 lg:p-12 shadow-2xl">
            <table class="w-full text-left border-separate border-spacing-y-4">
                <thead>
                    <tr class="text-[10px] font-black text-slate-600 uppercase tracking-widest">
                        <th class="px-8 py-2">Buku</th>
                        <th class="px-8 py-2">Detail</th>
                        <th class="px-8 py-2 text-center">Dipinjam</th>
                        <th class="px-8 py-2">Batas Waktu</th>
                        <th class="px-8 py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($data->num_rows > 0): ?>
                        <?php while($d = $data->fetch_assoc()): ?>
                        <tr class="table-row-custom">
                            <td class="px-8 py-7 rounded-l-[2rem] border-y border-l border-white/5">
                                <div class="w-14 h-20 rounded-xl overflow-hidden bg-slate-800 border border-white/5">
                                    <?php if(!empty($d['cover'])): ?>
                                        <img src="../uploads/<?= $d['cover'] ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-[8px] font-black opacity-20 uppercase">No Cover</div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5">
                                <div class="font-bold text-lg uppercase text-white leading-tight"><?= $d['nama_barang'] ?></div>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5 text-center">
                                <span class="bg-white/5 px-4 py-2 rounded-xl font-bold text-sm"><?= $d['jumlah'] ?></span>
                            </td>
                            <td class="px-8 py-7 border-y border-white/5 text-sm text-slate-400 font-medium">
                                <?= date('d M Y', strtotime($d['tanggal_kembali'])) ?>
                            </td>
                            <td class="px-8 py-7 rounded-r-[2rem] border-y border-r border-white/5 text-right">
                                <button onclick="openReturnModal(<?= $d['id'] ?>, '<?= addslashes($d['nama_barang']) ?>', <?= $d['jumlah'] ?>)" 
                                    class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-600/20">
                                    Kembalikan
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-24 text-slate-500 uppercase tracking-widest text-[10px] font-bold italic">Tidak ada buku yang sedang dipinjam</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    async function openReturnModal(id, nama, maxJumlah) {
        const { value: jumlah } = await Swal.fire({
            title: '<span style="font-family:Plus Jakarta Sans; font-weight:800; font-size: 18px; text-transform: uppercase;">Kembalikan ' + nama + '</span>',
            html: `<p style="color:#94a3b8; font-size:13px; margin-bottom:15px;">Maksimal yang bisa dikembalikan: <b>${maxJumlah}</b></p>`,
            input: 'number',
            inputAttributes: {
                min: 1,
                max: maxJumlah,
                step: 1
            },
            inputValue: maxJumlah,
            showCancelButton: true,
            confirmButtonText: 'Ajukan',
            confirmButtonColor: '#2563eb',
            background: '#0f172a',
            color: '#fff',
            customClass: {
                input: 'swal2-input-custom',
                popup: 'rounded-[2rem] border border-white/10'
            },
            inputValidator: (value) => {
                if (!value || value <= 0) return 'Jumlah tidak valid!'
                if (value > maxJumlah) return 'Melebihi jumlah pinjaman!'
            }
        });

        if (jumlah) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'proses_kembali.php';
            
            const fields = { id: id, jumlah_kembali: jumlah };
            for (const key in fields) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>