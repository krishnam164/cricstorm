<?php
include '../config.php';

// 1. ORGANIZER SECURITY GATE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); 
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// 2. FILTER LOGIC (Pagination & Tournament Selection)
$limit = 6; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;
$filter_tid = isset($_GET['tid']) ? intval($_GET['tid']) : 0;

/** * CRITICAL SQL: 
 * We only pull teams where the tournament is assigned to THIS user in auction_master
 */
$base_where = "WHERE am.user_id = '$user_id'";
if ($filter_tid > 0) {
    $base_where .= " AND t.tournament_id = '$filter_tid'";
}

// Get Total for Pagination
$total_sql = "SELECT COUNT(t.team_id) as total 
              FROM team_master t 
              JOIN auction_master am ON t.tournament_id = am.tournament_id 
              $base_where";
$total_res = mysqli_query($conn, $total_sql);
$total_records = mysqli_fetch_assoc($total_res)['total'] ?? 0;
$total_pages = ceil($total_records / $limit);

include 'includes/header.php';
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-end gap-6">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic uppercase">Franchise <span class="text-orange-500">Management</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-2">
            Managing teams for your assigned tournaments
        </p>
    </div>

    <div class="flex items-center gap-3">
       <select onchange="location.href='manage_teams.php?tid=' + this.value" class="bg-white border border-slate-100 rounded-2xl px-6 py-4 font-bold text-xs text-slate-600 shadow-sm outline-none cursor-pointer hover:border-orange-200 transition-colors">
            <option value="0">All My Tournaments</option>
            <?php
            // Added GROUP BY tm.tournament_id to remove duplicates
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
        <a href="add_team.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-500 transition-all shadow-lg">
            + New Team
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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

            // Stats for the card
            $draft_sql = "SELECT COUNT(*) as p_count, SUM(points) as spent FROM auction_tracking WHERE sold_team = '$t_id' AND is_sold = 1";
            $draft_data = mysqli_fetch_assoc(mysqli_query($conn, $draft_sql));
    ?>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden hover:shadow-2xl transition-all group">
        <div class="relative h-40 bg-slate-50 flex items-center justify-center">
            <img src="<?php echo $logo_path; ?>" class="w-24 h-24 object-contain group-hover:scale-110 transition-transform">
            <div class="absolute top-4 right-6 bg-orange-500 text-white px-3 py-1 rounded-full text-[9px] font-black italic">
                <?php echo $draft_data['p_count'] ?? 0; ?> PLAYERS
            </div>
        </div>

        <div class="p-6">
            <span class="text-[8px] font-black text-orange-500 uppercase tracking-widest bg-orange-50 px-2 py-1 rounded-md border border-orange-100">
                <?php echo $row['tournament_name']; ?>
            </span>
            <h3 class="text-xl font-black text-slate-900 mt-3 mb-1 uppercase italic"><?php echo $row['name']; ?></h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Spent: ₹<?php echo number_format($draft_data['spent'] ?? 0); ?></p>

            <button onclick="viewFullSquad(<?php echo $t_id; ?>, '<?php echo addslashes($row['name']); ?>')" 
                    class="w-full bg-slate-50 hover:bg-orange-50 border border-slate-100 p-3 rounded-xl flex items-center justify-between group/btn mb-6">
                <span class="text-[9px] font-black text-slate-600 uppercase tracking-widest">View Full Squad</span>
                <i class="fas fa-users text-slate-300 group-hover/btn:text-orange-500 transition-colors"></i>
            </button>

            <div class="flex gap-2">
                <a href="edit_team.php?id=<?php echo $t_id; ?>" class="flex-grow bg-slate-900 text-white py-3 rounded-xl font-black text-[9px] uppercase tracking-widest text-center hover:bg-orange-500 transition-all">
                    Edit
                </a>
                <button onclick="confirmDelete(<?php echo $t_id; ?>)" class="w-11 h-11 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                    <i class="fas fa-trash-alt text-xs"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endwhile; else: ?>
        <div class="col-span-full py-20 text-center bg-slate-50 rounded-[3rem] border border-dashed border-slate-200">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">No Teams assigned to you yet</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>