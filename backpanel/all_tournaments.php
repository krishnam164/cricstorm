<?php

include '../config.php';

/** * 1. MASTER SECURITY GATE
 * Only allowing Super Admins from 'user_master' with is_admin = 1.
 */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$admin_mobile = $_SESSION['admin_mobile'] ?? 'System Admin';

/** * 2. HANDLE MASTER DELETE ACTION */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM tournament_master WHERE tournament_id = $id");
    header("Location: all_tournaments.php");
    exit();
}

/** * 3. SEARCH & PAGINATION LOGIC */
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter logic
$where_clause = $search ? "WHERE tournament_name LIKE '%$search%' OR tournament_id LIKE '%$search%'" : "";

// Get total count for pagination
$total_query = mysqli_query($conn, "SELECT COUNT(tournament_id) as total FROM tournament_master $where_clause");
$total_records = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_records / $limit);

// Fetch results
$query = "SELECT * FROM tournament_master $where_clause ORDER BY tournament_id DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);

$active_page = 'all_tournaments';
include 'includes/header.php'; 
?>
<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
    <div>
        <h2 class="text-2xl font-black text-slate-900 italic">Global <span class="text-teal-500">Tournament Monitor</span></h2>
        <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Comprehensive League Oversight</p>
    </div>
    
    <div class="flex items-center gap-4">
        <a href="add_tournament.php" class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all flex items-center gap-2 shadow-lg shadow-slate-200">
            <i class="fas fa-plus"></i>
            Add Tournament
        </a>

        <form method="GET" class="bg-white px-4 py-2 rounded-xl border border-teal-50 shadow-sm flex items-center gap-3">
            <input type="hidden" name="search" value="<?php echo $search; ?>">
            <span class="text-[10px] font-bold text-slate-400 uppercase">Jump</span>
            <input type="number" name="page" min="1" max="<?php echo $total_pages; ?>" placeholder="<?php echo $page; ?>" class="w-10 text-center text-xs font-bold focus:outline-none">
            <button type="submit" class="text-teal-500"><i class="fas fa-arrow-right text-[10px]"></i></button>
        </form>
        
        <div class="bg-white px-6 py-3 rounded-2xl border border-teal-50 shadow-sm text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase">Total</p>
            <p class="text-xl font-black text-slate-900"><?php echo number_format($total_records); ?></p>
        </div>
    </div>
</div>

<div class="mb-8">
    <form method="GET" class="relative max-w-xl">
        <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-300"></i>
        <input type="text" name="search" value="<?php echo $search; ?>" placeholder="Search by tournament name or ID..." 
               class="w-full pl-14 pr-32 py-5 bg-white border border-teal-50 rounded-[2rem] shadow-sm focus:outline-none focus:border-teal-500 transition-all font-medium text-slate-700">
        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all">
            Filter
        </button>
    </form>
</div>

<div class="bg-white rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase">Logo</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase">Tournament Info</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase">Schedule</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase">Status</th>
                    <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase text-center">Control</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-teal-50">
                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $t_time = strtotime($row['tournament_date'] ?? 'now');
                        $current_time = time();
                        
                        // Dynamic Status Logic
                        if ($current_time < $t_time) {
                            $status = "Upcoming"; $css = "bg-blue-50 text-blue-500";
                        } elseif ($current_time <= ($t_time + 18000)) {
                            $status = "Live Now"; $css = "bg-teal-50 text-teal-600 animate-pulse";
                        } else {
                            $status = "Past Event"; $css = "bg-slate-100 text-slate-400";
                        }

                        $logo = trim($row['tournament_logo'] ?? '');
                        $logo_path = (strpos($logo, 'uploads/') !== false ? "../".$logo : "../uploads/tournaments/".$logo);
                ?>
                <tr class="hover:bg-teal-50/20 transition-colors">
                    <td class="px-8 py-6">
                        <img src="<?php echo $logo_path; ?>" class="w-12 h-12 rounded-xl object-contain bg-slate-50 p-1 border border-slate-100" onerror="this.onerror=null;this.src='../images/default_tournament.png';" alt="<?php echo $row['tournament_name']; ?>">
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-sm font-bold text-slate-800"><?php echo $row['tournament_name']; ?></div>
                        <div class="text-[10px] text-slate-400 font-medium">ID: #<?php echo $row['tournament_id']; ?></div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="text-xs font-bold text-slate-600"><?php echo date('d M, Y', $t_time); ?></div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="px-4 py-1.5 <?php echo $css; ?> rounded-full text-[9px] font-black uppercase">
                            <?php echo $status; ?>
                        </span>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-3">
                            <a href="view_details.php?id=<?php echo $row['tournament_id']; ?>" class="w-8 h-8 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center hover:bg-teal-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="?delete_id=<?php echo $row['tournament_id']; ?>" onclick="return confirm('Master Delete: Are you sure?')" class="w-8 h-8 bg-red-50 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' class='p-20 text-center text-slate-400 font-bold uppercase tracking-widest'>No tournaments found matching '{$search}'</td></tr>";
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