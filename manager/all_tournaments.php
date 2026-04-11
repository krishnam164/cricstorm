<?php
include '../config.php';

// 1. ORGANIZER SECURITY GATE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? 'manager';

/** 2. SEARCH & PAGINATION LOGIC */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if ($user_role == 'admin') {
    $where_clause = "WHERE 1=1"; 
} else {
    $where_clause = "WHERE am.user_id = '$user_id'";
}
if ($search) {
    $where_clause .= " AND (tm.tournament_name LIKE '%$search%' OR tm.tournament_id LIKE '%$search%')";
}

$total_query_sql = "SELECT COUNT(DISTINCT tm.tournament_id) as total FROM tournament_master tm JOIN auction_master am ON tm.tournament_id = am.tournament_id $where_clause";
$total_res = mysqli_query($conn, $total_query_sql);
$total_records = mysqli_fetch_assoc($total_res)['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

$query = "SELECT tm.*, am.auction_id FROM tournament_master tm JOIN auction_master am ON tm.tournament_id = am.tournament_id $where_clause GROUP BY tm.tournament_id ORDER BY tm.tournament_id DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

$active_page = 'all_tournaments';
include 'includes/header.php';
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 md:mb-10 px-2">
    <div class="text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic tracking-tight uppercase">My <span class="text-orange-500">Tournaments</span></h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-bold">Managing your hosted competitions</p>
    </div>
    
    <div class="flex justify-center">
        <div class="bg-white px-6 py-3 rounded-xl md:rounded-[2rem] border border-orange-50 shadow-sm text-center">
            <p class="text-[8px] font-bold text-slate-500 uppercase mb-0.5">Active Leagues</p>
            <p class="text-xl md:text-2xl font-black text-slate-900 leading-none"><?php echo number_format($total_records); ?></p>
        </div>
    </div>
</div>

<div class="mb-8 flex flex-col sm:flex-row justify-between items-center gap-4 px-2">
    <form method="GET" class="relative w-full sm:flex-grow sm:max-w-xl group">
        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-orange-500 transition-colors"></i>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search my leagues..." 
               class="w-full pl-12 pr-28 py-4 bg-white border border-orange-50 rounded-xl md:rounded-[2rem] shadow-sm focus:outline-none focus:border-orange-500 transition-all font-bold text-slate-700 text-sm">
        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-5 py-2.5 rounded-lg md:rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all active:scale-95">
            Filter
        </button>
    </form>
    
    <a href="add_tournament.php" class="w-full sm:w-auto text-center bg-orange-500 text-white px-8 py-4 rounded-xl md:rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg active:scale-95">
        <i class="fas fa-plus mr-1"></i> Create New
    </a>
</div>

<div class="bg-white rounded-[1.5rem] md:rounded-[3rem] border border-orange-50 shadow-sm overflow-hidden mb-8 mx-2 md:mx-0">
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-left min-w-[700px]">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 md:px-8 py-5 text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tournament Info</th>
                    <th class="px-6 md:px-8 py-5 text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Schedule</th>
                    <th class="px-6 md:px-8 py-5 text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Live Status</th>
                    <th class="px-6 md:px-8 py-5 text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-orange-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $t_date = strtotime($row['tournament_date'] ?? 'now');
                        $current = time();
                        
                        if ($current < $t_date) {
                            $status = "Upcoming"; $css = "bg-blue-50 text-blue-500";
                        } elseif ($current <= ($t_date + 18000)) {
                            $status = "Live Now"; $css = "bg-green-50 text-green-600 animate-pulse";
                        } else {
                            $status = "Finished"; $css = "bg-slate-100 text-slate-400";
                        }
                ?>
                <tr class="hover:bg-orange-50/20 transition-all group">
                    <td class="px-6 md:px-8 py-5 md:py-6">
                        <div class="text-xs md:text-sm font-bold text-slate-800 uppercase italic leading-tight truncate max-w-[200px]"><?php echo $row['tournament_name']; ?></div>
                        <div class="text-[9px] text-slate-400 font-medium tracking-tight mt-0.5">Ref ID: #T-<?php echo $row['tournament_id']; ?></div>
                    </td>
                    <td class="px-6 md:px-8 py-5 md:py-6 text-center">
                        <div class="text-[11px] font-bold text-slate-600"><?php echo date('d M, Y', $t_date); ?></div>
                        <div class="text-[9px] text-slate-400 font-black uppercase"><?php echo date('h:i A', $t_date); ?></div>
                    </td>
                    <td class="px-6 md:px-8 py-5 md:py-6 text-center">
                        <span class="inline-block px-3 py-1.5 <?php echo $css; ?> rounded-full text-[8px] font-black uppercase tracking-tighter">
                            <?php echo $status; ?>
                        </span>
                    </td>
                    <td class="px-6 md:px-8 py-5 md:py-6">
                        <div class="flex justify-center gap-2 md:gap-3">
                            <a href="manage_teams.php?id=<?php echo $row['tournament_id']; ?>" 
                               class="w-9 h-9 md:w-10 md:h-10 bg-orange-50 text-orange-600 rounded-lg md:rounded-xl flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all active:scale-90 shadow-sm border border-orange-100" 
                               title="Manage Teams">
                                <i class="fas fa-shield-alt text-xs"></i>
                            </a>
                            <a href="auction_monitor.php?id=<?php echo $row['tournament_id']; ?>" 
                               class="w-9 h-9 md:w-10 md:h-10 bg-slate-900 text-white rounded-lg md:rounded-xl flex items-center justify-center hover:bg-orange-500 transition-all active:scale-90 shadow-md" 
                               title="Launch Auction">
                                <i class="fas fa-hammer text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='p-16 md:p-24 text-center'><div class='w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4'><i class='fas fa-folder-open text-slate-200'></i></div><p class='text-[10px] font-bold text-slate-400 uppercase tracking-widest italic'>No assigned tournaments found</p></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex items-center justify-center flex-wrap gap-2 mb-10 px-4">
    <?php 
    $search_param = $search ? "&search=".urlencode($search) : "";
    if($page > 1): ?>
        <a href="?page=<?php echo $page-1 . $search_param; ?>" class="w-10 h-10 bg-white border border-orange-100 rounded-xl flex items-center justify-center text-slate-400 active:scale-95"><i class="fas fa-chevron-left text-xs"></i></a>
    <?php endif; ?>

    <?php 
    $start_p = max(1, $page - 1);
    $end_p = min($total_pages, $page + 1);
    for($i = $start_p; $i <= $end_p; $i++): 
    ?>
        <a href="?page=<?php echo $i . $search_param; ?>" class="w-10 h-10 flex items-center justify-center <?php echo ($page == $i) ? 'bg-orange-500 text-white shadow-lg' : 'bg-white text-slate-400 border border-orange-100'; ?> rounded-xl text-xs font-black transition-all">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="?page=<?php echo $page+1 . $search_param; ?>" class="w-10 h-10 bg-white border border-orange-100 rounded-xl flex items-center justify-center text-slate-400 active:scale-95"><i class="fas fa-chevron-right text-xs"></i></a>
    <?php endif; ?>
</div>

<?php 
if (file_exists('../backpanel/includes/footer.php')) {
    include '../backpanel/includes/footer.php'; 
}
?>