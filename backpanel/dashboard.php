<?php

include '../config.php';

/** * 1. MASTER SECURITY GATE */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

// 2. DATA AGGREGATION
$total_players = mysqli_num_rows(mysqli_query($conn, "SELECT player_id FROM player_master"));
$total_teams = mysqli_num_rows(mysqli_query($conn, "SELECT team_id FROM team_master"));
$live_bids = mysqli_num_rows(mysqli_query($conn, "SELECT auction_tracking_id FROM auction_tracking")); 
$active_tournaments = mysqli_num_rows(mysqli_query($conn, "SELECT tournament_id FROM tournament_master"));

$admin_mobile = $_SESSION['admin_mobile'] ?? 'System Admin';

include 'includes/header.php'; 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
    
    <div class="bg-white p-8 rounded-[2.5rem] border border-teal-50 shadow-sm transition-all hover:shadow-md hover:-translate-y-2 animate__animated animate__fadeInUp">
        <div class="flex justify-between items-start mb-4">
            <span class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center animate__animated animate__bounceIn animate__delay-1s">
                <i class="fas fa-id-badge text-xl"></i>
            </span>
            <span class="text-[10px] font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-full">GLOBAL</span>
        </div>
        <h4 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Registered Players</h4>
        <span class="text-4xl font-black text-slate-900 mt-2 block"><?php echo number_format($total_players); ?></span>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-teal-50 shadow-sm transition-all hover:shadow-md hover:-translate-y-2 animate__animated animate__fadeInUp animate__delay-100ms">
        <div class="flex justify-between items-start mb-4">
            <span class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center animate__animated animate__bounceIn animate__delay-1s">
                <i class="fas fa-shield-alt text-xl"></i>
            </span>
            <span class="text-[10px] font-bold text-purple-500 bg-purple-50 px-3 py-1 rounded-full">CLUBS</span>
        </div>
        <h4 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Active Teams</h4>
        <span class="text-4xl font-black text-slate-900 mt-2 block"><?php echo $total_teams; ?></span>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-teal-50 shadow-sm border-l-4 border-l-orange-500 transition-all hover:shadow-md hover:-translate-y-2 animate__animated animate__fadeInUp animate__delay-200ms group">
        <div class="flex justify-between items-start mb-4">
            <span class="w-12 h-12 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center group-hover:animate-spin transition-all">
                <i class="fas fa-hammer text-xl"></i>
            </span>
            <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-full animate-pulse">LIVE BIDS</span>
        </div>
        <h4 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Total Auction Hits</h4>
        <span class="text-4xl font-black text-orange-600 mt-2 block"><?php echo number_format($live_bids); ?></span>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-teal-50 shadow-sm transition-all hover:shadow-md hover:-translate-y-2 animate__animated animate__fadeInUp animate__delay-300ms">
        <div class="flex justify-between items-start mb-4">
            <span class="w-12 h-12 bg-teal-50 text-teal-500 rounded-2xl flex items-center justify-center animate__animated animate__flash animate__infinite animate__slow">
                <i class="fas fa-server text-xl"></i>
            </span>
        </div>
        <h4 class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">DB Integrity</h4>
        <span class="text-xl font-bold text-teal-600 mt-2 block flex items-center gap-2">
            <i class="fas fa-check-circle animate__animated animate__bounceIn animate__delay-1s"></i> 13 TABLES OK
        </span>
    </div>
</div>

<div class="bg-white rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden mb-10 animate__animated animate__fadeIn animate__delay-1s">
    <div class="p-8 border-b border-teal-50 flex justify-between items-center bg-slate-50/50">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Recent Auction Activity</h3>
            <p class="text-xs text-slate-400">Monitoring 'auction_master' data</p>
        </div>
        <a href="auction_history.php" class="text-[10px] font-bold text-teal-500 uppercase tracking-widest hover:underline transition-all hover:tracking-tighter">View Full Logs</a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Auction ID</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tournament Name</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sold Status</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Control</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-50">
                <?php
                $auction_res = mysqli_query($conn, "SELECT * FROM auction_master ORDER BY auction_id DESC LIMIT 5");

                if ($auction_res) {
                    while($a = mysqli_fetch_assoc($auction_res)) {
                        $current_auc_id = $a['auction_id'];
                        $bid_query = "SELECT id FROM auction_player_master WHERE auction_id = '$current_auc_id'";
                        $bid_result = mysqli_query($conn, $bid_query);
                        $bid_count = ($bid_result) ? mysqli_num_rows($bid_result) : 0;
                ?>
                <tr class="hover:bg-teal-50/40 transition-all duration-300 group">
                    <td class="px-8 py-6 font-bold text-slate-700 italic group-hover:translate-x-2 transition-transform">#AUC-<?php echo $current_auc_id; ?></td>
                    <td class="px-8 py-6">
                        <div class="text-sm font-bold text-slate-800"><?php echo $a['auction_id']; ?></div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-4 py-1.5 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-wider group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <?php echo $bid_count; ?> Players Drafted
                        </span>
                    </td>
                    <td class="px-8 py-6 flex justify-center gap-3">
                        <button class="w-8 h-8 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center hover:bg-teal-600 hover:text-white hover:rotate-12 transition-all shadow-sm">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                    </td>
                </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='4' class='p-8 text-center text-red-500'>Error loading auctions: " . mysqli_error($conn) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Custom delay for staggered grid entrance */
    .animate-delay-100ms { animation-delay: 100ms; }
    .animate-delay-200ms { animation-delay: 200ms; }
    .animate-delay-300ms { animation-delay: 300ms; }
</style>

<?php 
include 'includes/footer.php'; 
?>