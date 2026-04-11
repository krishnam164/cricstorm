<?php
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// 2. FILTER LOGIC
$limit = 6; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;
$filter_tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;

$base_where = "WHERE am.user_id = '$user_id'";
if ($filter_tid > 0) {
    $base_where .= " AND t.tournament_id = '$filter_tid'";
}

$total_sql = "SELECT COUNT(t.team_id) as total 
              FROM team_master t 
              JOIN auction_master am ON t.tournament_id = am.tournament_id 
              $base_where";
$total_res = mysqli_query($conn, $total_sql);
$total_records = mysqli_fetch_assoc($total_res)['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

if($page > $total_pages && $total_pages > 0) { $page = $total_pages; $start = ($page - 1) * $limit; }

include 'includes/header.php';
?>

<div class="mb-8 md:mb-10 flex flex-col md:flex-row justify-between items-center md:items-end gap-6 px-2 md:px-0">
    <div class="text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic uppercase leading-none tracking-tighter">
            Franchise <span class="text-orange-500">Management</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] md:tracking-[0.3em] mt-2">
            Managing teams for your assigned tournaments
        </p>
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
       <select onchange="location.href='manage_teams.php?tid=' + this.value" class="w-full sm:w-auto bg-white border border-slate-100 rounded-xl md:rounded-2xl px-5 py-3.5 md:py-4 font-bold text-xs text-slate-600 shadow-sm outline-none cursor-pointer appearance-none">
            <option value="0">All My Tournaments</option>
            <?php
            $t_sql = "SELECT tm.tournament_id, tm.tournament_name 
                    FROM tournament_master tm 
                    JOIN auction_master am ON tm.tournament_id = am.tournament_id 
                    WHERE am.user_id = '$user_id' 
                    GROUP BY tm.tournament_id 
                    ORDER BY tm.tournament_name ASC";
            $t_res = mysqli_query($conn, $t_sql);
            while($t = mysqli_fetch_assoc($t_res)) {
                $sel = ($filter_tid == $t['tournament_id']) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>
        <a href="add_team.php" class="w-full sm:w-auto text-center bg-slate-900 text-white px-8 py-4 rounded-xl md:rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-500 transition-all shadow-lg active:scale-95">
            + New Team
        </a>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-2 md:px-0">
    <?php
    $sql = "SELECT t.*, tm.tournament_name 
            FROM team_master t 
            JOIN tournament_master tm ON t.tournament_id = tm.tournament_id 
            JOIN auction_master am ON t.tournament_id = am.tournament_id
            $base_where 
            ORDER BY t.team_id DESC 
            LIMIT $start, $limit";
    
    $res = mysqli_query($conn, $sql);
    if(mysqli_num_rows($res) > 0):
        while($row = mysqli_fetch_assoc($res)):
            $t_id = $row['team_id'];
            $logo_path = !empty($row['team_logo']) ? '../'.$row['team_logo'] : '../images/default_team.png';
            $draft_sql = "SELECT COUNT(*) as p_count, SUM(points) as spent FROM auction_tracking WHERE sold_team = '$t_id' AND is_sold = 1";
            $draft_data = mysqli_fetch_assoc(mysqli_query($conn, $draft_sql));
    ?>
    <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] border border-slate-100 overflow-hidden hover:shadow-2xl transition-all duration-500 group">
        <div class="relative h-36 md:h-40 bg-slate-50 flex items-center justify-center">
            <img src="<?php echo $logo_path; ?>" class="w-20 h-20 md:w-24 md:h-24 object-contain group-hover:scale-110 transition-transform">
            <div class="absolute top-4 right-4 md:right-6 bg-orange-500 text-white px-3 py-1 rounded-full text-[8px] md:text-[9px] font-black italic shadow-lg">
                <?php echo $draft_data['p_count'] ?? 0; ?> PLAYERS
            </div>
        </div>

        <div class="p-5 md:p-6">
            <span class="text-[7px] md:text-[8px] font-black text-orange-500 uppercase tracking-widest bg-orange-50 px-2 py-1 rounded-md border border-orange-100 truncate inline-block max-w-full">
                <?php echo $row['tournament_name']; ?>
            </span>
            <h3 class="text-lg md:text-xl font-black text-slate-900 mt-3 mb-1 uppercase italic leading-tight truncate"><?php echo $row['name']; ?></h3>
            <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Spent: <span class="text-slate-900">₹<?php echo number_format($draft_data['spent'] ?? 0); ?></span></p>

            <button onclick="viewFullSquad(<?php echo $t_id; ?>, '<?php echo addslashes($row['name']); ?>')" 
                    class="w-full bg-slate-50 hover:bg-orange-50 border border-slate-100 p-3 rounded-xl flex items-center justify-between group/btn mb-6 transition-all active:scale-[0.98]">
                <span class="text-[8px] md:text-[9px] font-black text-slate-600 uppercase tracking-widest">View Full Squad</span>
                <i class="fas fa-users text-slate-300 group-hover/btn:text-orange-500 transition-colors"></i>
            </button>

            <div class="flex gap-2">
                <a href="edit_team.php?id=<?php echo $t_id; ?>" class="flex-grow bg-slate-900 text-white py-3.5 rounded-xl font-black text-[9px] uppercase tracking-widest text-center hover:bg-orange-500 transition-all active:scale-95">
                    Edit
                </a>
                <button onclick="confirmDelete(<?php echo $t_id; ?>)" class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all active:scale-90">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endwhile; else: ?>
        <div class="col-span-full py-20 text-center bg-slate-50 rounded-[2rem] md:rounded-[3rem] border border-dashed border-slate-200 mx-2">
            <i class="fas fa-shield-slash text-3xl text-slate-200 mb-4"></i>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No Teams assigned to your profile</p>
        </div>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1): ?>
