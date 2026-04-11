<?php $active_page = basename($_SERVER['PHP_SELF']); ?>

<aside id="main-sidebar" class="fixed lg:static inset-y-0 left-0 w-72 bg-[#0F172A] flex flex-col p-6 z-50 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto border-r border-white/5 shadow-2xl lg:shadow-none">
    
    <button onclick="toggleSidebar()" class="lg:hidden absolute top-6 right-6 text-slate-400 hover:text-white transition-colors">
        <i class="fas fa-times text-xl"></i>
    </button>

    <div class="mb-10 px-2 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl overflow-hidden border border-white/10 flex-shrink-0 shadow-lg bg-[#14B8A6]/10 flex items-center justify-center">
            <img src="../images/favicon.png" class="w-8 h-8 object-contain">
        </div>
        <div class="min-w-0">
            <h1 class="text-xl font-black text-white leading-none tracking-tighter truncate">CRIC<span class="text-[#14B8A6]">STORM</span></h1>
            <p class="text-[8px] font-bold text-slate-500 uppercase tracking-[0.2em] mt-1.5">Super Admin Control</p>
        </div>
    </div>

    <nav class="space-y-1.5 flex-grow">
        <div class="pb-2 px-6">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em]">Core Monitoring</p>
        </div>
        
        <a href="dashboard.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'dashboard.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-chart-line w-5"></i> Live Analytics
        </a>
        
        <a href="all_tournaments.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'all_tournaments.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-trophy w-5"></i> Tournaments
        </a>

        <a href="auction_controller.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'auction_controller.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-cogs w-5"></i> Auction Controller
        </a>

        <div class="pt-6 pb-2 px-6">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em]">Data Masters</p>
        </div>

        <a href="manage_staff.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'manage_staff.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-user-shield w-5"></i> Staff Control
        </a>

        <a href="player_directory.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'player_directory.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-id-card w-5"></i> Player Database
        </a>
        
        <a href="manage_teams.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'manage_teams.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-users w-5"></i> Team Management
        </a>

        <a href="requests.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'requests.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-envelope-open-text w-5"></i> Access Requests
        </a>

        <div class="pt-6 pb-2 px-6">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-[0.2em]">System</p>
        </div>

        <a href="settings.php" class="sidebar-item flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold transition-all <?php echo ($active_page == 'settings.php') ? 'active-link' : 'text-slate-400 hover:text-slate-200'; ?>">
            <i class="fas fa-sliders-h w-5"></i> Platform Settings
        </a>
    </nav>

    <div class="mt-auto pt-6 border-t border-white/5">
        <a href="logout.php" class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold text-rose-400 hover:bg-rose-400/10 transition-all uppercase tracking-widest">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>
</aside>

<style>
    /* Active Link: Teal gradient glow */
    .active-link {
        background: linear-gradient(90deg, rgba(20, 184, 166, 0.15) 0%, rgba(20, 184, 166, 0.05) 100%) !important;
        color: #14B8A6 !important;
        box-shadow: inset 4px 0 0 #14B8A6;
    }
    
    /* Hover effects for icons */
    .sidebar-item:hover i, .active-link i {
        color: #14B8A6;
    }

    /* JavaScript Toggle Classes */
    #main-sidebar.active {
        transform: translateX(0);
    }
</style>