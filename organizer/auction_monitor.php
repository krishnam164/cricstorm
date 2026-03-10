<?php
// config.php handles session_start() and DB connection
include '../config.php';

/** * 1. SESSION SECURITY GATE
 * Ensures the user is logged in as an Organizer/Manager.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

/** * 2. ADMIN PERMISSION GATE (The New Feature)
 * Verifies if the Super Admin has accepted the 'Global Monitor' request.
 */
$check_access = mysqli_query($conn, "SELECT organizer_request FROM users WHERE user_id = '$user_id'");
$access_data = mysqli_fetch_assoc($check_access);

if (($access_data['organizer_request'] ?? 'None') !== 'Accepted') {
    // If not accepted, redirect to the request page immediately
    header("Location: request_access.php"); 
    exit();
}

/** * 3. FETCH AUCTION DETAILS
 * This only runs if the Permission Gate (Step 2) is passed.
 */
$auction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$auc_sql = "SELECT * FROM auction_master WHERE auction_id = '$auction_id' AND user_id = '$user_id'";
$auc_res = mysqli_query($conn, $auc_sql);
$auc = mysqli_fetch_assoc($auc_res);

if (!$auc) {
    die("<div class='p-20 text-center font-black text-rose-500 uppercase tracking-widest'>Access Denied: Auction Not Found or Ownership Mismatch</div>");
}

// 4. FETCH CURRENT PLAYER IN AUCTION
$player_sql = "SELECT p.*, ap.base_price FROM user_master p 
               JOIN auction_player_master ap ON p.user_id = ap.player_id 
               WHERE ap.auction_id = '$auction_id' AND ap.status = 'Unsold' 
               LIMIT 1";
$player_res = mysqli_query($conn, $player_sql);
$cp = mysqli_fetch_assoc($player_res);

$active_page = 'all_tournaments';
include '../backpanel/includes/header.php'; 
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase">Live <span class="text-orange-500">Auction Monitor</span></h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold"><?php echo $auc['auction_name']; ?></p>
    </div>
    
    <div class="flex gap-4">
        <div class="bg-slate-900 px-8 py-4 rounded-[2rem] text-center border border-white/5 shadow-2xl">
            <p class="text-[9px] font-bold text-slate-500 uppercase mb-1 tracking-widest">Time Remaining</p>
            <p class="text-2xl font-black text-orange-400" id="timer">00:30</p>
        </div>
    </div>
</div>



<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <div class="lg:col-span-2 space-y-8">
        <?php if ($cp): ?>
        <div class="bg-white rounded-[4rem] border border-orange-50 shadow-sm p-12 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-orange-50 rounded-full -mr-32 -mt-32 opacity-50 transition-all group-hover:scale-110"></div>
            
            <div class="relative flex flex-col md:flex-row gap-10 items-center">
                <div class="w-48 h-48 bg-slate-100 rounded-[3rem] border-4 border-white shadow-xl overflow-hidden">
                    <img src="../uploads/players/<?php echo $cp['user_image'] ?? 'default.png'; ?>" class="w-full h-full object-cover">
                </div>
                
                <div class="flex-grow text-center md:text-left">
                    <span class="px-4 py-1.5 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-4 inline-block">Active Player</span>
                    <h3 class="text-4xl font-black text-slate-900 mb-2 leading-none"><?php echo $cp['user_fullname']; ?></h3>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs mb-8">Base Price: ₹<?php echo number_format($cp['base_price']); ?></p>
                    
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                        <button class="bg-slate-900 text-white px-10 py-5 rounded-[2rem] font-black uppercase text-[10px] tracking-widest hover:bg-orange-500 transition-all shadow-xl shadow-orange-100">Sold</button>
                        <button class="bg-rose-50 text-rose-500 px-10 py-5 rounded-[2rem] font-black uppercase text-[10px] tracking-widest hover:bg-rose-500 hover:text-white transition-all">Unsold</button>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-[4rem] p-20 text-center border-2 border-dashed border-slate-100">
            <i class="fas fa-check-circle text-5xl text-teal-500 mb-6"></i>
            <h3 class="text-2xl font-black text-slate-900">Auction Finished</h3>
            <p class="text-slate-400 font-medium">No unsold players remaining in this session.</p>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-[3rem] border border-orange-50 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-orange-50 bg-orange-50/10">
                <h4 class="font-black text-slate-800 uppercase text-xs tracking-widest italic">Live Bidding Log</h4>
            </div>
            <div class="p-4 space-y-4 h-64 overflow-y-auto custom-scrollbar" id="bidLog">
                <div class="p-5 text-center text-slate-400 font-bold text-[10px] uppercase">Waiting for first bid...</div>
            </div>
        </div>
    </div>

    <div class="space-y-8">
        <div class="bg-[#0F172A] p-10 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-orange-500/10 rounded-full blur-3xl"></div>
            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-8 italic">Participating Teams</h4>
            <div class="space-y-6 relative">
                <?php 
                $teams_res = mysqli_query($conn, "SELECT t.* FROM team_master t 
                                                  JOIN auction_team_master at ON t.id = at.team_id 
                                                  WHERE at.auction_id = '$auction_id'");
                while($team = mysqli_fetch_assoc($teams_res)): 
                ?>
                <div class="flex items-center gap-4 border-b border-white/5 pb-4 last:border-0">
                    <img src="../uploads/teams/<?php echo $team['team_logo']; ?>" class="w-10 h-10 rounded-xl bg-white/5 p-1">
                    <div>
                        <p class="text-xs font-bold"><?php echo $team['team_name']; ?></p>
                        <p class="text-[9px] font-black text-orange-400">Purse: ₹<?php echo number_format($team['remaining_budget'] ?? 10000000); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../backpanel/includes/footer.php'; ?>