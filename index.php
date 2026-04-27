<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotech - Katalog Perpustakaan Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0b0f1a] text-slate-200 min-h-screen">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[35%] h-[35%] bg-blue-600/10 blur-[100px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[35%] h-[35%] bg-indigo-600/10 blur-[100px] rounded-full"></div>
    </div>

    <nav class="relative z-10 border-b border-slate-800/40 bg-[#0b0f1a]/60 backdrop-blur-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="#" class="text-xl font-bold tracking-tighter text-white">
                BIBLIO<span class="text-blue-500">TECH</span>
            </a>
            <div class="flex items-center gap-6">
                <a href="login.php" class="text-sm font-medium text-slate-400 hover:text-white transition-colors">Masuk</a>
                <a href="register.php" class="text-sm font-semibold bg-white text-black py-2.5 px-6 rounded-full hover:bg-slate-200 transition-all active:scale-95 shadow-lg shadow-white/5">
                    Daftar Akun
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-10 flex flex-col items-center justify-center min-h-[80vh] px-6 text-center">
        <div class="max-w-3xl">            
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tight">
                Cari Buku <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Gak Pakai Ribet.</span>
            </h1>
            
            <p class="text-lg text-slate-400 mb-10 leading-relaxed max-w-xl mx-auto font-light">
                Cek ketersediaan buku fisik di perpustakaan secara real-time. Pesan dari kelas, ambil di perpustakaan saat jam istirahat.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="login.php" class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 font-bold text-white bg-blue-600 rounded-2xl transition-all hover:bg-blue-500 hover:-translate-y-1 active:scale-95 shadow-[0_20px_40px_-15px_rgba(37,99,235,0.4)]">
                <a href="auth/login.php" class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 font-bold text-white bg-blue-600 rounded-2xl transition-all hover:bg-blue-500 hover:-translate-y-1 active:scale-95 shadow-[0_20px_40px_-15px_rgba(37,99,235,0.4)]">
                    Pinjam Buku
                </a>
            </div>
            </div>
        </div>
    </main>

</body>
</html>