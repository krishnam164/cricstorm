<?php
include '../config.php';

// 1. ENHANCED SECURITY GATE
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. DATA AGGREGATION
$total_players = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM player_master"))['count'];
$total_teams   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM team_master"))['count'];
$live_bids     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM auction_tracking"))['count']; 
$active_tournaments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM tournament_master"))['count'];

$active_page = 'dashboard';
include 'includes/header.php'; 
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 mb-8 md:mb-10 px-2 md:px-0">
    
    <div class="bg-white p-6 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-teal-50 shadow-sm transition-all hover:shadow-md md:hover:-translate-y-2 animate__animated animate__fadeInUp">
        <div class="flex justify-between items-start mb-4">
            <span class="w-10 h-10 md:w-12 md:h-12 bg-blue-50 text-blue-500 rounded-xl md:rounded-2xl flex items-center justify-center">
                <i class="fas fa-id-badge text-lg md:text-xl"></i>
            </span>
            <span class="text-[8px] md:text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-full uppercase">Global</span>
        </div>
        <h4 class="text-slate-400 text-[9px] md:text-[10px] font-bold uppercase tracking-widest">Registered Players</h4>
        <span class="text-3xl md:text-4xl font-black text-slate-900 mt-1 md:mt-2 block"><?php echo number_format($total_players); ?></span>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-teal-50 shadow-sm transition-all hover:shadow-md md:hover:-translate-y-2 animate__animated animate__fadeInUp" style="animation-delay: 100ms;">
        <div class="flex justify-between items-start mb-4">
            <span class="w-10 h-10 md:w-12 md:h-12 bg-purple-50 text-purple-500 rounded-xl md:rounded-2xl flex items-center justify-center">
                <i class="fas fa-shield-alt text-lg md:text-xl"></i>
            </span>
            <span class="text-[8px] md:text-[10px] font-black text-purple-500 bg-purple-50 px-3 py-1 rounded-full uppercase">Clubs</span>
        </div>
        <h4 class="text-slate-400 text-[9px] md:text-[10px] font-bold uppercase tracking-widest">Active Franchises</h4>
        <span class="text-3xl md:text-4xl font-black text-slate-900 mt-1 md:mt-2 block"><?php echo number_format($total_teams); ?></span>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-teal-50 shadow-sm border-l-4 border-l-orange-500 transition-all hover:shadow-md md:hover:-translate-y-2 animate__animated animate__fadeInUp" style="animation-delay: 200ms;">
        <div class="flex justify-between items-start mb-4">
            <span class="w-10 h-10 md:w-12 md:h-12 bg-orange-50 text-orange-500 rounded-xl md:rounded-2xl flex items-center justify-center">
                <i class="fas fa-hammer text-lg md:text-xl"></i>
            </span>
            <span class="text-[8px] md:text-[10px] font-black text-orange-600 bg-orange-50 px-3 py-1 rounded-full animate-pulse uppercase">Live</span>
        </div>
        <h4 class="text-slate-400 text-[9px] md:text-[10px] font-bold uppercase tracking-widest">Auction Pulse</h4>
        <span class="text-3xl md:text-4xl font-black text-orange-600 mt-1 md:mt-2 block"><?php echo number_format($live_bids); ?></span>
    </div>

    <div class="bg-white p-6 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-teal-50 shadow-sm transition-all hover:shadow-md md:hover:-translate-y-2 animate__animated animate__fadeInUp" style="animation-delay: 300ms;">
        <div class="flex justify-between items-start mb-4">
            <span class="w-10 h-10 md:w-12 md:h-12 bg-teal-50 text-teal-500 rounded-xl md:rounded-2xl flex items-center justify-center">
                <i class="fas fa-server text-lg md:text-xl animate-pulse"></i>
            </span>
        </div>
        <h4 class="text-slate-400 text-[9px] md:text-[10px] font-bold uppercase tracking-widest">System Engine</h4>
        <span class="text-base md:text-lg font-bold text-teal-600 mt-1 md:mt-2 block flex items-center gap-2 italic uppercase">
            <i class="fas fa-check-circle text-xs"></i> Online
        </span>
    </div>
</div>

<div class="bg-white rounded-[1.5rem] md:rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden mb-10 animate__animated animate__fadeIn animate__delay-1s mx-2 md:mx-0">
    <div class="p-6 md:p-8 border-b border-teal-50 flex flex-col sm:flex-row justify-between items-center bg-slate-50/50 gap-4">
        <div class="text-center sm:text-left">
            <h3 class="text-base md:text-lg font-black text-slate-900 uppercase italic tracking-tighter">Recent Auction Streams</h3>
            <p class="text-[8px] md:text-[10px] text-slate-400 font-bold uppercase tracking-widest">Real-time monitoring</p>
        </div>
        <a href="all_tournaments.php" class="w-full sm:w-auto text-center bg-slate-900 text-white px-6 py-3 rounded-xl md:rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all shadow-md">
            View All
        </a>
    </div>
    
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-left min-w-[600px]">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 md:px-8 py-4 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">ID Token</th>
                    <th class="px-6 md:px-8 py-4 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">League Name</th>
                    <th class="px-6 md:px-8 py-4 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Draft Progress</th>
                    <th class="px-6 md:px-8 py-4 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Control</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-50">
                <?php
                $sql = "SELECT am.*, tm.tournament_name 
                        FROM auction_master am
                        LEFT JOIN tournament_master tm ON am.tournament_id = tm.tournament_id 
                        ORDER BY am.auction_id DESC LIMIT 5";
                
                $auction_res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($auction_res) > 0) {
                    while($a = mysqli_fetch_assoc($auction_res)) {
                        $current_auc_id = $a['auction_id'];
                        $p_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM auction_player_master WHERE tournament_id = '$current_auc_id'");
                        $p_data = mysqli_fetch_assoc($p_res);
                ?>
                <tr class="hover:bg-teal-50/40 transition-all duration-300 group">
                    <td class="px-6 md:px-8 py-5 md:py-6 font-bold text-slate-400 text-[10px] md:text-xs group-hover:text-teal-500 transition-colors">#AUC-<?php echo $current_auc_id; ?></td>
                    <td class="px-6 md:px-8 py-5 md:py-6">
                        <div class="text-xs md:text-sm font-black text-slate-800 uppercase italic"><?php echo $a['tournament_name'] ?? 'Unnamed Tournament'; ?></div>
                    </td>
                    <td class="px-6 md:px-8 py-5 md:py-6">
                        <span class="px-3 md:px-4 py-1 bg-slate-100 text-slate-600 rounded-full text-[8px] md:text-[9px] font-black uppercase tracking-widest border border-slate-200">
                            <?php echo $p_data['count']; ?> Sold
                        </span>
                    </td>
                    <td class="px-6 md:px-8 py-5 md:py-6 text-center">
                        <a href="auction_controller.php?id=<?php echo $current_auc_id; ?>" class="inline-flex w-8 h-8 bg-teal-50 text-teal-600 rounded-lg items-center justify-center hover:bg-teal-600 hover:text-white transition-all">
                            <i class="fas fa-external-link-alt text-[9px]"></i>
                        </a>
                    </td>
                </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='4' class='p-12 text-center text-[10px] font-bold text-slate-300 uppercase tracking-[0.3em]'>No recent auctions</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>