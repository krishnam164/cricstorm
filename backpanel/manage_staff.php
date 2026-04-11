<?php
include '../config.php';

/** 1. MASTER SECURITY GATE */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

/** 2. HANDLE STATUS UPDATES */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = mysqli_real_escape_string($conn, $_GET['action']);
    
    if ($action == 'delete') {
        $update_query = "UPDATE users SET user_status = 'Delete' WHERE user_id = $id";
    } else {
        $status = ($action == 'approve') ? 'Publish' : 'Draft';
        $update_query = "UPDATE users SET user_status = '$status' WHERE user_id = $id";
    }
    
    if(mysqli_query($conn, $update_query)) {
        header("Location: manage_staff.php?msg=" . $action);
    } else {
        header("Location: manage_staff.php?msg=error");
    }
    exit();
}

/** 3. SEARCH & PAGINATION LOGIC */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$where_clause = "WHERE user_status != 'Delete'";
if ($search) {
    $where_clause .= " AND (user_fullname LIKE '%$search%' OR user_email LIKE '%$search%' OR user_mobile LIKE '%$search%')";
}

$total_query = mysqli_query($conn, "SELECT COUNT(user_id) as total FROM users $where_clause");
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

$query = "SELECT * FROM users $where_clause ORDER BY user_id DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

$active_page = 'manage_staff';
include 'includes/header.php'; 
?>

<?php if(isset($_GET['msg'])): ?>
<div id="toast" class="fixed top-5 right-5 left-5 md:left-auto md:top-10 md:right-10 z-[200] animate__animated animate__fadeInDown md:animate__fadeInRight">
    <div class="bg-slate-900 text-white px-5 py-4 rounded-xl shadow-2xl flex items-center gap-4 border border-white/10">
        <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center text-xs shrink-0">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest leading-tight">Action processed successfully</p>
    </div>
</div>
<script>setTimeout(() => { document.getElementById('toast')?.classList.add('animate__fadeOut'); }, 3000);</script>
<?php endif; ?>

<div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8 md:mb-10 px-2">
    <div class="text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic uppercase tracking-tighter">
            Staff <span class="text-blue-600">Operations</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Authority & Permission Control</p>
    </div>
    
    <div class="flex justify-center md:justify-end">
        <div class="bg-white px-6 md:px-8 py-3 md:py-4 rounded-xl md:rounded-[1.5rem] border border-slate-100 shadow-sm text-center">
            <p class="text-[7px] md:text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">Active Registry</p>
            <p class="text-xl md:text-2xl font-black text-slate-900 leading-none"><?php echo number_format($total_records); ?></p>
        </div>
    </div>
</div>

