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
    $img_res = mysqli_query($conn, "SELECT team_logo FROM team_master WHERE team_id = '$id'");
    $img_data = mysqli_fetch_assoc($img_res);
    if(!empty($img_data['team_logo'])) { @unlink('../' . $img_data['team_logo']); }
    mysqli_query($conn, "DELETE FROM team_master WHERE team_id = '$id'");
    header("Location: manage_teams.php?msg=deleted");
    exit();
}

// 3. PAGINATION & FILTER LOGIC
$limit = 6; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;
$filter_tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
$where_clause = ($filter_tid > 0) ? "WHERE t.tournament_id = '$filter_tid'" : "";

$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM team_master t $where_clause");
$total_data = mysqli_fetch_assoc($total_res);
$total_records = $total_data['total'];
$total_pages = ceil($total_records / $limit);

if($page > $total_pages && $total_pages > 0) { $page = $total_pages; $start = ($page - 1) * $limit; }

$active_page = 'manage_teams';
include 'includes/header.php';
?>

<div class="hidden sm:flex items-center gap-2 mb-4 px-2">
    <a href="dashboard.php" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-teal-500 transition-colors">Dashboard</a>
    <i class="fas fa-chevron-right text-[7px] text-slate-300"></i>
    <span class="text-[10px] font-bold text-teal-500 uppercase tracking-widest">Team Franchises</span>
</div>

