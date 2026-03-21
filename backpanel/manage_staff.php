<?php
include '../config.php';

/** 1. MASTER SECURITY GATE */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

/** 2. HANDLE STATUS UPDATES (Fixed ID inconsistency) */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = mysqli_real_escape_string($conn, $_GET['action']);
    
    // Using user_id to match your table structure
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
<div id="toast" class="fixed top-10 right-10 z-50 animate__animated animate__fadeInRight">
    <div class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 border border-white/10">
        <div class="w-8 h-8 bg-teal-500 rounded-full flex items-center justify-center text-xs">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-[10px] font-black uppercase tracking-widest">Action processed successfully</p>
    </div>
</div>
<script>setTimeout(() => { document.getElementById('toast').classList.add('animate__fadeOutRight'); }, 3000);</script>
<?php endif; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic uppercase">Staff <span class="text-blue-600">Operations</span></h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Authority & Permission Control</p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="bg-white px-8 py-4 rounded-[1.5rem] border border-slate-100 shadow-sm text-center">
            <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest mb-1">Active Registry</p>
            <p class="text-2xl font-black text-slate-900 leading-none"><?php echo number_format($total_records); ?></p>
        </div>
    </div>
</div>

<div class="mb-10 flex flex-wrap justify-between items-center gap-6">
    <form method="GET" class="relative flex-grow max-w-xl">
        <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email or mobile..." 
               class="w-full pl-16 pr-32 py-5 bg-white border border-slate-100 rounded-[2rem] shadow-sm focus:outline-none focus:border-blue-500 transition-all font-bold text-slate-700 placeholder:text-slate-300">
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-8 py-3.5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg shadow-slate-200">
            Filter
        </button>
    </form>
    
    <a href="add_staff.php" class="bg-blue-600 text-white px-10 py-5 rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-blue-200 flex items-center gap-3">
        <i class="fas fa-user-plus text-xs"></i> Add New Staff
    </a>
</div>

<div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-sm overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Full Identity</th>
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Role</th>
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Access Status</th>
                    <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($u = mysqli_fetch_assoc($result)) {
                        $is_manager = ($u['user_role'] == 'manager');
                        $is_active = ($u['user_status'] == 'Publish');
                ?>
                <tr class="hover:bg-blue-50/30 transition-all duration-300 group">
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-black text-slate-400 uppercase group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <?php echo substr($u['user_fullname'], 0, 1); ?>
                            </div>
                            <div>
                                <div class="font-black text-slate-800 text-sm uppercase italic"><?php echo $u['user_fullname']; ?></div>
                                <div class="text-[10px] text-slate-400 font-bold"><?php echo $u['user_email']; ?> • <?php echo $u['user_mobile']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-10 py-8">
                        <span class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest <?php echo $is_manager ? 'text-purple-600 bg-purple-50' : 'text-orange-600 bg-orange-50'; ?> border <?php echo $is_manager ? 'border-purple-100' : 'border-orange-100'; ?>">
                            <?php echo $u['user_role']; ?>
                        </span>
                    </td>
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full <?php echo $is_active ? 'bg-green-500 animate-pulse' : 'bg-amber-400'; ?>"></span>
                            <span class="text-[10px] font-black uppercase tracking-widest <?php echo $is_active ? 'text-green-600' : 'text-amber-600'; ?>">
                                <?php echo $is_active ? 'Active' : 'Pending'; ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-10 py-8">
                        <div class="flex justify-center gap-3">
                            <a href="?action=<?php echo $is_active ? 'suspend' : 'approve'; ?>&id=<?php echo $u['user_id']; ?>" 
                               class="w-10 h-10 <?php echo $is_active ? 'bg-amber-50 text-amber-500' : 'bg-green-50 text-green-500'; ?> rounded-xl flex items-center justify-center hover:scale-110 transition-all shadow-sm border <?php echo $is_active ? 'border-amber-100' : 'border-green-100'; ?>"
                               title="<?php echo $is_active ? 'Suspend' : 'Activate'; ?>">
                                <i class="fas <?php echo $is_active ? 'fa-pause' : 'fa-check'; ?> text-xs"></i>
                            </a>
                            
                            <a href="?action=delete&id=<?php echo $u['user_id']; ?>" 
                               onclick="return confirm('CRITICAL: Remove this staff member permanently?')" 
                               class="w-10 h-10 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm border border-rose-100">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php } } else { ?>
                <tr>
                    <td colspan="4" class="p-20 text-center">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">No staff members found in registry</p>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex items-center justify-center gap-2 mb-20">
    <?php 
    $search_param = $search ? "&search=".urlencode($search) : "";
    if($page > 1): ?>
        <a href="?page=<?php echo $page-1 . $search_param; ?>" class="w-12 h-12 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-chevron-left text-xs"></i></a>
    <?php endif; ?>

    <?php 
    for($i = max(1, $page - 1); $i <= min($total_pages, $page + 1); $i++): 
    ?>
        <a href="?page=<?php echo $i . $search_param; ?>" class="w-12 h-12 flex items-center justify-center <?php echo ($page == $i) ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white text-slate-400 hover:bg-slate-50'; ?> border border-slate-100 rounded-xl text-xs font-black transition-all">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="?page=<?php echo $page+1 . $search_param; ?>" class="w-12 h-12 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:bg-blue-600 hover:text-white transition-all"><i class="fas fa-chevron-right text-xs"></i></a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>