<div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4 px-2">
    <form method="GET" class="relative w-full md:max-w-xl">
        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search identity..." 
               class="w-full pl-12 pr-28 py-4 bg-white border border-slate-100 rounded-xl md:rounded-[2rem] shadow-sm focus:outline-none focus:border-blue-500 transition-all font-bold text-slate-700 text-sm">
        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-5 py-2.5 rounded-lg md:rounded-[1.5rem] text-[9px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-md">
            Filter
        </button>
    </form>
    
    <a href="add_staff.php" class="w-full md:w-auto bg-blue-600 text-white px-8 py-4 rounded-xl md:rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-3">
        <i class="fas fa-user-plus text-xs"></i> Add <span class="md:inline">New Staff</span>
    </a>
</div>

<div class="bg-white rounded-[1.5rem] md:rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden mb-12 mx-2 md:mx-0">
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-8 md:px-10 py-5 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Full Identity</th>
                    <th class="px-8 md:px-10 py-5 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Role</th>
                    <th class="px-8 md:px-10 py-5 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Access Status</th>
                    <th class="px-8 md:px-10 py-5 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Operations</th>
                </tr>
            </thead>
           <tbody class="divide-y divide-slate-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($u = mysqli_fetch_assoc($result)) {
                        $user_id = $u['user_id'];
                        $is_manager = ($u['user_role'] == 'manager');
                        $is_active = ($u['user_status'] == 'Publish');

                        $assigned_sql = "SELECT tm.tournament_name FROM auction_master am JOIN tournament_master tm ON am.tournament_id = tm.tournament_id WHERE am.user_id = '$user_id' LIMIT 2";
                        $assigned_res = mysqli_query($conn, $assigned_sql);
                        $tournaments = [];
                        while($t = mysqli_fetch_assoc($assigned_res)) { $tournaments[] = $t['tournament_name']; }
                        $assigned_count = count($tournaments);
                ?>
                <tr class="hover:bg-blue-50/30 transition-all duration-300 group">
                    <td class="px-8 md:px-10 py-6 md:py-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-slate-100 flex items-center justify-center font-black text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all text-xs md:text-base shrink-0">
                                <?php echo substr($u['user_fullname'], 0, 1); ?>
                            </div>
                            <div class="min-w-0">
                                <div class="font-black text-slate-800 text-xs md:text-sm uppercase italic truncate"><?php echo $u['user_fullname']; ?></div>
                                <div class="text-[8px] md:text-[10px] text-slate-400 font-bold truncate"><?php echo $u['user_email']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 md:px-10 py-6 md:py-8">
                        <span class="px-3 md:px-4 py-1.5 md:py-2 rounded-lg md:rounded-xl text-[8px] md:text-[9px] font-black uppercase tracking-widest <?php echo $is_manager ? 'text-purple-600 bg-purple-50' : 'text-orange-600 bg-orange-50'; ?> border <?php echo $is_manager ? 'border-purple-100' : 'border-orange-100'; ?>">
                            <?php echo $u['user_role']; ?>
                        </span>
                    </td>
                    <td class="px-8 md:px-10 py-6 md:py-8">
                        <div class="flex flex-col gap-2 min-w-[150px]">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full <?php echo $is_active ? 'bg-green-500 animate-pulse' : 'bg-amber-400'; ?>"></span>
                                <span class="text-[9px] md:text-[10px] font-black uppercase tracking-widest <?php echo $is_active ? 'text-green-600' : 'text-amber-600'; ?>">
                                    <?php echo $is_active ? 'Active' : 'Pending'; ?>
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex flex-wrap gap-1 items-center flex-grow">
                                    <?php if($assigned_count > 0): ?>
                                        <?php foreach($tournaments as $name): ?>
                                            <span class="text-[8px] font-bold text-blue-500 bg-blue-50 px-2 py-0.5 rounded border border-blue-100 truncate max-w-[80px]">
                                                <?php echo $name; ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if($assigned_count > 2) echo '<span class="text-[8px] font-black text-slate-400">...</span>'; ?>
                                    <?php else: ?>
                                        <span class="text-[8px] font-bold text-slate-300 uppercase italic">No Controls</span>
                                    <?php endif; ?>
                                </div>
                                <a href="assign_tournament.php?uid=<?php echo $user_id; ?>" class="w-6 h-6 flex items-center justify-center rounded bg-slate-100 text-slate-400 hover:bg-blue-600 hover:text-white transition-all shrink-0 border border-slate-200">
                                    <i class="fas fa-plus text-[8px]"></i>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 md:px-10 py-6 md:py-8">
                        <div class="flex justify-center gap-2 md:gap-3">
                            <a href="?action=<?php echo $is_active ? 'suspend' : 'approve'; ?>&id=<?php echo $u['user_id']; ?>" 
                               class="w-9 h-9 md:w-10 md:h-10 <?php echo $is_active ? 'bg-amber-50 text-amber-500 border-amber-100' : 'bg-green-50 text-green-500 border-green-100'; ?> rounded-lg md:rounded-xl flex items-center justify-center hover:scale-105 active:scale-90 transition-all shadow-sm border">
                                <i class="fas <?php echo $is_active ? 'fa-pause' : 'fa-check'; ?> text-xs"></i>
                            </a>
                            <a href="?action=delete&id=<?php echo $u['user_id']; ?>" 
                               onclick="return confirm('CRITICAL: Permanently remove staff?')" 
                               class="w-9 h-9 md:w-10 md:h-10 bg-rose-50 text-rose-500 rounded-lg md:rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white active:scale-90 transition-all shadow-sm border border-rose-100">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php } } else { ?>
                    <tr><td colspan="4" class="p-20 text-center text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Staff registry empty</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex items-center justify-center flex-wrap gap-2 mb-12 px-4">
    <?php 
    $search_param = $search ? "&search=".urlencode($search) : "";
    if($page > 1): ?>
        <a href="?page=<?php echo $page-1 . $search_param; ?>" class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 shadow-sm"><i class="fas fa-chevron-left text-xs"></i></a>
    <?php endif; ?>

    <?php for($i = max(1, $page - 1); $i <= min($total_pages, $page + 1); $i++): ?>
        <a href="?page=<?php echo $i . $search_param; ?>" class="w-10 h-10 flex items-center justify-center <?php echo ($page == $i) ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-slate-400'; ?> border border-slate-100 rounded-xl text-xs font-black transition-all">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="?page=<?php echo $page+1 . $search_param; ?>" class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 shadow-sm"><i class="fas fa-chevron-right text-xs"></i></a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>