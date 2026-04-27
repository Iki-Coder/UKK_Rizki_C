<?php
session_start();
require '../config/Database.php';
$db = new Database();
$koneksi = $db->koneksi;

if (isset($_POST['simpan'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $query = "INSERT INTO pengguna (username, password, role) VALUES ('$username', '$password', '$role')";
    
    if ($koneksi->query($query)) {
        echo "<script>alert('Akun $role berhasil dibuat!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Staff | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b0f1a] text-slate-200 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-slate-900/50 border border-slate-800 p-8 rounded-[2.5rem] backdrop-blur-xl">
        <h2 class="text-2xl font-bold text-white mb-6">Buat Akun Staff</h2>
        
        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-2">Username</label>
                <input type="text" name="username" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-2">Password</label>
                <input type="password" name="password" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-widest block mb-2">Role Jabatan</label>
                <select name="role" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-white outline-none">
                    <option value="petugas">Petugas Perpustakaan</option>
                    <option value="admin">Administrator</option>
                </select>
            </div>
            <button type="submit" name="simpan" class="w-full bg-blue-600 hover:bg-blue-500 py-3 rounded-xl font-bold text-white transition shadow-lg shadow-blue-500/20">
                Daftarkan Staff
            </button>
            <a href="index.php" class="block text-center text-slate-500 text-sm hover:text-white transition">Batal</a>
        </form>
    </div>
</body>
</html>