<div class="mt-12 mb-10 flex flex-col items-center gap-6 px-4">
    <div class="flex items-center flex-wrap justify-center gap-2">
        <?php if($page > 1): ?>
            <a href="?page=<?php echo $page-1; ?>&tid=<?php echo $filter_tid; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 shadow-sm active:scale-90"><i class="fas fa-chevron-left text-xs"></i></a>
        <?php endif; ?>

        <?php 
        for($i = 1; $i <= $total_pages; $i++): 
            if($i == 1 || $i == $total_pages || ($i >= $page - 1 && $i <= $page + 1)):
        ?>
            <a href="?page=<?php echo $i; ?>&tid=<?php echo $filter_tid; ?>" class="px-4 py-2 min-w-[40px] text-center rounded-xl text-xs font-black transition-all shadow-sm border <?php echo ($page == $i) ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-slate-400 border-slate-100'; ?>">
                <?php echo $i; ?>
            </a>
        <?php elseif($i == $page - 2 || $i == $page + 2): echo '<span class="text-slate-300">...</span>'; endif; endfor; ?>

        <?php if($page < $total_pages): ?>
            <a href="?page=<?php echo $page+1; ?>&tid=<?php echo $filter_tid; ?>" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-100 text-slate-400 shadow-sm active:scale-90"><i class="fas fa-chevron-right text-xs"></i></a>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-3 bg-white p-1.5 rounded-2xl border border-slate-100 shadow-sm">
        <input type="number" id="pageJumper" min="1" max="<?php echo $total_pages; ?>" value="<?php echo $page; ?>" 
               class="w-12 py-1.5 text-center bg-slate-50 border border-slate-100 rounded-lg text-xs font-black outline-none">
        <button onclick="jumpToPage()" class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest active:scale-95">Jump</button>
    </div>
</div>
<?php endif; ?>

<script>
function jumpToPage() {
    const val = document.getElementById('pageJumper').value;
    if (val > 0 && val <= <?php echo $total_pages; ?>) {
        window.location.href = `manage_teams.php?page=${val}&tid=<?php echo $filter_tid; ?>`;
    }
}
function viewFullSquad(teamId, teamName) {
    const modal = document.getElementById('playerModal');
    const content = document.getElementById('modalContent');
    const title = document.getElementById('modalTitle');
    title.innerText = teamName + " Squad";
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="flex justify-center p-10"><i class="fas fa-circle-notch fa-spin text-orange-500 text-2xl"></i></div>';
    fetch(`get_team_squad.php?team_id=${teamId}`).then(r => r.text()).then(d => { content.innerHTML = d; });
}
function closeModal() { document.getElementById('playerModal').classList.add('hidden'); }
function confirmDelete(id) { if(confirm('Release all players and delete team?')) window.location.href = 'manage_teams.php?delete_id=' + id; }
window.onclick = function(e) { if (e.target == document.getElementById('playerModal')) closeModal(); }
</script>

<?php include 'includes/footer.php'; ?>