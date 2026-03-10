<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CricStrom | Professional Cricket Auction</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FDFCF8',  
                        brand: '#14B8A6',    
                        brandDark: '#0F766E', 
                        accent: '#db2f2fd3', 
                        slate900: '#0F172A', 
                        surface: '#FFFFFF'   
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .brand-glow { box-shadow: 0 10px 40px -10px rgba(20, 184, 166, 0.2); }
        /* Smooth transition for dropdown */
        .dropdown-menu { opacity: 0; transform: translateY(10px); pointer-events: none; transition: all 0.2s ease; }
        .group:hover .dropdown-menu { opacity: 1; transform: translateY(0); pointer-events: auto; }
    </style>
</head>
<body class="bg-primary text-slate-800">

    <nav class="sticky top-0 z-50 bg-surface/90 backdrop-blur-lg border-b border-teal-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-3">
                <img src="images/favicon.png" alt="CricStrom" class="h-12 rounded w-auto">
                <div class="flex flex-col">
                    <span class="text-2xl font-extrabold tracking-tighter leading-none text-slate900">
                        CRIC<span class="text-brand">STROM</span>
                    </span>
                    <span class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400">Auction Portal</span>
                </div>
            </a>

            <div class="hidden lg:flex items-center space-x-7 text-[13px] font-bold uppercase tracking-wider">
                <a href="index.php" class="text-slate900 hover:text-brand transition">Home</a>
                <a href="tournaments.php" class="text-slate900 hover:text-brand transition">Tournaments</a>
                
                <div class="relative group cursor-pointer">
                    <button class="flex items-center text-slate900 group-hover:text-brand transition gap-1 uppercase">
                        Auctions <i class="fas fa-chevron-down text-[10px] mt-0.5 transition-transform group-hover:rotate-180"></i>
                    </button>
                    <div class="dropdown-menu absolute left-0 mt-4 w-56 bg-white border border-teal-50 rounded-2xl shadow-xl py-3 z-50">
                        <a href="live_auctions.php" class="flex items-center px-5 py-3 text-slate-700 hover:bg-teal-50 hover:text-brand transition gap-3">
                            <i class="fas fa-tower-broadcast text-xs"></i>
                            <span>Live & Today's Auction</span>
                        </a>
                        <div class="border-t border-slate-50 mx-4 my-1"></div>
                        <a href="all_auctions.php" class="flex items-center px-5 py-3 text-slate-700 hover:bg-teal-50 hover:text-brand transition gap-3">
                            <i class="fas fa-list-ul text-xs"></i>
                            <span>All Auctions</span>
                        </a>
                    </div>
                </div>

                <a href="pricing.php" class="text-slate900 hover:text-brand transition">Pricing</a>
                <a href="about_us.php" class="text-slate900 hover:text-brand transition">About Us</a>
                <a href="contact_us.php" class="text-slate900 hover:text-brand transition">Contact Us</a>
                
                <a href="login.php" class="bg-brand text-white px-7 py-2.5 rounded-xl hover:bg-brandDark transition shadow-lg shadow-teal-200/50 flex items-center gap-2">
                    <i class="fas fa-user-lock text-xs"></i>
                    <span>Log in</span>
                </a>
            </div>

            <button class="lg:hidden text-slate-900 p-2">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>
    </nav>