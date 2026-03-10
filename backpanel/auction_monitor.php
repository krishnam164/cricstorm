<?php

include '../config.php';

// 1. MASTER SECURITY GATE
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}
// 2. FETCH LIVE DATA
// We use 'id' because your DB structure shows 'id' is the primary key for this table
$live_tracking = mysqli_query($conn, "SELECT * FROM auction_tracking ORDER BY id DESC LIMIT 10");

// Fixed column name from auction_tracking_id to id
$total_bids_today_query = mysqli_query($conn, "SELECT id FROM auction_tracking WHERE DATE(created_at) = CURDATE()");

if ($total_bids_today_query) {
    $total_bids_today = mysqli_num_rows($total_bids_today_query);
} else {
    $total_bids_today = 0; // Fallback if query fails
}

$active_page = 'auction_monitor';
include 'includes/header.php'; 
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-2xl font-black text-slate-900 italic">Live Auction <span class="text-red-600">Monitor</span></h2>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Real-time bidding surveillance</p>
    </div>
    <div class="flex gap-4">
        <div class="bg-white px-6 py-3 rounded-2xl border border-teal-50 shadow-sm">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Bids Today</p>
            <p class="text-xl font-black text-slate-900"><?php echo $total_bids_today; ?></p>
        </div>
        <button onclick="location.reload()" class="bg-slate-900 text-white p-4 rounded-2xl hover:bg-teal-500 transition-all shadow-lg">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>



<div class="bg-white rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden">
    <div class="p-8 border-b border-teal-50 bg-slate-50/50 flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-900">Live Bid Stream</h3>
        <span class="flex items-center gap-2 text-[10px] font-black text-teal-500 uppercase">
            <span class="w-2 h-2 bg-teal-500 rounded-full animate-ping"></span> Live Connection
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Log ID</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Auction Details</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bid Amount</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-50">
                <?php
                if($live_tracking && mysqli_num_rows($live_tracking) > 0) {
                    while($row = mysqli_fetch_assoc($live_tracking)) {
                ?>
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <td class="px-8 py-6 font-bold text-slate-400 text-xs">#TRK-<?php echo $row['id']; ?></td>
                    <td class="px-8 py-6">
                        <div class="text-sm font-bold text-slate-800">Tournament #<?php echo $row['tournament_id']; ?></div>
                        <div class="text-[10px] text-slate-400">Auction Reference: <?php echo $row['auction_id']; ?></div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-lg font-black text-slate-900">₹ <?php echo number_format($row['bid_amount'] ?? 0); ?></div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-4 py-1.5 bg-teal-50 text-teal-600 rounded-full text-[9px] font-black uppercase">
                            Recorded
                        </span>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='p-20 text-center text-slate-400 font-bold'>No live auction activity detected.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>