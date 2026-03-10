<?php
// We only include config to get the database connection
include 'config.php';

/** * SECURITY NOTE: 
 * The Login Gate has been removed to allow public access.
 * This page will now show live data to any visitor.
 */

// 1. FETCH LIVE STATISTICS
// Pulling the latest 20 bids from your 18,323+ rows in auction_tracking
$live_bids_query = mysqli_query($conn, "SELECT * FROM auction_tracking ORDER BY id DESC LIMIT 20");

// Counting how many auctions are currently active in auction_master
$active_auctions = mysqli_num_rows(mysqli_query($conn, "SELECT auction_id FROM auction_master WHERE auction_status = 'Live'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Auction Monitor | CricStrome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="w-3 h-3 bg-red-500 rounded-full animate-ping"></span>
                <h1 class="text-3xl font-black italic uppercase tracking-tighter">Live <span class="text-teal-400">Monitor</span></h1>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.3em]">CricStrome Real-time Auction Feed</p>
        </div>
        
        <div class="flex gap-4">
            <div class="glass-card px-8 py-4 rounded-[2rem] text-center">
                <p class="text-[9px] font-bold text-slate-500 uppercase mb-1">Active Arenas</p>
                <p class="text-2xl font-black text-teal-400"><?php echo $active_auctions; ?></p>
            </div>
        </div>
    </div>

    

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <div class="lg:col-span-2 glass-card rounded-[3rem] overflow-hidden">
            <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/5">
                <h3 class="font-bold text-lg italic">Recent Bids</h3>
                <span class="text-[10px] font-black text-teal-500 bg-teal-500/10 px-3 py-1 rounded-full">AUTO-SYNC ON</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                            <th class="px-8 py-6">ID</th>
                            <th class="px-8 py-6">Tournament</th>
                            <th class="px-8 py-6">Amount</th>
                            <th class="px-8 py-6 text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php if($live_bids_query && mysqli_num_rows($live_bids_query) > 0): ?>
                            <?php while($bid = mysqli_fetch_assoc($live_bids_query)): ?>
                            <tr class="hover:bg-white/5 transition-all">
                                <td class="px-8 py-6 text-xs font-bold text-slate-600">#<?php echo $bid['id']; ?></td>
                                <td class="px-8 py-6 text-sm font-black">Competition #<?php echo $bid['tournament_id']; ?></td>
                                <td class="px-8 py-6">
                                    <span class="text-xl font-black text-teal-400">₹<?php echo number_format($bid['bid_amount']); ?></span>
                                </td>
                                <td class="px-8 py-6 text-right text-[10px] font-bold text-slate-500">
                                    <?php echo date('h:i:s A', strtotime($bid['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="p-20 text-center text-slate-500 font-bold uppercase tracking-widest italic">Waiting for bids...</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-8">
            <div class="glass-card p-10 rounded-[3rem] border-l-4 border-teal-500">
                <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-6">Network Info</h4>
                <div class="space-y-4">
                    <p class="text-xs text-slate-400 leading-relaxed">This is a public read-only view of the <strong>CricStrome Auction Engine</strong>.</p>
                    <p class="text-xs text-slate-400 leading-relaxed">Refresh rate is set to 5 seconds to provide near-instant updates.</p>
                </div>
            </div>

            <div class="bg-indigo-600 p-10 rounded-[3rem] text-white shadow-xl shadow-indigo-500/20">
                <i class="fas fa-info-circle text-3xl mb-4"></i>
                <h4 class="text-xl font-black leading-tight mb-2">Want to Bid?</h4>
                <p class="text-xs font-bold opacity-70 leading-relaxed mb-6">Contact your tournament organizer to register your team and get bidding credentials.</p>
                <a href="login.php" class="inline-block bg-white text-indigo-600 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-wider hover:bg-teal-400 hover:text-white transition-all">Staff Login</a>
            </div>
        </div>
    </div>

</body>
</html>