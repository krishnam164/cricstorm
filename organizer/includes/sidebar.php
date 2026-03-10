<?php
$current_page = basename($_SERVER['PHP_SELF']);

// SAFETY CHECK: Ensure the query is valid before fetching
$perm_res = mysqli_query($conn, "SELECT organizer_request FROM users WHERE id = '$user_id'");

$is_approved = false;
if ($perm_res && mysqli_num_rows($perm_res) > 0) {
    $perm_data = mysqli_fetch_assoc($perm_res);
    $is_approved = ($perm_data['organizer_request'] == 'Accepted');
}
?>

<aside class="w-72 bg-white h-screen sticky top-0 border-r border-slate-100 p-6 flex flex-col hidden lg:flex">
    <div class="mb-12 px-2 flex items-center gap-3">
        <div class="w-14 h-14  rounded-xl flex items-center justify-center ">
           <img src="../images/favicon.png" alt="Logo" class="w-10 h-10 object-cover">
        </div>
        <h1 class="text-xl font-black text-slate-900 tracking-tighter italic">CRIC<span class="text-orange-500">STROME</span></h1>
    </div>

    <nav class="space-y-2 flex-grow">
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 px-4">Menu</p>
        
        <a href="dashboard.php" class="flex items-center gap-4 px-6 py-4 text-sm font-bold transition-all <?php echo ($current_page == 'dashboard.php') ? 'active-link' : 'text-slate-400 hover:text-slate-600'; ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>

        <a href="all_tournaments.php" class="flex items-center gap-4 px-6 py-4 text-sm font-bold transition-all <?php echo ($current_page == 'all_tournaments.php') ? 'active-link' : 'text-slate-400 hover:text-slate-600'; ?>">
            <i class="fas fa-trophy"></i> My Tournaments
        </a>

        <a href="request_access.php" class="flex items-center justify-between px-6 py-4 text-sm font-bold transition-all <?php echo ($current_page == 'request_access.php') ? 'active-link' : 'text-slate-400 hover:text-slate-600'; ?>">
            <div class="flex items-center gap-4">
                <i class="fas fa-shield-halved"></i> Global Monitor
            </div>
            <?php if($is_approved): ?>
                <span class="w-2 h-2 bg-green-500 rounded-full shadow-[0_0_10px_#22c55e]"></span>
            <?php endif; ?>
        </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-100">
        <a href="../logout.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-rose-500 hover:bg-rose-50 transition-all">
            <i class="fas fa-power-off"></i> Sign Out
        </a>
    </div>
</aside>