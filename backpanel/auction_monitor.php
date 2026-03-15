<?php
include '../config.php';

/** * 1. MASTER SECURITY GATE
 * Using the unified 'users' table logic we established.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrator') {
    header("Location: ../login.php"); 
    exit();
}

// 2. FETCH LIVE DATA
// Limits to last 15 bids for performance on your 21k+ user DB
$live_tracking = mysqli_query($conn, "SELECT * FROM auction_tracking ORDER BY id DESC LIMIT 15");

// Stats: Total bids recorded today
$total_bids_today_query = mysqli_query($conn, "SELECT id FROM auction_tracking WHERE DATE(created_at) = CURDATE()");
$total_bids_today = ($total_bids_today_query) ? mysqli_num_rows($total_bids_today_query) : 0;

$active_page = 'auction_monitor';
include 'includes/header.php'; 
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase">Live <span class="text-red-600">Surveillance</span></h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Real-time Global Bidding Stream</p>
    </div>
    <div class="flex gap-4">
        <div class="bg-white px-8 py-4 rounded-[2rem] border border-teal-50 shadow-sm flex flex-col justify-center">
            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Bids Today</p>
            <p class="text-2xl font-black text-slate-900 leading-none mt-1"><?php echo number_format($total_bids_today); ?></p>
        </div>
        <button onclick="location.reload()" class="bg-slate-900 text-white w-14 h-14 rounded-2xl hover:bg-red-600 transition-all shadow-lg flex items-center justify-center group">
            <i class="fas fa-sync-alt group-hover:rotate-180 transition-all duration-500"></i>
        </button>
    </div>
</div>



<div class="bg-white rounded-[3.5rem] border border-teal-50 shadow-sm overflow-hidden mb-12">
    <div class="p-10 border-b border-teal-50 bg-slate-50/30 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-3 h-3 bg-red-500 rounded-full animate-ping"></div>
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest italic">Live Bid Stream</h3>
        </div>
        <span class="text-[10px] font-black text-teal-500 uppercase tracking-widest bg-teal-50 px-4 py-2 rounded-full">
            Active Connection: Secured
        </span>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase">Log Tracking</th>
                    <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase">Tournament & Auction</th>
                    <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase">Amount</th>
                    <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase text-center">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-50" id="bid-stream-body">
                <?php
                if($live_tracking && mysqli_num_rows($live_tracking) > 0) {
                    while($row = mysqli_fetch_assoc($live_tracking)) {
                ?>
                <tr class="hover:bg-red-50/20 transition-colors group">
                    <td class="px-10 py-8">
                        <span class="text-[10px] font-black text-slate-300 group-hover:text-red-500 transition-colors">#TRK-<?php echo $row['id']; ?></span>
                    </td>
                    <td class="px-10 py-8">
                        <div class="text-sm font-black text-slate-800 uppercase italic">Tournament #<?php echo $row['tournament_id']; ?></div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1">Ref ID: <?php echo $row['auction_id']; ?></div>
                    </td>
                    <td class="px-10 py-8">
                        <div class="text-xl font-black text-slate-900">₹ <?php echo number_format($row['bid_amount'] ?? 0); ?></div>
                    </td>
                    <td class="px-10 py-8 text-center">
                        <div class="text-[10px] font-black text-slate-400 uppercase bg-slate-100 inline-block px-4 py-2 rounded-xl">
                            <?php echo date('H:i:s', strtotime($row['created_at'] ?? 'now')); ?>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='p-24 text-center text-slate-300 font-black uppercase tracking-[0.3em] text-xs'>Zero live activity in stream</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    /**
     * Automatic Refresh Logic
     * Refreshes the page every 10 seconds to keep the admin updated on new bids.
     */
    setTimeout(function() {
        location.reload();
    }, 10000); // 10000ms = 10 seconds
</script>

<?php include 'includes/footer.php'; ?>