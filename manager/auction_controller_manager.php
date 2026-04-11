<?php
include '../config.php';

// 1. SECURITY & PERMISSION GATE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

$perm_res = mysqli_query($conn, "SELECT manager_request FROM users WHERE user_id = '$user_id'");
$perm_row = mysqli_fetch_assoc($perm_res);

if (($perm_row['manager_request'] ?? '') !== 'Accepted') {
    header("Location: request_access.php?msg=denied");
    exit();
}

// 2. TOURNAMENT INITIALIZATION
if (isset($_GET['tid'])) {
    $active_tournament = intval($_GET['tid']);
    $auth_check = mysqli_query($conn, "SELECT auction_id FROM auction_master WHERE tournament_id = '$active_tournament' AND user_id = '$user_id'");
    if (mysqli_num_rows($auth_check) == 0) {
        die("<div class='p-10 text-center font-black text-rose-500'>Unauthorized Access.</div>");
    }
} else {
    $latest = mysqli_query($conn, "SELECT tm.tournament_id FROM tournament_master tm 
                                    JOIN auction_master am ON tm.tournament_id = am.tournament_id 
                                    WHERE am.user_id = '$user_id' 
                                    ORDER BY tm.tournament_id DESC LIMIT 1");
    $active_tournament = mysqli_fetch_assoc($latest)['tournament_id'] ?? 0;
}

if ($active_tournament == 0) {
    header("Location: all_tournaments.php?msg=no_assignments"); exit();
}

// 3. AUTO-SELECT LOGIC
$current = null;
if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    $next_q = "SELECT player_id FROM player_master WHERE tournament_id = '$active_tournament' 
               AND player_id NOT IN (SELECT player_id FROM auction_tracking WHERE tournament_id = '$active_tournament')
               ORDER BY player_id ASC LIMIT 1";
    $next_res = mysqli_query($conn, $next_q);
    if ($next_data = mysqli_fetch_assoc($next_res)) {
        $current = ['player_id' => $next_data['player_id'], 'points' => 0, 'sold_team' => 0];
    }
}

if (!$current) {
    $preview_res = mysqli_query($conn, "SELECT * FROM auction_tracking WHERE tournament_id = '$active_tournament' ORDER BY auction_tracking_id DESC LIMIT 1");
    $current = mysqli_fetch_assoc($preview_res);
}

