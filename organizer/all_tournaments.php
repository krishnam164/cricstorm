<?php
include '../config.php';

// 1. ORGANIZER SECURITY GATE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? 'manager'; // Assume manager if not set

/** 2. SEARCH & PAGINATION LOGIC */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

/** 
 * NEW FILTER LOGIC:
 * If user is Admin -> Show all
 * If user is Manager/Organizer -> Only show if their ID is in auction_master
 */
if ($user_role == 'admin') {
    $where_clause = "WHERE 1=1"; 
} else {
    // Only show tournaments assigned to this specific manager in auction_master
    $where_clause = "WHERE am.user_id = '$user_id'";
}

if ($search) {
    $where_clause .= " AND (tm.tournament_name LIKE '%$search%' OR tm.tournament_id LIKE '%$search%')";
}

// Get total count using a JOIN to respect assignments
$total_query_sql = "SELECT COUNT(DISTINCT tm.tournament_id) as total 
                    FROM tournament_master tm
                    JOIN auction_master am ON tm.tournament_id = am.tournament_id 
                    $where_clause";
$total_res = mysqli_query($conn, $total_query_sql);
$total_records = mysqli_fetch_assoc($total_res)['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Fetch results with JOIN
$query = "SELECT tm.*, am.auction_id 
          FROM tournament_master tm
          JOIN auction_master am ON tm.tournament_id = am.tournament_id 
          $where_clause 
          GROUP BY tm.tournament_id
          ORDER BY tm.tournament_id DESC 
          LIMIT $offset, $limit";
          
$result = mysqli_query($conn, $query);

$active_page = 'all_tournaments';
include 'includes/header.php';
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-2xl font-black text-slate-900 italic tracking-tight">My <span class="text-orange-500">Tournaments</span></h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest font-bold">Managing your hosted competitions</p>
    </div>
    
    <div class="bg-white px-8 py-4 rounded-[2rem] border border-orange-50 shadow-sm text-center">
        <p class="text-[9px] font-bold text-slate-500 uppercase mb-1">Active Leagues</p>
        <p class="text-2xl font-black text-slate-900"><?php echo number_format($total_records); ?></p>
    </div>
</div>

<div class="mb-8 flex justify-between items-center gap-4">
    <form method="GET" class="relative flex-grow max-w-xl">
        <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search my tournaments..." 
               class="w-full pl-14 pr-32 py-5 bg-white border border-orange-50 rounded-[2rem] shadow-sm focus:outline-none focus:border-orange-500 transition-all font-medium text-slate-700">
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all">
            Filter
        </button>
    </form>
    
    <a href="add_tournament.php" class="bg-orange-500 text-white px-8 py-5 rounded-[2rem] text-xs font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-orange-500/20">
        + Create New
    </a>
</div>



<div class="bg-white rounded-[3rem] border border-orange-50 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tournament Info</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Date & Time</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Live Status</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-orange-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $t_date = strtotime($row['tournament_date'] ?? 'now');
                        $current = time();
                        
                        // Dynamic Status Logic
                        if ($current < $t_date) {
                            $status = "Upcoming"; $css = "bg-blue-50 text-blue-500";
                        } elseif ($current <= ($t_date + 18000)) {
                            $status = "Live Now"; $css = "bg-green-50 text-green-600 animate-pulse";
                        } else {
                            $status = "Finished"; $css = "bg-slate-100 text-slate-400";
                        }
                ?>
                <tr class="hover:bg-orange-50/20 transition-colors">
                    <td class="px-8 py-6">
                        <div class="text-sm font-bold text-slate-800"><?php echo $row['tournament_name']; ?></div>
                        <div class="text-[10px] text-slate-400 font-medium tracking-tight">Ref ID: #T-<?php echo $row['tournament_id']; ?></div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="text-xs font-bold text-slate-600"><?php echo date('d M, Y', $t_date); ?></div>
                        <div class="text-[10px] text-slate-400 font-black uppercase"><?php echo date('h:i A', $t_date); ?></div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="px-4 py-1.5 <?php echo $css; ?> rounded-full text-[9px] font-black uppercase">
                            <?php echo $status; ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-3">
                            <a href="manage_teams.php?id=<?php echo $row['tournament_id']; ?>" class="w-9 h-9 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all shadow-sm" title="Manage Teams">
                                <i class="fas fa-shield-alt text-xs"></i>
                            </a>
                            <a href="auction_monitor.php?id=<?php echo $row['tournament_id']; ?>" class="w-9 h-9 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-orange-500 transition-all shadow-sm" title="Launch Auction">
                                <i class="fas fa-hammer text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='4' class='p-20 text-center text-slate-400 font-bold uppercase tracking-widest italic'>No tournaments found under your account</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex items-center justify-center gap-2 mb-10">
    <?php 
    $search_param = $search ? "&search=$search" : "";
    if($page > 1): ?>
        <a href="?page=<?php echo $page-1 . $search_param; ?>" class="w-10 h-10 bg-white border border-orange-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-orange-500 hover:text-white transition-all shadow-sm"><i class="fas fa-chevron-left"></i></a>
    <?php endif; ?>

    <?php 
    $start = max(1, $page - 1);
    $end = min($total_pages, $page + 1);
    for($i = $start; $i <= $end; $i++): 
    ?>
        <a href="?page=<?php echo $i . $search_param; ?>" class="px-5 py-2 <?php echo ($page == $i) ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'bg-white text-slate-400'; ?> border border-orange-50 rounded-xl text-xs font-bold transition-all shadow-sm">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="?page=<?php echo $page+1 . $search_param; ?>" class="w-10 h-10 bg-white border border-orange-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-orange-500 hover:text-white transition-all shadow-sm"><i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
</div>

<?php 
if (file_exists('../backpanel/includes/footer.php')) {
    include '../backpanel/includes/footer.php'; 
}
?>