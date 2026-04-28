<?php
session_start();
require '../config/Database.php';
require '../models/Pengguna.php';

$db = new Database();
$koneksi = $db->koneksi;
$pengguna = new Pengguna($koneksi);

if (!isset($_SESSION['login']) || ($_SESSION['role'] != 'petugas' && $_SESSION['role'] != 'admin')) {
    header("Location: ../auth/login.php");
    exit;
}

$data_siswa = null;
$barcode = null;

if (isset($_GET['barcode'])) { $barcode = $_GET['barcode']; }
if (isset($_POST['cari'])) { $barcode = $_POST['barcode']; }

if ($barcode) {
    $data_siswa = $pengguna->getByBarcode($barcode);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Siswa | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #0b0e1a;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(37, 99, 235, 0.05) 0px, transparent 50%);
        }
        
        .card-glass {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        #reader { 
            border: none !important; 
            background: transparent !important;
        }

        #reader video { 
            border-radius: 1.5rem !important;
            object-fit: cover !important;
        }

        /* Styling internal library scanner */
        #reader__dashboard_section_csr button {
            background: #2563eb !important;
            color: white !important;
            border-radius: 0.75rem !important;
            padding: 10px 20px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 11px !important;
            letter-spacing: 0.05em !important;
            border: none !important;
            transition: all 0.2s;
        }

        #reader__dashboard_section_csr button:hover {
            background: #3b82f6 !important;
            transform: translateY(-1px);
        }

        #reader__camera_selection {
            background: #1e293b !important;
            color: white !important;
            border-radius: 0.5rem !important;
            padding: 5px !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
        }

        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: #3b82f6;
            box-shadow: 0 0 15px #3b82f6;
            animation: scan 2s linear infinite;
            z-index: 10;
        }

        @keyframes scan {
            0% { top: 0%; }
            100% { top: 100%; }
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen p-4 md:p-8 flex items-center justify-center">

    <div class="max-w-xl w-full">
        <div class="flex items-center justify-between mb-8 px-2">
            <div>
                <h1 class="text-3xl font-black tracking-tighter text-white uppercase">
                    Scan <span class="text-blue-500">QR.</span>
                </h1>
            </div>
            <a href="index.php" class="bg-slate-800/50 hover:bg-slate-800 px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/5 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="space-y-6">
            <div class="card-glass rounded-[2.5rem] p-4 shadow-2xl relative overflow-hidden">
                <div class="relative group">
                    <div id="reader" class="overflow-hidden rounded-[1.8rem] bg-slate-950/50 border border-white/5"></div>
                </div>

                <div class="p-4 mt-2">
                    <p class="text-slate-500 text-center text-xs font-medium leading-relaxed px-4">
                        Arahkan kamera ke QR Code pada kartu siswa untuk melakukan proses peminjaman otomatis.
                    </p>
                </div>
            </div>

            <div class="card-glass rounded-3xl p-6 shadow-xl">
                <h3 class="text-white text-xs font-black uppercase tracking-widest mb-4 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                    Input Manual
                </h3>
                <form method="POST" class="flex gap-3">
                    <input type="text" name="barcode" placeholder="Masukkan Nomor Barcode" required
                        class="flex-1 bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-sm text-white placeholder:text-slate-600 focus:ring-2 focus:ring-blue-500/50 focus:outline-none transition-all">
                    <button name="cari" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3.5 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all transform active:scale-95 shadow-lg shadow-blue-600/20">
                        Cari
                    </button>
                </form>
            </div>

            <?php if ($barcode && !$data_siswa) { ?>
            <div class="bg-rose-500/10 border border-rose-500/20 p-5 rounded-2xl text-rose-400 text-center text-[11px] font-black uppercase tracking-widest flex items-center justify-center gap-3 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Data siswa tidak ditemukan!
            </div>
            <?php } ?>

            <?php if ($data_siswa) { ?>
            <div class="bg-blue-600 rounded-[2.5rem] p-[1px] shadow-2xl shadow-blue-600/20 animate-in fade-in zoom-in duration-300">
                <div class="bg-[#0f172a] rounded-[2.45rem] p-8">
                    <div class="flex flex-col items-center text-center">
                        <div class="relative mb-6">
                            <div class="absolute -inset-4 bg-blue-500/20 blur-xl rounded-full"></div>
                            <div class="bg-white p-4 rounded-3xl shadow-2xl relative">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $data_siswa['barcode'] ?>" 
                                     alt="QR" class="w-24 h-24">
                            </div>
                        </div>
                        
                        <h4 class="text-2xl font-black text-white tracking-tight"><?= $data_siswa['username'] ?></h4>
                        <div class="mt-4 flex flex-col gap-2 w-full">
                            <div class="flex justify-between items-center bg-white/5 px-4 py-3 rounded-xl border border-white/5">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">ID Siswa</span>
                                <span class="text-sm font-bold text-blue-400 font-mono"><?= $data_siswa['id'] ?></span>
                            </div>
                            <div class="flex justify-between items-center bg-white/5 px-4 py-3 rounded-xl border border-white/5">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Barcode</span>
                                <span class="text-sm font-bold text-slate-200 font-mono"><?= $data_siswa['barcode'] ?></span>
                            </div>
                        </div>

                        <a href="tambah.php?id=<?= $data_siswa['id'] ?>" class="w-full mt-8">
                            <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black text-[11px] tracking-[0.1em] py-5 rounded-2xl shadow-xl shadow-blue-600/30 transition-all transform hover:-translate-y-1 active:scale-[0.98] uppercase">
                                PINJAMKAN BARANG SEKARANG
                            </button>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText) {
            // Hentikan scanner sementara agar tidak redirect berkali-kali
            scanner.clear();
            window.location.href = "scan.php?barcode=" + decodedText;
        }

        let scanner = new Html5QrcodeScanner(
            "reader", { 
                fps: 20,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                showTorchButtonIfSupported: true
            }
        );
        scanner.render(onScanSuccess);
    </script>

</body>
</html>