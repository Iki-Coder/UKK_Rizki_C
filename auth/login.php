<?php 
session_start(); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | Bibliotech</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #0b0f1a;
        }
        .card-glass {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex flex-col overflow-x-hidden">

    <div class="fixed top-0 left-0 w-full h-full pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-5%] w-[40%] h-[40%] bg-blue-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[40%] h-[40%] bg-indigo-600/10 blur-[120px] rounded-full"></div>
    </div>

    <nav class="relative z-10 border-b border-white/5 bg-[#0b0f1a]/60 backdrop-blur-lg">
        <div class="container mx-auto px-8 py-5 flex justify-between items-center">
            <a href="../index.php" class="text-xl font-black tracking-tighter text-white italic">
                BIBLIO<span class="text-blue-500">TECH.</span>
            </a>
            <a href="register.php" class="text-[10px] font-black uppercase tracking-widest bg-white text-black py-3 px-8 rounded-full hover:bg-slate-200 transition-all active:scale-95">
                Daftar Akun
            </a>
        </div>
    </nav>

    <main class="relative z-10 flex-grow flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">
            <div class="card-glass p-10 rounded-[2.5rem] shadow-2xl">
                
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-black text-white tracking-tighter mb-2 uppercase italic">Selamat <span class="text-blue-500">Datang.</span></h2>
                    <p class="text-slate-500 font-medium text-sm">Masuk dengan Username atau Email Anda.</p>
                </div>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-500 text-[10px] font-black text-center uppercase tracking-widest animate-pulse">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="proses_login.php" method="POST" class="space-y-6">
                    
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] font-black text-slate-500 mb-3 ml-1">Username / Email</label>
                        <input type="text" name="identifier" 
                            class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition duration-300 text-sm"
                            placeholder="username atau email" required autofocus>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] font-black text-slate-500 mb-3 ml-1">Password</label>
                        <input type="password" name="password" 
                            class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition duration-300 text-sm"
                            placeholder="••••••••" required>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="login" 
                            class="w-full py-4 px-6 bg-blue-600 hover:bg-blue-500 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all hover:-translate-y-1 active:scale-95 shadow-xl shadow-blue-600/30">
                            Masuk Sekarang
                        </button>
                    </div>

                    <div class="relative flex py-4 items-center">
                        <div class="flex-grow border-t border-white/5"></div>
                        <span class="flex-shrink mx-4 text-slate-600 text-[10px] font-black uppercase tracking-widest">Atau</span>
                        <div class="flex-grow border-t border-white/5"></div>
                    </div>

                    <div class="text-center">
                        <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest mb-4">Belum punya akun?</p>
                        <a href="../register.php" 
                            class="w-full inline-block py-4 px-6 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl border border-white/10 transition duration-300">
                            Buat Akun Baru
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </main>

</body>
</html>