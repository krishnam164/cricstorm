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
    
    // Fetch logo path to remove the physical file
    $img_res = mysqli_query($conn, "SELECT team_logo FROM team_master WHERE team_id = '$id'");
    $img_data = mysqli_fetch_assoc($img_res);
    
    if(!empty($img_data['team_logo'])) {
        // Path is stored as "uploads/tournaments/...", going up one level to root
        @unlink('../' . $img_data['team_logo']);
    }
    
    mysqli_query($conn, "DELETE FROM team_master WHERE team_id = '$id'");
    header("Location: manage_teams.php?msg=deleted");
    exit();
}

// 3. PAGINATION & FILTER LOGIC
$limit = 6; // Show 6 teams per page (2 rows of 3)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;
$filter_tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;

$where_clause = ($filter_tid > 0) ? "WHERE t.tournament_id = '$filter_tid'" : "";

// Get Total Records for Pagination math
$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM team_master t $where_clause");
$total_data = mysqli_fetch_assoc($total_res);
$total_records = $total_data['total'];
$total_pages = ceil($total_records / $limit);

$active_page = 'manage_teams';
include 'includes/header.php';
?>

<div class="flex items-center gap-2 mb-4">
    <a href="dashboard.php" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-teal-500 transition-colors">Dashboard</a>
    <i class="fas fa-chevron-right text-[7px] text-slate-300"></i>
    <span class="text-[10px] font-bold text-teal-500 uppercase tracking-widest">Team Franchises</span>
</div>

