<?php session_start(); ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#0b0f1a] text-slate-200 min-h-screen flex flex-col">

    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[35%] h-[35%] bg-blue-600/10 blur-[100px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[35%] h-[35%] bg-indigo-600/10 blur-[100px] rounded-full"></div>
    </div>

    <nav class="relative z-10 border-b border-slate-800/40 bg-[#0b0f1a]/60 backdrop-blur-lg">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="../index.php" class="text-xl font-bold tracking-tighter text-white">
                BIBLIO<span class="text-blue-500">TECH</span>
            </a>
            <div class="flex items-center gap-6">
                <a href="../register.php" class="text-sm font-semibold bg-white text-black py-2.5 px-6 rounded-full hover:bg-slate-200 transition-all active:scale-95 shadow-lg shadow-white/5">
                    Daftar Akun
                </a>
            </div>
        </div>
    </nav>

    <main class="relative z-10 flex-grow flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <div class="bg-slate-900/40 backdrop-blur-xl p-10 rounded-3xl border border-slate-800/50 shadow-2xl shadow-blue-950/20">
                
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight mb-2">Selamat Datang</h2>
                    <p class="text-slate-400 font-light text-sm">Silakan masuk untuk mulai meminjam buku.</p>
                </div>

                <form action="proses_login.php" method="POST" class="space-y-6">
                    
                    <div>
                        <label class="block text-xs uppercase tracking-widest font-bold text-slate-500 mb-2.5">Username</label>
                        <input type="text" name="username" 
                            class="w-full px-5 py-4 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm"
                            placeholder="Masukkan username Anda" required autofocus>
                    </div>

                    <div>
                        <div class="flex justify-between mb-2.5">
                            <label class="text-xs uppercase tracking-widest font-bold text-slate-500">Password</label>
                        </div>
                        <input type="password" name="password" 
                            class="w-full px-5 py-4 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 text-sm"
                            placeholder="••••••••" required>
                    </div>

                    <div class="pt-2">
                        <button type="submit" name="login" 
                            class="w-full py-4 px-6 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-full transition-all hover:-translate-y-0.5 active:scale-95 shadow-lg shadow-blue-600/20 duration-200 text-sm">
                            Masuk ke Akun
                        </button>
                    </div>

                    <div class="relative flex py-4 items-center">
                        <div class="flex-grow border-t border-slate-800"></div>
                        <span class="flex-shrink mx-4 text-slate-600 text-xs uppercase tracking-widest">Atau</span>
                        <div class="flex-grow border-t border-slate-800"></div>
                    </div>

                    <div class="text-center">
                        <p class="text-slate-500 text-sm mb-4">Belum memiliki akun Bibliotech?</p>
                        <a href="../register.php" 
                            class="w-full inline-block py-3.5 px-6 bg-slate-800/50 hover:bg-slate-800 text-white font-semibold rounded-full border border-slate-700/50 transition duration-200 text-center text-sm">
                            Daftar Akun Baru
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </main>

</body>
</html>