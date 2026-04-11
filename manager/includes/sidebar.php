<?php
$current_page = basename($_SERVER['PHP_SELF']);
// SAFETY CHECK: Get approval status
$perm_res = mysqli_query($conn, "SELECT manager_request FROM users WHERE user_id = '$user_id'");
$is_approved = false;
if ($perm_res && mysqli_num_rows($perm_res) > 0) {
    $perm_data = mysqli_fetch_assoc($perm_res);
    $is_approved = ($perm_data['manager_request'] == 'Accepted');
}
?>

<aside id="managerSidebar" class="fixed lg:static inset-y-0 left-0 w-72 bg-white h-screen border-r border-slate-100 p-6 flex flex-col z-[1000] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto shadow-2xl lg:shadow-none">
    
    <button onclick="toggleSidebar()" class="lg:hidden absolute top-6 right-6 text-slate-400 hover:text-orange-500  transition-all">
        <i class="fas fa-times text-2xl"></i>
    </button>

    <div class="mb-10 px-2 flex items-center gap-3">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-orange-50 shadow-sm border border-orange-100 flex-shrink-0">
           <img src="../images/favicon.png" alt="Logo" class="w-8 h-8 object-cover">
        </div>
        <div class="min-w-0">
            <h1 class="text-xl font-black text-slate-900 tracking-tighter italic leading-none truncate">CRIC<span class="text-orange-500">STORM</span></h1>
            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">Manager Workspace</p>
        </div>
    </div>

    <nav class="space-y-1.5 flex-grow">
        <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 px-4">Main Menu</p>
        <a href="dashboard.php" class="flex items-center gap-4 px-6 py-3.5 text-sm font-bold transition-all <?php echo ($current_page == 'dashboard.php') ? 'active-link' : 'text-slate-400'; ?>">
            <i class="fas fa-th-large w-5"></i> <span>Dashboard</span>
        </a>
        <a href="all_tournaments.php" class="flex items-center gap-4 px-6 py-3.5 text-sm font-bold transition-all <?php echo ($current_page == 'all_tournaments.php') ? 'active-link' : 'text-slate-400'; ?>">
            <i class="fas fa-trophy w-5"></i> <span>My Tournaments</span>
        </a>
        <a href="manage_teams.php" class="flex items-center gap-4 px-6 py-3.5 text-sm font-bold transition-all <?php echo ($current_page == 'manage_teams.php') ? 'active-link' : 'text-slate-400'; ?>">
            <i class="fas fa-shield-alt w-5"></i> <span>Manage Teams</span>
        </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-100">
        <a href="../logout.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-rose-500 hover:bg-rose-50 active:scale-95 transition-all">
            <i class="fas fa-power-off"></i> <span>Sign Out</span>
        </a>
    </div>
</aside>

<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[900] hidden lg:hidden transition-opacity"></div>

<style>
    .active-link { background-color: #fff7ed !important; color: #f97316 !important; border-radius: 1rem; box-shadow: inset 4px 0 0 #f97316; }
    .active-link i { color: #f97316; }
    
</style>