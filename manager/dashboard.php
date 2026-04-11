<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_fullname'] ?? 'Manager';

// 1. Count assignments
$t_res = mysqli_query($conn, "SELECT COUNT(auction_id) as total FROM auction_master WHERE user_id = '$user_id'");
$t_count = ($t_res) ? mysqli_fetch_assoc($t_res)['total'] : 0;

// 2. Count players in those assignments
$p_res = mysqli_query($conn, "SELECT COUNT(p.player_id) as total 
                              FROM player_master p
                              JOIN auction_master am ON p.tournament_id = am.tournament_id
                              WHERE am.user_id = '$user_id'");
$p_count = ($p_res) ? mysqli_fetch_assoc($p_res)['total'] : 0;

// 3. Count teams in those assignments
$team_res = mysqli_query($conn, "SELECT COUNT(t.team_id) as total 
                                 FROM team_master t
                                 JOIN auction_master am ON t.tournament_id = am.tournament_id
                                 WHERE am.user_id = '$user_id'");
$team_count = ($team_res) ? mysqli_fetch_assoc($team_res)['total'] : 0;

include 'includes/header.php'; 
?>

<div class="mb-8 md:mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6 px-2 md:px-0">
    <div class="text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic tracking-tighter uppercase leading-none">
            Manager <span class="text-orange-500">Dashboard</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 mt-2 uppercase tracking-[0.3em] font-bold">Session Active: <?php echo $user_name; ?></p>
    </div>
    <a href="../live_broadcast.php" target="_blank" class="w-full md:w-auto bg-slate-900 text-white px-8 py-4 rounded-xl md:rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center justify-center gap-3 hover:bg-orange-600 transition-all shadow-xl active:scale-95">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
        </span>
        Live Broadcast
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-8 md:mb-12 px-2 md:px-0">
    <div class="bg-white p-8 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-orange-50 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-orange-50 rounded-full transition-transform group-hover:scale-150 opacity-50 md:opacity-100"></div>
        <i class="fas fa-trophy text-xl md:text-2xl text-orange-500 mb-6 relative"></i>
        <h4 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative">My Leagues</h4>
        <p class="text-3xl md:text-4xl font-black text-slate-900 relative"><?php echo number_format($t_count); ?></p>
    </div>

    <div class="bg-white p-8 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-orange-50 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full transition-transform group-hover:scale-150 opacity-50 md:opacity-100"></div>
        <i class="fas fa-user-friends text-xl md:text-2xl text-blue-500 mb-6 relative"></i>
        <h4 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative">My Players</h4>
        <p class="text-3xl md:text-4xl font-black text-slate-900 relative"><?php echo number_format($p_count); ?></p>
    </div>

    <div class="bg-[#0F172A] p-8 md:p-10 rounded-[2rem] md:rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full"></div>
        <i class="fas fa-shield-alt text-xl md:text-2xl text-orange-400 mb-6 relative"></i>
        <h4 class="text-[9px] md:text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 relative">Total Teams</h4>
        <p class="text-3xl md:text-4xl font-black text-white relative"><?php echo number_format($team_count); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 md:gap-10 px-2 md:px-0 pb-12">
    <div class="lg:col-span-2 bg-white rounded-[1.5rem] md:rounded-[3.5rem] border border-orange-50 shadow-sm overflow-hidden">
        <div class="p-6 md:p-10 border-b border-orange-50 flex justify-between items-center bg-orange-50/10">
            <h3 class="font-bold text-slate-800 italic uppercase text-[10px] md:text-xs tracking-widest leading-none">Recent Activity</h3>
            <a href="manage_tournaments.php" class="text-[8px] md:text-[9px] font-black text-orange-500 uppercase tracking-widest hover:underline">View All</a>
        </div>
        <div class="p-2 overflow-x-auto scrollbar-hide">
            <table class="w-full text-left min-w-[500px]">
                <thead>
                    <tr class="text-[8px] md:text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-6 md:px-8 py-4 md:py-5">League Name</th>
                        <th class="px-6 md:px-8 py-4 md:py-5">Date Started</th>
                        <th class="px-6 md:px-8 py-4 md:py-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-50">
                    <?php 
                    $recent_t = mysqli_query($conn, "SELECT * FROM tournament_master WHERE user_id = '$user_id' ORDER BY tournament_id DESC LIMIT 5");
                    if($recent_t && mysqli_num_rows($recent_t) > 0):
                        while($row = mysqli_fetch_assoc($recent_t)): 
                    ?>
                    <tr class="hover:bg-orange-50/30 transition-all group">
                        <td class="px-6 md:px-8 py-5 md:py-6 font-bold text-slate-700 text-xs md:text-sm group-hover:text-orange-600 transition-colors truncate max-w-[150px] md:max-w-none">
                            <?php echo $row['tournament_name']; ?>
                        </td>
                        <td class="px-6 md:px-8 py-5 md:py-6 text-[10px] md:text-xs text-slate-500">
                            <?php echo date('d M, Y', strtotime($row['tournament_date'])); ?>
                        </td>
                        <td class="px-6 md:px-8 py-5 md:py-6 text-center">
                            <span class="px-3 md:px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[8px] md:text-[9px] font-black uppercase">Active</span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="3" class="p-10 text-center text-slate-400 text-[10px] font-bold uppercase italic">No leagues managed yet</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-orange-500 p-8 md:p-10 rounded-[1.5rem] md:rounded-[3.5rem] text-white shadow-xl shadow-orange-500/20 self-start relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none">
            <i class="fas fa-plus-circle text-8xl md:text-9xl"></i>
        </div>
        <div class="w-12 h-12 md:w-16 md:h-16 flex items-center justify-center border-2 border-white/20 rounded-xl md:rounded-2xl mb-6 relative">
            <i class="fas fa-plus-circle text-2xl md:text-3xl"></i>
        </div>
        <h4 class="text-lg md:text-xl font-black leading-tight mb-4 tracking-tighter italic relative uppercase">Launch New <br>Competition</h4>
        <p class="text-[10px] md:text-[11px] text-orange-100 mb-8 font-medium leading-relaxed relative">Setup a new tournament, register teams, and start your live auction session today.</p>
        <a href="add_tournament.php" class="relative block w-full text-center md:inline-block md:w-auto bg-white text-orange-600 px-8 py-4 rounded-xl md:rounded-2xl font-black text-[9px] md:text-[10px] uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all active:scale-95">
            Create League
        </a>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>