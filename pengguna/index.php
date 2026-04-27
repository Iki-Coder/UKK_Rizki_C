<?php
session_start();
require '../config/Database.php';
$db = new Database();
$koneksi = $db->koneksi;

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$users = $koneksi->query("SELECT * FROM pengguna WHERE role IN ('admin', 'petugas') ORDER BY role ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Staff | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#0b0f1a] text-slate-200 p-8 font-['Plus_Jakarta_Sans']">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[30%] h-[30%] bg-blue-600/5 blur-[100px] rounded-full"></div>
    </div>

    <div class="max-w-5xl mx-auto relative z-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
            <div class="flex items-center gap-4">
                <a href="../admin/index.php" class="p-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl hover:bg-slate-700 transition group" title="Kembali ke Dashboard">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white tracking-tight">Manajemen Staff</h1>
                    <p class="text-slate-500 text-sm">Kelola akun Administrator dan Petugas.</p>
                </div>
            </div>
            
            <a href="tambah.php" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-blue-600/20 active:scale-95 text-center">
                + Tambah Staff Baru
            </a>
        </div>

        <div class="bg-slate-900/40 border border-slate-800/50 rounded-[2rem] overflow-hidden backdrop-blur-xl shadow-2xl">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-800/30 text-slate-500 text-[10px] uppercase tracking-[0.2em] font-black">
                        <th class="px-8 py-5">Username</th>
                        <th class="px-8 py-5 text-center">Role Akses</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    <?php while($u = $users->fetch_assoc()): ?>
                    <tr class="hover:bg-blue-500/[0.02] transition group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <span class="font-semibold text-white"><?= $u['username'] ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="inline-block px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border <?= $u['role'] == 'admin' ? 'border-blue-500/30 text-blue-500 bg-blue-500/5' : 'border-emerald-500/30 text-emerald-500 bg-emerald-500/5' ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <?php if($u['username'] != $_SESSION['username']): ?>
                                <a href="hapus.php?id=<?= $u['id'] ?>" onclick="return confirm('Yakin ingin menghapus staff ini?')" class="text-rose-500 hover:text-white hover:bg-rose-500 px-4 py-2 rounded-lg text-xs font-bold transition-all">
                                    Hapus
                                </a>
                            <?php else: ?>
                                <span class="text-slate-600 text-xs italic">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>