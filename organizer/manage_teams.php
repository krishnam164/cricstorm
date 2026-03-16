<?php
session_start();
include '../config.php';

// 1. SECURITY GATE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'organizer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. HANDLE TEAM DELETION
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    // First, get logo name to delete file
    $img_res = mysqli_query($conn, "SELECT logo FROM team_master WHERE team_id = '$del_id'");
    $img_data = mysqli_fetch_assoc($img_res);
    
    if ($img_data['logo'] != 'default_team.png') {
        @unlink('../uploads/teams/' . $img_data['logo']);
    }

    mysqli_query($conn, "DELETE FROM team_master WHERE team_id = '$del_id'");
    header("Location: manage_teams.php?msg=deleted");
    exit();
}

// 3. TOURNAMENT FILTERING
$active_tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;

include 'includes/header.php'; 
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic uppercase">Manage <span class="text-orange-500">Teams</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-1">Squad Formation & Branding</p>
    </div>

    <div class="flex gap-4">
        <select onchange="location.href='manage_teams.php?tid=' + this.value" class="bg-slate-100 border-none rounded-2xl px-6 py-4 font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-orange-500">
            <option value="0">All Tournaments</option>
            <?php
            $t_list = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master WHERE user_id = '$user_id'");
            while($t = mysqli_fetch_assoc($t_list)) {
                $sel = ($active_tid == $t['tournament_id']) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>

        <a href="add_team.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
            <i class="fas fa-plus mr-2"></i> Add New Team
        </a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="mb-6 p-4 bg-green-50 text-green-600 rounded-2xl border border-green-100 text-xs font-bold uppercase tracking-widest animate-pulse">
        Action Completed Successfully!
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php
    $query = "SELECT t.*, tm.tournament_name 
              FROM team_master t 
              LEFT JOIN tournament_master tm ON t.tournament_id = tm.tournament_id 
              WHERE tm.user_id = '$user_id'";
    
    if ($active_tid > 0) {
        $query .= " AND t.tournament_id = '$active_tid'";
    }
    
    $res = mysqli_query($conn, $query);
    if(mysqli_num_rows($res) > 0):
        while($team = mysqli_fetch_assoc($res)):
            $logo = !empty($team['logo']) ? '../uploads/teams/'.$team['logo'] : '../uploads/teams/default_team.png';
    ?>
    <div class="bg-white rounded-[3.5rem] border border-slate-100 p-8 shadow-sm hover:shadow-xl transition-all group">
        <div class="flex flex-col items-center text-center">
            <div class="w-32 h-32 rounded-[2.5rem] overflow-hidden bg-slate-50 border-4 border-slate-50 mb-6 group-hover:scale-105 transition-transform">
                <img src="<?php echo $logo; ?>" class="w-full h-full object-contain p-4" onerror="this.onerror=null;this.src='../uploads/teams/default_team.png';">
            </div>

            <span class="text-[9px] font-black text-orange-500 uppercase tracking-[0.2em] mb-2"><?php echo $team['tournament_name']; ?></span>
            <h3 class="text-xl font-black text-slate-900 uppercase italic leading-tight mb-6"><?php echo $team['name']; ?></h3>
            
            <div class="grid grid-cols-2 w-full gap-4 mb-8">
                <div class="bg-slate-50 p-4 rounded-3xl">
                    <p class="text-[8px] font-bold text-slate-400 uppercase mb-1">Max Players</p>
                    <p class="font-black text-slate-700"><?php echo $team['max_players'] ?? '15'; ?></p>
                </div>
                <div class="bg-slate-50 p-4 rounded-3xl">
                    <p class="text-[8px] font-bold text-slate-400 uppercase mb-1">Total Purse</p>
                    <p class="font-black text-teal-600">₹<?php echo number_format($team['total_purse'] ?? 1000000); ?></p>
                </div>
            </div>

            <div class="flex gap-3 w-full">
                <a href="edit_team.php?id=<?php echo $team['team_id']; ?>" class="flex-grow bg-slate-100 text-slate-600 py-4 rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">
                    Edit Team
                </a>
                <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $team['team_id']; ?>)" class="bg-rose-50 text-rose-500 px-6 py-4 rounded-2xl hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
        </div>
    </div>
    <?php endwhile; else: ?>
        <div class="col-span-full bg-slate-50 p-20 rounded-[4rem] text-center border-2 border-dashed border-slate-200">
            <i class="fas fa-shield-alt text-5xl text-slate-200 mb-6"></i>
            <h3 class="text-xl font-black text-slate-400 uppercase italic">No Teams Registered Yet</h3>
            <p class="text-xs text-slate-400 mt-2">Start by adding your first team to this tournament.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
    if(confirm('Are you sure you want to delete this team? This will remove all their data from the current auction.')) {
        window.location.href = 'manage_teams.php?delete_id=' + id;
    }
}
</script>

<?php include 'includes/footer.php'; ?>