// 4. ACTION HANDLER
if (isset($_POST['action'])) {
    $p_id = intval($_POST['player_id']);
    $points = intval($_POST['points']);
    $t_id = intval($_POST['tournament_id']);
    $team_id = intval($_POST['team_id']);
    $action = $_POST['action'];

    if ($action == 'Undo') {
        mysqli_query($conn, "DELETE FROM auction_tracking WHERE tournament_id = '$t_id' ORDER BY auction_tracking_id DESC LIMIT 1");
        header("Location: auction_controller.php?tid=$t_id&msg=undone"); exit();
    }

    $is_sold = ($action == 'Sold') ? 1 : 0;
    $is_skip = ($action == 'Unsold') ? 1 : 0;

    $sql = "INSERT INTO auction_tracking (player_id, points, tournament_id, sold_team, is_sold, is_skip, auction_tracking_datetime) 
            VALUES ('$p_id', '$points', '$t_id', '$team_id', '$is_sold', '$is_skip', NOW())";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: auction_controller.php?tid=$t_id&msg=success"); exit();
    }
}

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto p-4 md:p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 md:mb-8">
        <div>
            <h2 class="text-xl md:text-2xl font-black uppercase tracking-tighter">Manager <span class="text-orange-500">Controller</span></h2>
            <p class="text-[8px] md:text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Authorized Session Active</p>
        </div>
        <select onchange="location.href='?tid='+this.value" class="w-full sm:w-auto bg-white border border-slate-200 rounded-xl font-bold text-sm px-4 py-3 outline-none shadow-sm cursor-pointer appearance-none">
            <?php
            $tourneys = mysqli_query($conn, "SELECT tm.tournament_id, tm.tournament_name FROM tournament_master tm 
                                           JOIN auction_master am ON tm.tournament_id = am.tournament_id 
                                           WHERE am.user_id = '$user_id' ORDER BY tm.tournament_id DESC");
            while($t = mysqli_fetch_assoc($tourneys)) {
                $sel = ($t['tournament_id'] == $active_tournament) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-7">
        <div class="lg:col-span-2 bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-2xl border border-slate-100 p-5 md:p-8">
            <form method="POST" id="auctionForm" class="space-y-6">
                <input type="hidden" name="tournament_id" id="tournament_id" value="<?php echo $active_tournament; ?>">
                
                <div>
                    <div class="flex justify-between items-center mb-2 px-1">
                        <label class="text-[8px] font-black uppercase text-slate-400 block tracking-widest">Active Bidder</label>
                        <button type="button" onclick="nextDropdownPlayer()" class="text-[9px] font-black uppercase bg-orange-100 text-orange-600 px-3 py-1.5 rounded-lg hover:bg-orange-600 hover:text-white transition-all active:scale-95">
                            Next <span class="hidden sm:inline">Player</span> <i class="fas fa-forward ml-1"></i>
                        </button>
                    </div>
                    <select name="player_id" id="player_select" onchange="autoUpdate()" class="w-full p-4 bg-slate-50 border-none rounded-2xl font-bold text-lg md:text-xl focus:ring-2 focus:ring-orange-500 appearance-none">
                        <?php
                        $players = mysqli_query($conn, "SELECT player_id, name FROM player_master WHERE tournament_id = '$active_tournament' ORDER BY name ASC");
                        while($p = mysqli_fetch_assoc($players)) {
                            $sel = ($p['player_id'] == ($current['player_id'] ?? '')) ? 'selected' : '';
                            echo "<option value='".$p['player_id']."' $sel>#".$p['player_id']." - ".$p['name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-900 rounded-[2rem] p-5 md:p-6 text-white shadow-xl">
                        <label class="text-[10px] font-bold uppercase text-slate-500 mb-4 block tracking-widest">Current Bid (₹)</label>
                        <div class="flex items-center gap-2 bg-slate-800/40 rounded-2xl border border-white/5 p-2">
                            <input type="number" name="points" id="points_input" oninput="autoUpdate()"
                                   value="<?php echo $current['points'] ?? 0; ?>" 
                                   class="bg-transparent border-none text-left pl-2 font-black text-4xl md:text-5xl w-full focus:ring-0">
                            
                            <div class="flex flex-col gap-1.5">
                                <button type="button" onclick="adjustBid('up')" class="h-12 w-14 flex items-center justify-center bg-orange-600 rounded-t-xl hover:bg-orange-500 transition-all active:scale-90">
                                    <i class="fas fa-plus text-sm"></i>
                                </button>
                                <button type="button" onclick="adjustBid('down')" class="h-12 w-14 flex items-center justify-center bg-slate-700 rounded-b-xl hover:bg-slate-600 transition-all active:scale-90">
                                    <i class="fas fa-minus text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div id="status_indicator" class="text-left pl-2 text-[9px] mt-4 font-black text-slate-500 uppercase tracking-widest transition-colors">Status: Ready</div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest ml-2">Buying Team</label>
                        <select name="team_id" id="team_select" onchange="autoUpdate()" class="w-full p-6 md:p-8 bg-slate-50 border-none rounded-[1.5rem] md:rounded-3xl font-black text-slate-700 text-sm md:text-base focus:ring-2 focus:ring-orange-500 appearance-none">
                            <option value="0">WAITING...</option>
                            <?php
                            $teams = mysqli_query($conn, "SELECT team_id, name FROM team_master WHERE tournament_id = '$active_tournament'");
                            while($t = mysqli_fetch_assoc($teams)) {
                                $sel = ($t['team_id'] == ($current['sold_team'] ?? '')) ? 'selected' : '';
                                echo "<option value='".$t['team_id']."' $sel>".$t['name']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 pt-4 md:pt-6">
                    <button type="submit" name="action" value="Sold" onclick="return confirm('Confirm Sale?')" class="p-4 md:p-5 bg-green-600 text-white rounded-xl md:rounded-2xl font-black uppercase text-xs md:text-sm tracking-widest hover:bg-green-700 shadow-xl transition-all active:scale-95">Sold</button>
                    <button type="submit" name="action" value="Unsold" class="p-4 md:p-5 bg-rose-600 text-white rounded-xl md:rounded-2xl font-black uppercase text-xs md:text-sm tracking-widest hover:bg-rose-700 transition-all active:scale-95">Unsold</button>
                    <button type="submit" name="action" value="Undo" class="col-span-2 md:col-span-1 p-4 md:p-5 bg-slate-100 text-slate-400 rounded-xl md:rounded-2xl font-black uppercase text-xs md:text-sm tracking-widest hover:bg-red-50 hover:text-red-500 transition-all active:scale-95">Undo Action</button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-50 rounded-[2rem] p-6 md:p-8 border border-slate-200 shadow-sm">
                <h3 class="text-[10px] font-black uppercase text-slate-400 mb-6 tracking-widest">My Recent Logs</h3>
                <div class="space-y-3" id="recent_activity_box">
                    <?php
                    $recent_query = "SELECT p.name, a.points, a.is_sold FROM auction_tracking a JOIN player_master p ON p.player_id = a.player_id WHERE a.tournament_id = '$active_tournament' ORDER BY a.auction_tracking_id DESC LIMIT 5";
                    $recent = mysqli_query($conn, $recent_query);
                    while($r = mysqli_fetch_assoc($recent)): ?>
                        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-slate-100 transition-all">
                            <span class="font-bold text-[11px] text-slate-700 truncate mr-2"><?php echo $r['name']; ?></span>
                            <span class="font-black <?php echo $r['is_sold'] ? 'text-green-600' : 'text-slate-400'; ?> text-[11px] italic shrink-0">
                                <?php echo $r['is_sold'] ? '₹' . number_format($r['points']) : 'SKIPPED'; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <a href="../live_auctions.php" target="_blank" class="block bg-white p-5 rounded-2xl border border-slate-100 text-center font-black uppercase text-[10px] tracking-widest text-slate-400 hover:text-orange-600 transition-all shadow-sm active:bg-orange-50">
                <i class="fas fa-broadcast-tower mr-1"></i> Public Live Board
            </a>
        </div>
    </div>
</div>

<script>
// Javascript logic with touch optimizations
function nextDropdownPlayer() {
    const select = document.getElementById('player_select');
    if (select.selectedIndex < select.options.length - 1) {
        select.selectedIndex = select.selectedIndex + 1;
        document.getElementById('points_input').value = 0;
        document.getElementById('team_select').value = 0;
        autoUpdate();
    }
}

function autoUpdate() {
    const statusTag = document.getElementById('status_indicator');
    statusTag.innerText = "Status: Syncing...";
    statusTag.style.color = "#f97316"; // orange-500
    
    const formData = {
        ajax_update: true,
        player_id: document.getElementById('player_select').value,
        points: document.getElementById('points_input').value,
        team_id: document.getElementById('team_select').value,
        tournament_id: document.getElementById('tournament_id').value
    };

    fetch('../backpanel/ajax_update_auction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            statusTag.innerText = "Status: Live & Saved";
            statusTag.style.color = "#10b981"; // emerald-500
        }
    })
    .catch(error => { 
        statusTag.innerText = "Status: Sync Error"; 
        statusTag.style.color = "#ef4444"; // red-500
    });
}

function adjustBid(dir) {
    const input = document.getElementById('points_input');
    let val = parseInt(input.value) || 0;
    let step = (val >= 100000) ? 10000 : (val >= 50000 ? 5000 : 2000);
    input.value = (dir === 'up') ? val + step : Math.max(0, val - step);
    autoUpdate(); 
}
</script>

<?php include '../backpanel/includes/footer.php'; ?>