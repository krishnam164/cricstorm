<?php
include 'config.php';

// Check Maintenance Mode from Settings Master
$set_res = mysqli_query($conn, "SELECT maintenance_mode FROM settings_master WHERE id = 1");
$settings = mysqli_fetch_assoc($set_res);

if (($settings['maintenance_mode'] ?? 'Off') == 'On') {
    include 'maintenance_view.php'; // Show a "Coming Soon" screen if On
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CricStrome | Premier Auction Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #FDFCF8; }
        .hero-gradient { background: radial-gradient(circle at top right, #E2F2F0, transparent); }
    </style>
</head>
<body class="hero-gradient min-h-screen">

    <nav class="px-6 md:px-20 py-8 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-bolt"></i>
            </div>
            <h1 class="text-xl font-black text-slate-900 italic">CricStrome</h1>
        </div>
        <div class="flex items-center gap-8">
            <a href="live_auctions.php" class="text-xs font-bold text-slate-500 uppercase tracking-widest hover:text-teal-500 transition-all">Live Monitor</a>
            <a href="login.php" class="bg-slate-900 text-white px-8 py-3 rounded-2xl text-xs font-bold hover:bg-teal-500 transition-all shadow-xl">Admin Login</a>
        </div>
    </nav>

    <section class="px-6 md:px-20 pt-20 pb-32 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
        <div>
            <span class="px-4 py-1.5 bg-teal-50 text-teal-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-teal-100 mb-6 inline-block">
                Platform Active
            </span>
            <h2 class="text-5xl md:text-7xl font-black text-slate-900 leading-[1.1] mb-8">
                The Next Gen <br><span class="text-teal-500">Cricket Auction</span>
            </h2>
            <p class="text-slate-500 font-medium text-lg leading-relaxed mb-10 max-w-md">
                Managing over 21,000 player records with real-time bidding precision.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="live_auctions.php" class="bg-teal-500 text-white px-10 py-5 rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-2xl shadow-teal-200 hover:scale-105 transition-all">
                    Watch Live Bids
                </a>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-10 bg-teal-200/20 blur-[100px] rounded-full"></div>
            <div class="relative bg-white p-12 rounded-[4rem] border border-teal-50 shadow-2xl">
                <div class="grid grid-cols-2 gap-10">
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Teams</p>
                        <p class="text-4xl font-black text-slate-900">66</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Players</p>
                        <p class="text-4xl font-black text-teal-500">21k+</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
</html>