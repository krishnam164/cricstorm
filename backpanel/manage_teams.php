<?php
include '../config.php';

// 1. SECURITY GATE
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
    header("Location: ../login.php");
    exit();
}

// 2. DELETE LOGIC
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    // Get logo to delete file from server
    $img_res = mysqli_query($conn, "SELECT logo FROM team_master WHERE team_id = '$id'");
    $img_data = mysqli_fetch_assoc($img_res);
    if($img_data['logo'] != 'default.png') {
        @unlink('../uploads/teams/' . $img_data['logo']);
    }
    
    mysqli_query($conn, "DELETE FROM team_master WHERE team_id = '$id'");
    header("Location: manage_teams.php?msg=deleted");
    exit();
}

// 3. FILTER LOGIC
$filter_tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;

$active_page = 'manage_teams';
include 'includes/header.php';
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic uppercase">Team <span class="text-orange-600">Franchises</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-1">Manage Squad Limits & Branding</p>
    </div>

    <div class="flex items-center gap-4">
        <select onchange="location.href='manage_teams.php?tid=' + this.value" class="bg-slate-100 border-none rounded-2xl px-6 py-4 font-bold text-slate-700 focus:ring-2 focus:ring-orange-500 shadow-sm">
            <option value="0">All Tournaments</option>
            <?php
            $t_res = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_id DESC");
            while($t = mysqli_fetch_assoc($t_res)) {
                $sel = ($filter_tid == $t['tournament_id']) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>

        <a href="add_team.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200">
            <i class="fas fa-plus mr-2"></i> Add Team
        </a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="mb-8 p-4 bg-teal-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest animate-pulse">
        Database Updated Successfully
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php
    $sql = "SELECT t.*, tm.tournament_name 
            FROM team_master t 
            LEFT JOIN tournament_master tm ON t.tournament_id = tm.tournament_id";
    if($filter_tid > 0) {
        $sql .= " WHERE t.tournament_id = '$filter_tid'";
    }
    $sql .= " ORDER BY t.team_id DESC";
    
    $res = mysqli_query($conn, $sql);
    if(mysqli_num_rows($res) > 0):
        while($row = mysqli_fetch_assoc($res)):
            $logo_path = !empty($row['logo']) ? '../uploads/teams/'.$row['logo'] : '../uploads/teams/default.png';
    ?>
    <div class="bg-white rounded-[3.5rem] border border-slate-100 p-8 shadow-sm hover:shadow-2xl transition-all group relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-32 h-32 bg-slate-50 rounded-full group-hover:bg-orange-50 transition-colors"></div>
        
        <div class="relative z-10 flex flex-col items-center text-center">
            <div class="w-28 h-28 rounded-3xl overflow-hidden bg-white shadow-inner border border-slate-50 mb-6 p-4">
                <img src="<?php echo $logo_path; ?>" class="w-full h-full object-contain" onerror="this.onerror=null;this.src='../uploads/teams/default.png';" alt="<?php echo $row['name']; ?> Logo">
            </div>

            <span class="text-[9px] font-black text-orange-500 uppercase tracking-widest mb-1"><?php echo $row['tournament_name']; ?></span>
            <h3 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter mb-6"><?php echo $row['name']; ?></h3>

            <div class="grid grid-cols-2 gap-4 w-full mb-8">
                <div class="bg-slate-50 rounded-2xl p-4">
                    <p class="text-[8px] font-bold text-slate-400 uppercase">Points Limit</p>
                    <p class="font-black text-slate-800 italic">₹<?php echo number_format($row['total_purse'] ?? 1000000); ?></p>
                </div>
                <div class="bg-slate-50 rounded-2xl p-4">
                    <p class="text-[8px] font-bold text-slate-400 uppercase">Max Squad</p>
                    <p class="font-black text-slate-800 italic"><?php echo $row['max_players'] ?? '15'; ?></p>
                </div>
            </div>

            <div class="flex gap-2 w-full">
                <a href="edit_team.php?id=<?php echo $row['team_id']; ?>" class="flex-grow bg-slate-900 text-white py-4 rounded-2xl font-black text-[9px] uppercase tracking-widest hover:bg-orange-500 transition-all">
                    Edit Info
                </a>
                <button onclick="confirmDelete(<?php echo $row['team_id']; ?>)" class="bg-rose-50 text-rose-500 px-6 py-4 rounded-2xl hover:bg-rose-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endwhile; else: ?>
    <div class="col-span-full py-20 text-center bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
        <i class="fas fa-shield-alt text-4xl text-slate-200 mb-4"></i>
        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Teams Found for this selection</p>
    </div>
    <?php endif; ?>
</div>

<script>
function confirmDelete(id) {
    if(confirm('Are you sure? Deleting a team will remove all their auction records!')) {
        window.location.href = 'manage_teams.php?delete_id=' + id;
    }
}
</script>

<?php include 'includes/footer.php'; ?>