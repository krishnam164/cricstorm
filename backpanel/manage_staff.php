<?php
include '../config.php';

/** * 1. MASTER SECURITY GATE */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$admin_mobile = $_SESSION['admin_mobile'] ?? 'System Admin';

/** * 2. HANDLE STATUS UPDATES */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = mysqli_real_escape_string($conn, $_GET['action']);
    
    if ($action == 'delete') {
        $update_query = "UPDATE users SET user_status = 'Delete' WHERE id = $id";
    } else {
        $status = ($action == 'approve') ? 'Publish' : 'Draft';
        $update_query = "UPDATE users SET user_status = '$status' WHERE id = $id";
    }
    mysqli_query($conn, $update_query);
    header("Location: manage_staff.php");
    exit();
}

/** * 3. SEARCH & PAGINATION LOGIC */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$where_clause = "WHERE user_status != 'Delete'";
if ($search) {
    $where_clause .= " AND (user_fullname LIKE '%$search%' OR user_email LIKE '%$search%' OR user_mobile LIKE '%$search%')";
}

$total_query = mysqli_query($conn, "SELECT COUNT(user_id) as total FROM users $where_clause");
if(!$total_query){
    die("Query Failed: " . mysqli_error($conn));
}
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

$query = "SELECT * FROM users $where_clause ORDER BY user_id DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

$active_page = 'manage_staff';
include 'includes/header.php'; 
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-2xl font-black text-slate-900 italic">Staff <span class="text-blue-800"> Management</span></h2>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Manage Organizers & Managers</p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="bg-white px-6 py-3 rounded-2xl border border-teal-50 shadow-sm text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Total Staff</p>
            <p class="text-xl font-black text-slate-900"><?php echo number_format($total_records); ?></p>
        </div>
    </div>
</div>

<div class="mb-8 flex justify-between items-center gap-4">
    <form method="GET" class="relative flex-grow max-w-xl">
        <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search staff members..." 
               class="w-full pl-14 pr-32 py-5 bg-white border border-teal-50 rounded-[2rem] shadow-sm focus:outline-none focus:border-teal-500 transition-all font-medium text-slate-700">
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all">
            Search
        </button>
    </form>
    
    <button class="bg-teal-500 text-white px-8 py-5 rounded-[2rem] text-xs font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-teal-500/20">
        + Add New Staff
    </button>
</div>

<div class="bg-white rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Full Name</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mobile</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Role</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($u = mysqli_fetch_assoc($result)) {
                        $role_color = ($u['user_role'] == 'manager') ? 'text-purple-500 bg-purple-50' : 'text-orange-500 bg-orange-50';
                ?>
                <tr class="hover:bg-teal-50/20 transition-colors">
                    <td class="px-8 py-6">
                        <div class="font-bold text-slate-700 text-sm"><?php echo $u['user_fullname']; ?></div>
                        <div class="text-[10px] text-slate-400"><?php echo $u['user_email']; ?></div>
                    </td>
                    <td class="px-8 py-6 text-sm font-medium text-slate-600"><?php echo $u['user_mobile']; ?></td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest <?php echo $role_color; ?>">
                            <?php echo $u['user_role']; ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-3 py-1 <?php echo ($u['user_status'] == 'Publish') ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600'; ?> rounded-full text-[9px] font-black uppercase">
                            <?php echo ($u['user_status'] == 'Publish') ? 'Active' : 'Pending'; ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-3">
                            <a href="?action=<?php echo ($u['user_status'] == 'Draft') ? 'approve' : 'suspend'; ?>&id=<?php echo $u['user_id']; ?>" class="w-8 h-8 <?php echo ($u['user_status'] == 'Draft') ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600'; ?> rounded-lg flex items-center justify-center hover:opacity-70 transition-all shadow-sm">
                                <i class="fas <?php echo ($u['user_status'] == 'Draft') ? 'fa-check' : 'fa-pause'; ?> text-xs"></i>
                            </a>
                            <a href="?action=delete&id=<?php echo $u['user_id']; ?>" onclick="return confirm('Delete staff member?')" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="flex items-center justify-center gap-2 mb-10">
    <?php 
    $search_param = $search ? "&search=$search" : "";
    if($page > 1): ?>
        <a href="?page=<?php echo $page-1 . $search_param; ?>" class="w-10 h-10 bg-white border border-teal-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-teal-500 hover:text-white transition-all shadow-sm"><i class="fas fa-chevron-left"></i></a>
    <?php endif; ?>

    <?php 
    $start = max(1, $page - 1);
    $end = min($total_pages, $page + 1);
    for($i = $start; $i <= $end; $i++): 
    ?>
        <a href="?page=<?php echo $i . $search_param; ?>" class="px-5 py-2 <?php echo ($page == $i) ? 'bg-teal-500 text-white' : 'bg-white text-slate-400'; ?> border border-teal-50 rounded-xl text-xs font-bold transition-all shadow-sm">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="?page=<?php echo $page+1 . $search_param; ?>" class="w-10 h-10 bg-white border border-teal-50 rounded-xl flex items-center justify-center text-slate-400 hover:bg-teal-500 hover:text-white transition-all shadow-sm"><i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>