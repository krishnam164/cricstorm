<?php
include '../config.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$t_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. FETCH ALL 23+ RECORDS FROM TOURNAMENT_MASTER
$query = "SELECT t.*, u.user_fullname, u.user_mobile 
          FROM tournament_master t 
          LEFT JOIN users u ON t.user_id = u.user_id 
          WHERE t.tournament_id = '$t_id'";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

$t = mysqli_fetch_assoc($result);

if (!$t) {
    die("<div class='p-20 text-center font-black text-rose-500 uppercase'>Tournament Not Found</div>");
}
 $logo = trim($t['tournament_logo'] ?? '');
     $logo_path = (strpos($logo, 'uploads/') !== false ? "../".$logo : "../uploads/tournaments/".$logo);

$active_page = 'all_tournaments';
include 'includes/header.php';
?>

<div class="mb-10 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase">League <span class="text-teal-500">Intelligence</span></h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Comprehensive Data for ID #<?php echo $t['tournament_id']; ?></p>
    </div>
    <a href="all_tournaments.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all shadow-lg">
        <i class="fas fa-chevron-left mr-2"></i> Monitor
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    
    <div class="space-y-8">
        <div class="bg-white p-10 rounded-[4rem] border border-teal-50 shadow-sm text-center">
            <div class="w-32 h-32 bg-slate-50 rounded-[2.5rem] mx-auto mb-6 flex items-center justify-center border border-slate-100 shadow-inner overflow-hidden">
                <img src="<?php echo $logo_path; ?>" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='../images/placeholder.png';">
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-1"><?php echo $t['tournament_name']; ?></h3>
            <p class="text-[9px] font-black text-teal-500 uppercase tracking-widest mb-10 italic"><?php echo $t['tournament_auction_title']; ?></p>
            
            <div class="grid grid-cols-2 gap-4 pt-10 border-t border-slate-50">
                <div class="text-left">
                    <p class="text-[9px] font-black text-slate-300 uppercase">Status</p>
                    <span class="text-xs font-bold <?php echo ($t['tournament_status'] == 'Publish') ? 'text-green-500' : 'text-orange-400'; ?>">
                        <?php echo $t['tournament_status']; ?>
                    </span>
                </div>
                <div class="text-left">
                    <p class="text-[9px] font-black text-slate-300 uppercase">Theme</p>
                    <span class="text-xs font-bold text-slate-700"><?php echo $t['tournament_theme']; ?></span>
                </div>
            </div>
        </div>

        <div class="bg-[#0F172A] p-10 rounded-[3.5rem] text-white">
            <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6 italic">Organizer Contact</h4>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-teal-400">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <p class="text-sm font-bold"><?php echo $t['user_fullname'] ?? 'System Admin'; ?></p>
                    <p class="text-[10px] text-slate-500"><?php echo $t['user_mobile']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-8">
        
        <div class="bg-white rounded-[4rem] border border-teal-50 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-teal-50 bg-teal-50/10">
                <h4 class="font-black text-slate-800 uppercase text-xs tracking-widest italic">Auction Configuration</h4>
            </div>
            <div class="p-10 grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <label class="text-[9px] font-black text-slate-300 uppercase block mb-1">Base Value</label>
                    <p class="text-lg font-black text-slate-800">₹<?php echo number_format((float)$t['tournament_base_value']); ?></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-300 uppercase block mb-1">Bid Increment</label>
                    <p class="text-lg font-black text-slate-800">₹<?php echo number_format((float)$t['tournament_bid_value']); ?></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-300 uppercase block mb-1">Team Points</label>
                    <p class="text-lg font-black text-slate-800"><?php echo $t['tournament_team_points']; ?></p>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-300 uppercase block mb-1">Total Players</label>
                    <p class="text-lg font-black text-slate-800"><?php echo $t['tournament_total_players']; ?></p>
                </div>
            </div>
            
            <div class="px-10 pb-10 flex gap-4">
                <span class="px-4 py-2 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest">
                    Type: <?php echo $t['tournament_auction_type']; ?>
                </span>
                <span class="px-4 py-2 bg-teal-50 text-teal-600 rounded-xl text-[9px] font-black uppercase tracking-widest">
                    Forms: <?php echo ($t['tournament_have_form_no']) ? 'Enabled' : 'Disabled'; ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-[3rem] border border-teal-50 shadow-sm">
                <h5 class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Tournament Schedule</h5>
                <div class="flex items-center gap-4">
                    <i class="fas fa-calendar-alt text-teal-500"></i>
                    <p class="text-sm font-bold text-slate-700"><?php echo date('d M, Y', strtotime($t['tournament_date'])); ?></p>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[3rem] border border-teal-50 shadow-sm">
                <h5 class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">Registration Period</h5>
                <p class="text-xs font-bold text-slate-700">
                    <?php echo date('d M', strtotime($t['registration_start_date'])); ?> 
                    <span class="text-slate-300 mx-2">to</span> 
                    <?php echo date('d M, Y', strtotime($t['registration_end_date'])); ?>
                </p>
            </div>
        </div>

        <div class="bg-white rounded-[4rem] border border-teal-50 shadow-sm p-12">
            <h4 class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-6 italic">Tournament Rules & Guidelines</h4>
            <div class="text-slate-600 text-sm leading-relaxed font-medium">
                <?php echo !empty($t['tournament_rules']) ? nl2br($t['tournament_rules']) : 'No specific rules defined for this league.'; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>