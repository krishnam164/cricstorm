<?php
include '../config.php';

/** 1. MASTER SECURITY GATE */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

/** 2. HANDLE DELETE ACTION */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $img_res = mysqli_query($conn, "SELECT photo FROM player_master WHERE player_id = $id");
    $img_data = mysqli_fetch_assoc($img_res);
    if(!empty($img_data['photo'])) { @unlink('../' . $img_data['photo']); }

    mysqli_query($conn, "DELETE FROM player_master WHERE player_id = $id");
    header("Location: player_directory.php?msg=deleted");
    exit();
}

/** 3. SEARCH, TOURNAMENT FILTER & PAGINATION LOGIC */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$tournament_id = isset($_GET['tournament_id']) ? (int)$_GET['tournament_id'] : 0;

$limit = 20; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Build conditions using the table alias 'p' for player_master
$conditions = [];
if ($search) {
    $conditions[] = "(p.name LIKE '%$search%' OR p.mobile_no LIKE '%$search%' OR p.player_id LIKE '%$search%')";
}
if ($tournament_id > 0) {
    $conditions[] = "p.tournament_id = $tournament_id";
}

$where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

// Count filtered records (using alias 'p')
$total_query = mysqli_query($conn, "SELECT COUNT(p.player_id) as total FROM player_master p $where_clause");
$total_data = mysqli_fetch_assoc($total_query);
$total_records = $total_data['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

// Main Query with JOIN
$query = "SELECT p.*, t.tournament_name 
          FROM player_master p 
          LEFT JOIN tournament_master t ON p.tournament_id = t.tournament_id 
          $where_clause 
          ORDER BY p.player_id DESC 
          LIMIT $offset, $limit";

$result = mysqli_query($conn, $query);

// Fetch all tournaments for the dropdown filter
$tournament_list = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_name ASC");

$active_page = 'player_directory';
include 'includes/header.php'; 
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic uppercase">
            <span class="text-orange-500">Player</span> Directory
        </h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Registry Management • Page <?php echo $page; ?></p>
    </div>
    
    <div class="flex items-center gap-4">
        <form method="GET" class="bg-white px-4 py-2 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-3">
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <input type="hidden" name="tournament_id" value="<?php echo $tournament_id; ?>">
            <span class="text-[10px] font-black text-slate-400 uppercase italic">Jump</span>
            <input type="number" name="page" min="1" max="<?php echo $total_pages; ?>" placeholder="<?php echo $page; ?>" class="w-10 text-center text-xs font-bold focus:outline-none bg-transparent">
            <button type="submit" class="text-orange-500"><i class="fas fa-bolt text-[10px]"></i></button>
        </form>
        
        <div class="bg-white px-8 py-3 rounded-2xl border border-slate-100 shadow-sm text-center">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Found</p>
            <p class="text-xl font-black text-slate-900 leading-none"><?php echo number_format($total_records); ?></p>
        </div>
    </div>
</div>

<div class="mb-10">
    <form method="GET" class="flex flex-col lg:flex-row gap-4 max-w-5xl">
        <div class="relative min-w-[280px]">
            <select name="tournament_id" onchange="this.form.submit()" 
                    class="w-full appearance-none pl-6 pr-10 py-5 bg-white border border-slate-100 rounded-[2rem] shadow-sm focus:outline-none focus:border-orange-500 font-bold text-slate-700">
                <option value="0">All Active Tournaments</option>
                <?php if($tournament_list): mysqli_data_seek($tournament_list, 0); ?>
                    <?php while($t_row = mysqli_fetch_assoc($tournament_list)): ?>
                        <option value="<?php echo $t_row['tournament_id']; ?>" <?php echo ($tournament_id == $t_row['tournament_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t_row['tournament_name']); ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
            <i class="fas fa-trophy absolute right-6 top-1/2 -translate-y-1/2 text-orange-500 pointer-events-none"></i>
        </div>

        <div class="relative flex-1">
            <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, mobile, or ID..." 
                   class="w-full pl-16 pr-40 py-5 bg-white border border-slate-100 rounded-[2rem] shadow-sm focus:outline-none focus:border-orange-500 transition-all font-bold text-slate-700 placeholder:text-slate-300">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-8 py-3.5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all shadow-lg shadow-slate-200">
                Filter Results
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Player Profile</th>
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tactical Role</th>
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $photo = trim($row['photo'] ?? '');
                        $photo_path = empty($photo) ? "../images/default_player.png" : "../" . $photo;
                        
                        $role = $row['player_role'] ?? 'General';
                        $role_css = "bg-slate-100 text-slate-500";
                        if(stripos($role, 'Batsman') !== false) $role_css = "bg-blue-50 text-blue-600 border border-blue-100";
                        if(stripos($role, 'Bowler') !== false) $role_css = "bg-rose-50 text-rose-600 border border-rose-100";
                        if(stripos($role, 'All') !== false) $role_css = "bg-orange-50 text-orange-600 border border-orange-100";
                ?>
                <tr class="hover:bg-slate-50/50 transition-all duration-300 group">
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-5">
                            <div class="relative">
                                <img src="<?php echo $photo_path; ?>" class="w-14 h-14 rounded-2xl object-cover bg-slate-100 border-2 border-white shadow-sm group-hover:scale-105 transition-transform" alt="Player Photo">
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg flex items-center justify-center shadow-sm text-[8px] font-black text-slate-400">#<?php echo $row['player_id']; ?></span>
                            </div>
                            <div>
                                <div class="text-base font-black text-slate-800 uppercase italic tracking-tight leading-tight"><?php echo $row['name']; ?></div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-phone text-[8px]"></i> <?php echo $row['mobile_no']; ?>
                                    <?php if(!empty($row['tournament_name'])): ?>
                                        <span class="ml-2 text-orange-500 font-black">• <?php echo $row['tournament_name']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-10 py-8">
                        <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest <?php echo $role_css; ?>">
                            <?php echo $role; ?>
                        </span>
                    </td>
                    <td class="px-10 py-8">
                        <div class="flex justify-center gap-3 group-hover:opacity-100 transition-opacity">
                            <a href="edit_player.php?id=<?php echo $row['player_id']; ?>" class="w-10 h-10 bg-white border border-slate-100 text-slate-600 rounded-xl flex items-center justify-center hover:bg-orange-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <a href="?delete_id=<?php echo $row['player_id']; ?>" onclick="return confirm('Critical: Permanent delete?')" class="w-10 h-10 bg-white border border-slate-100 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='3' class='p-32 text-center'><div class='text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]'>No Athletes logged for this selection</div></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php if($total_pages > 1): ?>
<div class="flex items-center justify-center gap-2 mb-20">
    <?php 
    $pagination_params = http_build_query([
        'search' => $search,
        'tournament_id' => $tournament_id
    ]);
    $url_prefix = "?" . $pagination_params . "&page=";

    if($page > 1): ?>
        <a href="<?php echo $url_prefix . ($page-1); ?>" class="w-12 h-12 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:bg-orange-500 hover:text-white transition-all shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
    <?php endif; ?>

    <?php 
    $start = max(1, $page - 1);
    $end = min($total_pages, $page + 1);
    for($i = $start; $i <= $end; $i++): 
    ?>
        <a href="<?php echo $url_prefix . $i; ?>" class="w-12 h-12 flex items-center justify-center <?php echo ($page == $i) ? 'bg-orange-500 text-white shadow-lg shadow-orange-200' : 'bg-white text-slate-400 hover:bg-slate-50'; ?> border border-slate-100 rounded-xl text-xs font-black transition-all">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="<?php echo $url_prefix . ($page+1); ?>" class="w-12 h-12 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:bg-orange-500 hover:text-white transition-all shadow-sm"><i class="fas fa-chevron-right text-xs"></i></a>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>