<div class="mb-10 flex flex-col md:flex-row justify-between items-end gap-6">
    <div>
        <h2 class="text-4xl font-black text-slate-900 italic uppercase leading-none">
            Franchise <span class="text-teal-500">Hub</span>
        </h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.4em] mt-3">
            Showing Page <?php echo $page; ?> of <?php echo $total_pages; ?> — <?php echo $total_records; ?> Total Teams
        </p>
    </div>

    <div class="flex items-center gap-3 w-full md:w-auto">
        <select onchange="location.href='manage_teams.php?tid=' + this.value" class="bg-white border border-slate-100 rounded-2xl px-6 py-4 font-bold text-slate-600 shadow-sm outline-none cursor-pointer hover:border-teal-200 transition-colors">
            <option value="0">All Tournaments</option>
            <?php
            $t_res = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_id DESC");
            while($t = mysqli_fetch_assoc($t_res)) {
                $sel = ($filter_tid == $t['tournament_id']) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>

        <a href="add_team.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-teal-500 transition-all shadow-xl flex items-center">
            <i class="fas fa-plus mr-2 text-[8px]"></i> Add Team
        </a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="mb-8 p-4 bg-teal-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest animate__animated animate__fadeIn flex items-center">
        <i class="fas fa-check-circle mr-3 text-sm"></i>
        Action successfully processed in database
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php
    $sql = "SELECT t.*, tm.tournament_name 
            FROM team_master t 
            LEFT JOIN tournament_master tm ON t.tournament_id = tm.tournament_id 
            $where_clause 
            ORDER BY t.team_id DESC 
            LIMIT $start, $limit";
    
    $res = mysqli_query($conn, $sql);
    if(mysqli_num_rows($res) > 0):
        /* --- PASTE THE NEW CODE STARTING FROM HERE --- */
        while($row = mysqli_fetch_assoc($res)):
            $t_id = $row['team_id'];
            $logo_path = !empty($row['team_logo']) ? '../'.$row['team_logo'] : '../images/default_team.png';

            // NEW: FETCH DRAFT STATS FOR THIS TEAM
            $draft_sql = "SELECT COUNT(*) as player_count, SUM(points) as total_spent 
                          FROM auction_tracking 
                          WHERE sold_team = '$t_id' AND is_sold = 1";
            $draft_res = mysqli_query($conn, $draft_sql);
            $draft_data = mysqli_fetch_assoc($draft_res);
            $p_count = $draft_data['player_count'] ?? 0;
            $spent = $draft_data['total_spent'] ?? 0;
        ?>
        <div class="bg-white rounded-[3rem] border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500 group">
            <div class="relative h-44 bg-slate-50 flex items-center justify-center">
                <img src="<?php echo $logo_path; ?>" class="relative z-10 w-28 h-28 object-contain transition-transform group-hover:scale-110 duration-500" alt="Team Logo">
                
                <!-- Player Count Badge -->
                <div class="absolute top-6 right-6 bg-slate-900 text-white px-4 py-2 rounded-2xl text-[10px] font-black z-20 shadow-lg">
                    <?php echo $p_count; ?> PLAYERS
                </div>

                <div class="absolute bottom-4 left-8 opacity-10 group-hover:opacity-100 transition-all duration-500 transform group-hover:-translate-y-2">
                     <span class="text-5xl font-black text-slate-300 italic"><?php echo $row['short_name']; ?></span>
                </div>
            </div>

            <div class="p-8">
                <span class="text-[9px] font-black text-teal-500 uppercase tracking-widest bg-teal-50 px-3 py-1 rounded-full border border-teal-100"><?php echo $row['tournament_name']; ?></span>
                <h3 class="text-2xl font-black text-slate-900 mt-4 mb-2 uppercase italic leading-tight group-hover:text-teal-600 transition-colors"><?php echo $row['name']; ?></h3>
                
                <!-- Spent Info -->
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-6">Total Spent: <span class="text-slate-900 font-black">₹<?php echo number_format($spent); ?></span></p>

                <div class="mb-8">
                    <button onclick="viewFullSquad(<?php echo $t_id; ?>, '<?php echo addslashes($row['name']); ?>')" 
                            class="w-full bg-slate-50 border border-slate-100 hover:border-teal-200 hover:bg-teal-50 transition-all p-4 rounded-2xl flex items-center justify-between group/btn">
                        <div class="flex flex-col items-start">
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Current Squad</p>
                            <p class="text-[11px] font-black text-slate-700 uppercase italic"><?php echo $p_count; ?> Players Drafted</p>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover/btn:text-teal-500 group-hover/btn:border-teal-200 transition-all shadow-sm">
                            <i class="fas fa-users text-xs"></i>
                        </div>
                    </button>
                </div>

                <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-50">
                    <div>
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mb-1">Owner</p>
                        <p class="text-sm font-bold text-slate-700"><?php echo $row['owner_name']; ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mb-1">Mobile</p>
                        <p class="text-sm font-bold text-slate-700"><?php echo $row['mobile_no']; ?></p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="edit_team.php?id=<?php echo $row['team_id']; ?>" class="flex-grow bg-slate-100 text-slate-700 py-4 rounded-2xl font-black text-[9px] uppercase tracking-widest text-center hover:bg-slate-900 hover:text-white transition-all">
                        Update Profile
                    </a>
                    <button onclick="confirmDelete(<?php echo $row['team_id']; ?>)" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php 
        endwhile; 
        /* --- END OF NEW CODE --- */
    else: ?>
        <div class="col-span-full py-24 text-center bg-slate-50 rounded-[4rem] border-2 border-dashed border-slate-200">
            <i class="fas fa-users-slash text-4xl text-slate-200 mb-4"></i>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Teams found in database</p>
        </div>
    <?php endif; ?>
</div>
</div>

<?php if($total_pages > 1): ?>
<div class="mt-16 flex flex-col md:flex-row items-center justify-center gap-8">
    
    <div class="flex items-center gap-2">
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a href="manage_teams.php?page=<?php echo $i; ?>&tid=<?php echo $filter_tid; ?>" 
               class="w-12 h-12 flex items-center justify-center rounded-xl font-black text-xs transition-all 
               <?php echo ($page == $i) ? 'bg-teal-500 text-white shadow-xl shadow-teal-200' : 'bg-white text-slate-400 hover:bg-slate-100 border border-slate-100'; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>

    <div class="flex items-center bg-white border border-slate-200 p-1.5 rounded-2xl shadow-sm">
        <input type="number" id="pageJumper" min="1" max="<?php echo $total_pages; ?>" placeholder="Pg #" 
               class="w-16 px-4 py-2 text-xs font-black text-slate-800 outline-none bg-transparent"
               onkeydown="if(event.key === 'Enter') jumpToPage()">
        <button onclick="jumpToPage()" class="bg-slate-900 text-white px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all">
            Jump
        </button>
    </div>

</div>

<script>
function jumpToPage() {
    const val = document.getElementById('pageJumper').value;
    const maxPages = <?php echo $total_pages; ?>;
    const t_id = <?php echo $filter_tid; ?>;
    
    if (val > 0 && val <= maxPages) {
        window.location.href = `manage_teams.php?page=${val}&tid=${t_id}`;
    } else {
        alert('Please enter a page between 1 and ' + maxPages);
    }
}

function viewFullSquad(teamId, teamName) {
    // Reuse the playerModal from your previous setup
    const modal = document.getElementById('playerModal');
    const content = document.getElementById('modalContent');
    const title = document.getElementById('modalTitle');
    
    if(!modal) {
        alert("Modal element not found in this page!");
        return;
    }

    title.innerText = teamName + " - Full Squad";
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="flex justify-center p-10"><i class="fas fa-circle-notch fa-spin text-teal-500 text-2xl"></i></div>';
    
    // Fetch all players for this team via AJAX
    fetch(`get_team_squad.php?team_id=${teamId}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
        })
        .catch(err => {
            content.innerHTML = "<p class='text-center text-red-500 font-bold'>Error loading squad.</p>";
        });
}
</script>
<?php endif; ?>

<script>
function confirmDelete(id) {
    if(confirm('Warning: This will remove the team and all associated data. Continue?')) {
        window.location.href = 'manage_teams.php?delete_id=' + id;
    }
}
</script>

<div id="playerModal" class="fixed inset-0 z-[150] hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden animate__animated animate__zoomIn animate__faster border border-white/20">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h3 id="modalTitle" class="text-sm font-black text-slate-800 uppercase italic tracking-tighter">Team Squad</h3>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div id="modalContent" class="p-6 max-h-[60vh] overflow-y-auto">
            <div class="flex justify-center p-10">
                <i class="fas fa-circle-notch fa-spin text-teal-500 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<script>
// Logic to open and close the modal
function closeModal() {
    document.getElementById('playerModal').classList.add('hidden');
}

// Close modal when clicking on the dark background
window.onclick = function(event) {
    const modal = document.getElementById('playerModal');
    if (event.target == modal) closeModal();
}
</script>

<?php include 'includes/footer.php'; ?>