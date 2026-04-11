<?php
// config.php handles session_start() and DB connection
include '../config.php';

/** 1. SESSION SECURITY GATE */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

/** 2. ADMIN PERMISSION GATE */
$check_access = mysqli_query($conn, "SELECT manager_request FROM users WHERE user_id = '$user_id'");
$access_data = mysqli_fetch_assoc($check_access);

if (($access_data['manager_request'] ?? 'None') !== 'Accepted') {
    header("Location: request_access.php"); 
    exit();
}

/** 3. FETCH AUCTION DETAILS */
$auction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$auc_sql = "SELECT * FROM auction_master WHERE auction_id = '$auction_id' AND user_id = '$user_id'";
$auc_res = mysqli_query($conn, $auc_sql);
$auc = mysqli_fetch_assoc($auc_res);

if (!$auc) {
    die("<div class='p-10 text-center font-black text-rose-500 uppercase tracking-widest'>Access Denied: Auction Not Found</div>");
}

// 4. FETCH CURRENT PLAYER
$player_sql = "SELECT p.*, ap.base_price FROM user_master p 
               JOIN auction_player_master ap ON p.user_id = ap.player_id 
               WHERE ap.auction_id = '$auction_id' AND ap.status = 'Unsold' 
               LIMIT 1";
$player_res = mysqli_query($conn, $player_sql);
$cp = mysqli_fetch_assoc($player_res);

$active_page = 'all_tournaments';
include '../backpanel/includes/header.php'; 
?>

<div class="mb-8 md:mb-10 flex flex-col sm:flex-row justify-between items-center gap-6 px-2 md:px-0">
    <div class="text-center sm:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic tracking-tighter uppercase leading-none">
            Live <span class="text-orange-500">Auction Monitor</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 mt-2 uppercase tracking-[0.2em] font-bold"><?php echo $auc['auction_name']; ?></p>
    </div>
    
    <div class="flex justify-center w-full sm:w-auto">
        <div class="bg-slate-900 px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-[2rem] text-center border border-white/5 shadow-2xl shrink-0">
            <p class="text-[8px] md:text-[9px] font-bold text-slate-500 uppercase mb-1 tracking-widest leading-none">Time Remaining</p>
            <p class="text-xl md:text-2xl font-black text-orange-400 leading-none" id="timer">00:30</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-10 px-2 md:px-0 pb-12">
    <div class="lg:col-span-2 space-y-6 md:space-y-8">
        <?php if ($cp): ?>
        <div class="bg-white rounded-[2rem] md:rounded-[4rem] border border-orange-50 shadow-sm p-6 md:p-12 relative overflow-hidden group">
            <div class="hidden sm:block absolute top-0 right-0 w-64 h-64 bg-orange-50 rounded-full -mr-32 -mt-32 opacity-50 transition-all group-hover:scale-110"></div>
            
            <div class="relative flex flex-col md:flex-row gap-6 md:gap-10 items-center">
                <div class="w-40 h-40 md:w-48 md:h-48 bg-slate-50 rounded-[2rem] md:rounded-[3rem] border-4 border-white shadow-xl overflow-hidden shrink-0">
                    <img src="../uploads/players/<?php echo $cp['user_image'] ?? 'default.png'; ?>" class="w-full h-full object-cover" onerror="this.src='../uploads/players/default.png'">
                </div>
                
                <div class="flex-grow text-center md:text-left min-w-0">
                    <span class="px-3 md:px-4 py-1.5 bg-orange-50 text-orange-600 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-widest mb-4 inline-block">Active Player</span>
                    <h3 class="text-2xl md:text-4xl font-black text-slate-900 mb-2 leading-tight truncate"><?php echo $cp['user_fullname']; ?></h3>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px] md:text-xs mb-6 md:mb-8">Base Price: ₹<?php echo number_format($cp['base_price']); ?></p>
                    
                    <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                        <button class="bg-slate-900 text-white px-8 md:px-10 py-4 md:py-5 rounded-xl md:rounded-[2rem] font-black uppercase text-[10px] tracking-widest hover:bg-orange-500 transition-all shadow-lg active:scale-95">Sold</button>
                        <button class="bg-rose-50 text-rose-500 px-8 md:px-10 py-4 md:py-5 rounded-xl md:rounded-[2rem] font-black uppercase text-[10px] tracking-widest hover:bg-rose-500 hover:text-white transition-all active:scale-95">Unsold</button>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-[2rem] md:rounded-[4rem] p-12 md:p-20 text-center border-2 border-dashed border-slate-100">
            <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-3xl text-teal-500"></i>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-slate-900 leading-tight">Auction Finished</h3>
            <p class="text-slate-400 font-medium text-sm mt-2">No remaining players in session.</p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-[1.5rem] md:rounded-[3rem] border border-orange-50 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-orange-50 bg-orange-50/10">
                <h4 class="font-black text-slate-800 uppercase text-[10px] md:text-xs tracking-widest italic leading-none">Live Bidding Log</h4>
            </div>
            <div class="p-4 space-y-4 h-56 md:h-64 overflow-y-auto scrollbar-hide" id="bidLog">
                <div class="p-10 text-center text-slate-300 font-bold text-[9px] md:text-[10px] uppercase tracking-widest">
                    <i class="fas fa-satellite-dish block text-xl mb-3 opacity-20"></i>
                    Syncing Live Stream...
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6 md:space-y-8">
        <div class="bg-[#0F172A] p-8 md:p-10 rounded-[2rem] md:rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
            <h4 class="text-[9px] md:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-8 italic">Participating Teams</h4>
            
            <div class="space-y-5 relative">
                <?php 
                $teams_res = mysqli_query($conn, "SELECT t.* FROM team_master t 
                                                  JOIN auction_team_master at ON t.id = at.team_id 
                                                  WHERE at.auction_id = '$auction_id'");
                if($teams_res && mysqli_num_rows($teams_res) > 0):
                    while($team = mysqli_fetch_assoc($teams_res)): 
                ?>
                <div class="flex items-center gap-4 border-b border-white/5 pb-4 last:border-0 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-white/5 p-1 shrink-0 flex items-center justify-center border border-white/10">
                        <img src="../uploads/teams/<?php echo $team['team_logo']; ?>" class="w-full h-full object-contain" onerror="this.src='../uploads/teams/default_team.png'">
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold truncate leading-tight"><?php echo $team['team_name']; ?></p>
                        <p class="text-[9px] font-black text-orange-400 mt-1 uppercase">Purse: ₹<?php echo number_format($team['remaining_budget'] ?? 10000000); ?></p>
                    </div>
                </div>
                <?php endwhile; else: ?>
                    <p class="text-[10px] text-slate-600 font-bold uppercase italic text-center py-4">No teams assigned</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../backpanel/includes/footer.php'; ?>