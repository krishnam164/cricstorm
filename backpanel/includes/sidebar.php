<?php $active_page = basename($_SERVER['PHP_SELF']); ?>
<aside class="w-72 bg-[#0F172A] flex flex-col p-6 hidden lg:flex">
    <div class="mb-10 px-2 flex items-center gap-3">
        <div class="w-12 h-12 rounded-xl overflow-hidden border border-white/20">
            <img src="../images/favicon.png" class="w-full h-full object-cover">
        </div>
        <div>
            <h1 class="text-xl font-black text-white leading-tight">Cric<span class="text-[#14B8A6]">Strome</span></h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Super Admin Panel</p>
        </div>
    </div>

    <nav class="space-y-1 flex-grow">
        <div class="pb-2 px-6">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Core Monitoring</p>
        </div>
        <a href="dashboard.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'dashboard.php') ? 'active-link' : 'text-slate-400'; ?>">
            <i class="fas fa-chart-line"></i> Live Analytics
        </a>
        
        <a href="all_tournaments.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'all_tournaments.php') ? 'active-link' : 'text-slate-400'; ?> ">
            <i class="fas fa-trophy"></i> Tournaments
        </a>
        <a href="auction_monitor.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'auction_monitor.php' ? 'active-link' : 'text-slate-400') ?>">
            <i class="fas fa-gavel"></i> Global Auctions
        </a>
          <a href="auction_controller.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'auction_controller.php' ? 'active-link' : 'text-slate-400') ?>">
            <i class="fas fa-cogs"></i> auction controller
        </a>

        <div class="pt-6 pb-2 px-6">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">Data Masters</p>
        </div>
        <a href="manage_staff.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'manage_staff.php' ? 'active-link' : 'text-slate-400') ?>">
            <i class="fas fa-user-shield"></i> Staff Control
        </a>
        <a href="player_directory.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'player_directory.php' ? 'active-link' : 'text-slate-400') ?>">
            <i class="fas fa-id-card"></i> Player Database
        </a>
        
         <a href="manage_teams.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'manage_teams.php' ? 'active-link' : 'text-slate-400') ?>">
            <i class="fas fa-users"></i> Team Management
        </a>

        <a href="requests.php" class="sidebar-item <?php echo ($active_page == 'requests.php') ? 'active-link' : 'text-slate-400'; ?> flex items-center justify-between px-6 py-4 rounded-2xl text-sm font-bold transition-all">
            <div class="flex items-center gap-4">
                <i class="fas fa-user-shield"></i> Access Requests
            </div>
        </a>

        <div class="pt-6 pb-2 px-6">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em]">System</p>
        </div>
        <a href="settings.php" class="sidebar-item flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-slate-400 hover:text-white hover:bg-white/5 transition-all <?php echo ($active_page == 'settings.php' ? 'active-link' : 'text-slate-400') ?>">
            <i class="fas fa-sliders-h"></i> Platform Settings
        </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-white/10">
        <a href="logout.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-red-400 hover:bg-red-400/10 transition-all">
            <i class="fas fa-power-off"></i> logout
        </a>
    </div>
</aside>

<style>
    /* Ensure the active-link stands out with a subtle glow */
    .active-link {
        background: rgba(20, 184, 166, 0.1);
        color: #01110f !important;
        box-shadow: inset 4px 0 0 #14B8A6;
    }
    
    /* Custom Sidebar Item Hover */
    .sidebar-item:hover i {
        color: #14B8A6;
    }
</style>