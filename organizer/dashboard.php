<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'organizer') {
    header("Location: ../login.php");
    exit();
}

include 'includes/header.php'; 

// 1. FETCH METRICS FOR ORGANIZER
// Count tournaments created by this organizer
$t_res = mysqli_query($conn, "SELECT COUNT(tournament_id) as total FROM tournament_master WHERE user_id = '$user_id'");
$t_count = ($t_res) ? mysqli_fetch_assoc($t_res)['total'] : 0;

// Count total players registered in the global system
$p_res = mysqli_query($conn, "SELECT COUNT(player_id) as total FROM player_master");
$p_count = ($p_res) ? mysqli_fetch_assoc($p_res)['total'] : 0;

// Count teams specifically linked to this organizer's latest tournament
$team_res = mysqli_query($conn, "SELECT COUNT(team_id) as total FROM team_master");
$team_count = ($team_res) ? mysqli_fetch_assoc($team_res)['total'] : 0;
?>

<div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic tracking-tight uppercase">
            Organizer <span class="text-orange-500">Dashboard</span>
        </h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Session Active: <?php echo $user_name; ?></p>
    </div>
    <a href="../live_broadcast.php" target="_blank" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest flex items-center gap-3 hover:bg-orange-600 transition-all shadow-xl shadow-slate-200">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
        </span>
        Live Broadcast
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
    <div class="bg-white p-10 rounded-[3rem] border border-orange-50 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full transition-transform group-hover:scale-150"></div>
        <i class="fas fa-trophy text-2xl text-orange-500 mb-6 relative"></i>
        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative">My Leagues</h4>
        <p class="text-4xl font-black text-slate-900 relative"><?php echo number_format($t_count); ?></p>
    </div>

    <div class="bg-white p-10 rounded-[3rem] border border-orange-50 shadow-sm relative overflow-hidden group">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full transition-transform group-hover:scale-150"></div>
        <i class="fas fa-user-friends text-2xl text-blue-500 mb-6 relative"></i>
        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative">Global Players</h4>
        <p class="text-4xl font-black text-slate-900 relative"><?php echo number_format($p_count); ?></p>
    </div>

    <div class="bg-[#0F172A] p-10 rounded-[3rem] text-white shadow-2xl relative overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full"></div>
        <i class="fas fa-shield-alt text-2xl text-orange-400 mb-6 relative"></i>
        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 relative">Total Teams</h4>
        <p class="text-4xl font-black text-white relative"><?php echo number_format($team_count); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <div class="lg:col-span-2 bg-white rounded-[3.5rem] border border-orange-50 shadow-sm overflow-hidden">
        <div class="p-10 border-b border-orange-50 flex justify-between items-center bg-orange-50/10">
            <h3 class="font-bold text-slate-800 italic uppercase text-xs tracking-widest">Recent Activity</h3>
            <a href="manage_tournaments.php" class="text-[9px] font-black text-orange-500 uppercase tracking-widest hover:underline">View All</a>
        </div>
        <div class="p-2 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-5">League Name</th>
                        <th class="px-8 py-5">Date Started</th>
                        <th class="px-8 py-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-orange-50">
                    <?php 
                    $recent_t = mysqli_query($conn, "SELECT * FROM tournament_master WHERE user_id = '$user_id' ORDER BY tournament_id DESC LIMIT 5");
                    if($recent_t && mysqli_num_rows($recent_t) > 0):
                        while($row = mysqli_fetch_assoc($recent_t)): 
                    ?>
                    <tr class="hover:bg-orange-50/30 transition-all group">
                        <td class="px-8 py-6 font-bold text-slate-700 text-sm group-hover:text-orange-600 transition-colors"><?php echo $row['tournament_name']; ?></td>
                        <td class="px-8 py-6 text-xs text-slate-500"><?php echo date('d M, Y', strtotime($row['tournament_date'])); ?></td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-4 py-1.5 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase">Active</span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="3" class="p-10 text-center text-slate-400 text-xs font-bold uppercase italic">No tournaments found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-orange-500 p-10 rounded-[3.5rem] text-white shadow-xl shadow-orange-500/20 self-start relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10">
            <i class="fas fa-plus-circle text-9xl"></i>
        </div>
        <i class="fas fa-plus-circle text-3xl mb-6 relative border-2 border-white/20 w-16 h-16 flex items-center justify-center rounded-2xl"></i>
        <h4 class="text-xl font-black leading-tight mb-6 tracking-tighter italic relative">Launch New <br>Competition</h4>
        <p class="text-[11px] text-orange-100 mb-8 font-medium leading-relaxed relative">Setup a new tournament, register teams, and start your live auction session.</p>
        <a href="add_tournament.php" class="relative inline-block bg-white text-orange-600 px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">
            Create League
        </a>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>