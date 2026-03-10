<?php require 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>Register Franchise | CricStrome</title>

    <style type="text/css">
        .font-oswald { font-family: 'Oswald', sans-serif; }
        .font-poppins { font-family: 'Poppins', sans-serif; }
        
        body {
            background-image: linear-gradient(to bottom, rgba(15, 23, 42, 0.92), rgba(15, 23, 42, 0.98)), 
                              url('https://images.unsplash.com/photo-1540747913346-19e32dc3e97e?q=80&w=2000');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .social-btn:hover {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .icon-input { padding-left: 3rem !important; }
    </style>
</head>
<body class="font-poppins flex min-h-screen items-center justify-center p-4 antialiased">

    <div class="w-full max-w-md">
        <div class="mb-8 text-center animate-in fade-in slide-in-from-top-4 duration-700">
            <h2 class="font-oswald text-5xl font-bold tracking-tighter text-white uppercase sm:text-6xl italic">
                JOIN THE <span class="text-yellow-400">LEAGUE</span>
            </h2>
            <div class="mt-3 flex justify-center">
                <span class="h-1 w-12 bg-blue-600 rounded-full"></span>
            </div>
            <p class="mt-4 text-xs font-bold tracking-[0.3em] text-blue-400 uppercase">New Franchise Registration</p>
        </div>

        <div class="glass-card overflow-hidden rounded-[2.5rem] shadow-2xl transition-all duration-500 hover:border-blue-500/30">
            <div class="p-8 sm:p-10">
                
                <form action="process_registration.php" method="POST" class="space-y-4">

                    <div>
                        <label class="mb-2 ml-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">Username / Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-envelope text-slate-500 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input type="email" name="email" 
                                class="icon-input w-full rounded-2xl border border-slate-700 bg-slate-900/50 p-4 text-white placeholder-slate-600 transition-all focus:border-blue-500 focus:bg-slate-950 focus:ring-4 focus:ring-blue-500/10 focus:outline-none" 
                                placeholder="owner@franchise.com" required>
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 ml-1 block text-[10px] font-bold tracking-widest text-slate-400 uppercase">Password Key</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="fa-solid fa-lock text-slate-500 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input type="password" name="password" 
                                class="icon-input w-full rounded-2xl border border-slate-700 bg-slate-900/50 p-4 text-white placeholder-slate-600 transition-all focus:border-blue-500 focus:bg-slate-950 focus:ring-4 focus:ring-blue-500/10 focus:outline-none" 
                                placeholder="Create Password" required>
                        </div>
                    </div>

                    <button type="submit" 
                        class="group relative flex w-full items-center justify-center overflow-hidden rounded-2xl bg-yellow-500 py-4 text-sm font-black tracking-widest text-slate-900 shadow-[0_10px_20px_rgba(234,179,8,0.2)] transition-all duration-300 hover:bg-yellow-400 active:scale-95 uppercase mt-2">
                        <span class="relative z-10 flex items-center gap-2">
                            <i class="fa-solid fa-trophy"></i>
                            Register Franchise
                        </span>
                    </button>
                </form>

                <div class="relative flex items-center py-6">
                    <div class="flex-grow border-t border-slate-800"></div>
                    <span class="mx-4 flex-shrink text-[10px] font-bold text-slate-500 uppercase tracking-widest">Fast Track with</span>
                    <div class="flex-grow border-t border-slate-800"></div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <a href="google_auth_redirect.php" class="social-btn flex items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/40 py-3.5 transition-all hover:bg-slate-900">
                        <i class="fa-brands fa-google text-red-500"></i>
                        <span class="text-[10px] font-bold text-slate-200 uppercase tracking-tighter">Google</span>
                    </a>

                    <a href="email_otp_request.php" class="social-btn flex items-center justify-center gap-2 rounded-2xl border border-slate-700 bg-slate-900/40 py-3.5 transition-all hover:bg-slate-900">
                        <i class="fa-solid fa-bolt text-blue-400"></i>
                        <span class="text-[10px] font-bold text-slate-200 uppercase tracking-tighter">OTP Link</span>
                    </a>
                </div>

                <div class="mt-8 text-center border-t border-white/5 pt-6">
                    <p class="text-[11px] font-medium tracking-wide text-slate-400">
                        Already have a team? 
                        <a href="login.php" class="font-bold text-white hover:text-blue-400 transition-colors uppercase ml-1 underline underline-offset-4 decoration-yellow-500/50">Sign In</a>
                    </p>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-yellow-500 via-yellow-300 to-blue-600"></div>
        </div>

        <div class="mt-8 text-center animate-in fade-in slide-in-from-bottom-2 duration-1000">
            <a href="index.php" class="inline-flex items-center text-xs font-bold tracking-widest text-slate-500 transition-colors hover:text-blue-400 uppercase">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Arena
            </a>
        </div>
    </div>

</body>
</html>