<div class="mb-8 md:mb-10 flex flex-col md:flex-row justify-between items-center md:items-end gap-6 px-2">
    <div class="text-center md:text-left">
        <h2 class="text-3xl md:text-4xl font-black text-slate-900 italic uppercase leading-none tracking-tighter">
            Franchise <span class="text-teal-500">Hub</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] md:tracking-[0.4em] mt-3">
            Page <?php echo $page; ?> of <?php echo $total_pages; ?> — <?php echo $total_records; ?> Total Teams
        </p>
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
        <select onchange="location.href='manage_teams.php?tid=' + this.value" class="w-full sm:w-auto bg-white border border-slate-100 rounded-xl md:rounded-2xl px-5 py-3.5 md:py-4 font-bold text-slate-600 shadow-sm outline-none appearance-none cursor-pointer text-sm">
            <option value="0">All Tournaments</option>
            <?php
            $t_res = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_id DESC");
            while($t = mysqli_fetch_assoc($t_res)) {
                $sel = ($filter_tid == $t['tournament_id']) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>

        <a href="add_team.php" class="w-full sm:w-auto bg-slate-900 text-white px-8 py-4 rounded-xl md:rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-teal-500 transition-all shadow-xl flex items-center justify-center">
            <i class="fas fa-plus mr-2 text-[8px]"></i> Add Team
        </a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="mx-2 mb-8 p-4 bg-teal-500 text-white rounded-xl md:rounded-2xl text-[9px] md:text-[10px] font-black uppercase tracking-widest animate__animated animate__fadeIn flex items-center">
        <i class="fas fa-check-circle mr-3 text-sm"></i>
        Action successfully processed
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-2 md:px-0">
    <?php
    $sql = "SELECT t.*, tm.tournament_name FROM team_master t LEFT JOIN tournament_master tm ON t.tournament_id = tm.tournament_id $where_clause ORDER BY t.team_id DESC LIMIT $start, $limit";
    $res = mysqli_query($conn, $sql);
    if(mysqli_num_rows($res) > 0):
        while($row = mysqli_fetch_assoc($res)):
            $t_id = $row['team_id'];
            $logo_path = !empty($row['team_logo']) ? '../'.$row['team_logo'] : '../images/default_team.png';

            $draft_sql = "SELECT COUNT(*) as player_count, SUM(points) as total_spent FROM auction_tracking WHERE sold_team = '$t_id' AND is_sold = 1";
            $draft_res = mysqli_query($conn, $draft_sql);
            $draft_data = mysqli_fetch_assoc($draft_res);
            $p_count = $draft_data['player_count'] ?? 0;
            $spent = $draft_data['total_spent'] ?? 0;
        ?>
        <div class="bg-white rounded-[2rem] md:rounded-[3rem] border border-slate-100 overflow-hidden hover:shadow-2xl transition-all duration-500 group">
            <div class="relative h-40 md:h-44 bg-slate-50 flex items-center justify-center">
                <img src="<?php echo $logo_path; ?>" class="relative z-10 w-24 h-24 md:w-28 md:h-28 object-contain transition-transform group-hover:scale-110" alt="Team Logo">
                
                <div class="absolute top-4 right-4 md:top-6 md:right-6 bg-slate-900 text-white px-3 py-1.5 md:px-4 md:py-2 rounded-xl md:rounded-2xl text-[8px] md:text-[10px] font-black z-20 shadow-lg uppercase">
                    <?php echo $p_count; ?> Players
                </div>

                <div class="absolute bottom-4 left-6 opacity-5 group-hover:opacity-10 transition-all duration-500 transform">
                     <span class="text-4xl md:text-5xl font-black text-slate-900 italic"><?php echo $row['short_name']; ?></span>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <span class="text-[8px] md:text-[9px] font-black text-teal-500 uppercase tracking-widest bg-teal-50 px-3 py-1 rounded-full border border-teal-100"><?php echo $row['tournament_name']; ?></span>
                <h3 class="text-xl md:text-2xl font-black text-slate-900 mt-4 mb-1 uppercase italic leading-tight truncate"><?php echo $row['name']; ?></h3>
                
                <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6">Total Spent: <span class="text-slate-900 font-black">₹<?php echo number_format($spent); ?></span></p>

                <div class="mb-6 md:mb-8">
                    <button onclick="viewFullSquad(<?php echo $t_id; ?>, '<?php echo addslashes($row['name']); ?>')" 
                            class="w-full bg-slate-50 border border-slate-100 hover:border-teal-200 hover:bg-teal-50 transition-all p-4 rounded-xl md:rounded-2xl flex items-center justify-between group/btn active:scale-[0.98]">
                        <div class="flex flex-col items-start min-w-0">
                            <p class="text-[7px] md:text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Squad Monitor</p>
                            <p class="text-[10px] md:text-[11px] font-black text-slate-700 uppercase italic truncate w-full"><?php echo $p_count; ?> Players Drafted</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex shrink-0 items-center justify-center text-slate-400 group-hover/btn:text-teal-500 transition-all shadow-sm">
                            <i class="fas fa-users text-xs"></i>
                        </div>
                    </button>
                </div>

                <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-50 gap-2">
                    <div class="min-w-0">
                        <p class="text-[7px] md:text-[8px] font-bold text-slate-400 uppercase mb-0.5">Owner</p>
                        <p class="text-xs md:text-sm font-bold text-slate-700 truncate"><?php echo $row['owner_name']; ?></p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-[7px] md:text-[8px] font-bold text-slate-400 uppercase mb-0.5">Mobile</p>
                        <p class="text-xs md:text-sm font-bold text-slate-700"><?php echo $row['mobile_no']; ?></p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="edit_team.php?id=<?php echo $row['team_id']; ?>" class="flex-grow bg-slate-100 text-slate-700 py-3.5 md:py-4 rounded-xl md:rounded-2xl font-black text-[9px] md:text-[10px] uppercase tracking-widest text-center hover:bg-slate-900 hover:text-white transition-all active:scale-95">
                        Edit Profile
                    </a>
                    <button onclick="confirmDelete(<?php echo $row['team_id']; ?>)" class="w-12 h-12 md:w-14 md:h-14 flex items-center justify-center rounded-xl md:rounded-2xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-90">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-span-full py-16 md:py-24 text-center bg-slate-50 rounded-[2rem] md:rounded-[4rem] border-2 border-dashed border-slate-200 mx-2">
            <i class="fas fa-users-slash text-3xl text-slate-200 mb-4"></i>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4">No Teams matching this filter</p>
        </div>
    <?php endif; ?>
</div>

<?php if($total_pages > 1): ?>
<div class="mt-12 md:mt-16 mb-10 flex flex-col md:flex-row items-center justify-center gap-6 md:gap-8 px-4">
    <div class="flex items-center flex-wrap justify-center gap-2">
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="manage_teams.php?page=<?php echo $i; ?>&tid=<?php echo $filter_tid; ?>" 
               class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-lg md:rounded-xl font-black text-xs transition-all 
               <?php echo ($page == $i) ? 'bg-teal-500 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <div class="flex items-center bg-white border border-slate-200 p-1 rounded-xl md:rounded-2xl shadow-sm">
        <input type="number" id="pageJumper" min="1" max="<?php echo $total_pages; ?>" placeholder="Pg" 
               class="w-12 md:w-16 px-3 py-2 text-xs font-black text-slate-800 outline-none bg-transparent">
        <button onclick="jumpToPage()" class="bg-slate-900 text-white px-4 py-2 rounded-lg md:rounded-xl text-[8px] md:text-[9px] font-black uppercase tracking-widest active:scale-95">
            Jump
        </button>
    </div>
</div>
<?php endif; ?>

<div id="playerModal" class="fixed inset-0 z-[150] hidden flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-[1.5rem] md:rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">
        <div class="p-5 md:p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h3 id="modalTitle" class="text-xs md:text-sm font-black text-slate-800 uppercase italic tracking-tighter truncate pr-4">Team Squad</h3>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:text-red-500 active:scale-90 transition-all shrink-0">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div id="modalContent" class="p-4 md:p-6 max-h-[65vh] overflow-y-auto scrollbar-hide">
            <div class="flex justify-center p-10"><i class="fas fa-circle-notch fa-spin text-teal-500 text-2xl"></i></div>
        </div>
    </div>
</div>

<script>
function jumpToPage() {
    const val = document.getElementById('pageJumper').value;
    const maxPages = <?php echo $total_pages; ?>;
    const t_id = <?php echo $filter_tid; ?>;
    if (val > 0 && val <= maxPages) { window.location.href = `manage_teams.php?page=${val}&tid=${t_id}`; }
}
function viewFullSquad(teamId, teamName) {
    const modal = document.getElementById('playerModal');
    const content = document.getElementById('modalContent');
    const title = document.getElementById('modalTitle');
    title.innerText = teamName + " Squad";
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    fetch(`get_team_squad.php?team_id=${teamId}`).then(r => r.text()).then(d => { content.innerHTML = d; });
}
function closeModal() { document.getElementById('playerModal').classList.add('hidden'); document.body.style.overflow = 'auto'; }
function confirmDelete(id) { if(confirm('Delete Team?')) { window.location.href = 'manage_teams.php?delete_id=' + id; } }
window.onclick = function(e) { if (e.target == document.getElementById('playerModal')) closeModal(); }
</script>

<?php include 'includes/footer.php'; ?>