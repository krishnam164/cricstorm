<?php
include '../config.php';

// 1. ORGANIZER SECURITY GATE
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] == 'player') {
    header("Location: ../login.php"); exit();
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

// 2. FETCH ASSIGNED TOURNAMENT ID
// If tid is in URL, verify ownership. If not, pick the latest assigned one.
if (isset($_GET['tid'])) {
    $active_tournament = intval($_GET['tid']);
    // VERIFY OWNERSHIP: Check if this user is assigned to this TID in auction_master
    $check_auth = mysqli_query($conn, "SELECT auction_id FROM auction_master WHERE tournament_id = '$active_tournament' AND user_id = '$user_id'");
    if (mysqli_num_rows($check_auth) == 0) {
        die("<div style='padding:50px; text-align:center; font-family:sans-serif;'>
                <h1 style='color:red;'>ACCESS DENIED</h1>
                <p>You are not authorized to control this tournament auction.</p>
                <a href='all_tournaments.php'>Back to My Tournaments</a>
             </div>");
    }
} else {
    // Auto-select the most recent tournament assigned to this organizer
    $latest = mysqli_query($conn, "SELECT tournament_id FROM auction_master WHERE user_id = '$user_id' ORDER BY auction_id DESC LIMIT 1");
    $active_tournament = mysqli_fetch_assoc($latest)['tournament_id'] ?? 0;
}

if ($active_tournament == 0) {
    header("Location: all_tournaments.php?msg=no_assignments"); exit();
}

// 3. AUTO-SELECT NEXT PLAYER LOGIC (Same as Admin)
$current = null;
$preview_res = mysqli_query($conn, "SELECT * FROM auction_tracking WHERE tournament_id = '$active_tournament' ORDER BY auction_tracking_id DESC LIMIT 1");
$current = mysqli_fetch_assoc($preview_res);

// 4. ACTION HANDLER (Sold, Unsold, Undo)
if (isset($_POST['action'])) {
    $p_id = intval($_POST['player_id']);
    $points = intval($_POST['points']);
    $t_id = intval($_POST['tournament_id']);
    $team_id = intval($_POST['team_id']);
    $action = $_POST['action'];

    if ($action == 'Undo') {
        mysqli_query($conn, "DELETE FROM auction_tracking WHERE tournament_id = '$t_id' ORDER BY auction_tracking_id DESC LIMIT 1");
        header("Location: auction_controller_organizer.php?tid=$t_id&msg=undone"); exit();
    }

    $is_sold = ($action == 'Sold') ? 1 : 0;
    $is_skip = ($action == 'Unsold') ? 1 : 0;

    $sql = "INSERT INTO auction_tracking (player_id, points, tournament_id, sold_team, is_sold, is_skip, auction_tracking_datetime) 
            VALUES ('$p_id', '$points', '$t_id', '$team_id', '$is_sold', '$is_skip', NOW())";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: auction_controller_organizer.php?tid=$t_id&msg=success"); exit();
    }
}

include 'includes/header.php';
?>

<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black uppercase tracking-tighter text-slate-800">Organizer <span class="text-orange-500">Console</span></h2>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Live Sync: On</p>
        </div>
        
        <select onchange="location.href='?tid='+this.value" class="bg-white border border-slate-100 rounded-xl font-bold text-sm px-4 py-2 shadow-sm outline-none">
            <?php
            $tourneys = mysqli_query($conn, "SELECT tm.tournament_id, tm.tournament_name 
                                           FROM tournament_master tm 
                                           JOIN auction_master am ON tm.tournament_id = am.tournament_id 
                                           WHERE am.user_id = '$user_id' 
                                           GROUP BY tm.tournament_id");
            while($t = mysqli_fetch_assoc($tourneys)) {
                $sel = ($t['tournament_id'] == $active_tournament) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-2xl border border-slate-100 p-8">
            <form method="POST" id="auctionForm" class="space-y-6">
                <input type="hidden" name="tournament_id" id="tournament_id" value="<?php echo $active_tournament; ?>">
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-[8px] font-black uppercase text-slate-400 block tracking-widest">Target Player</label>
                        <button type="button" onclick="nextDropdownPlayer()" class="text-[9px] font-black uppercase bg-orange-100 text-orange-600 px-3 py-1 rounded-lg hover:bg-orange-600 hover:text-white transition-all">
                            Next <i class="fas fa-forward ml-1"></i>
                        </button>
                    </div>
                    <select name="player_id" id="player_select" onchange="autoUpdate()" class="w-full p-4 bg-slate-50 border-none rounded-2xl font-bold text-xl focus:ring-2 focus:ring-orange-500">
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
                    <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl">
                        <label class="text-[10px] font-bold uppercase text-slate-500 mb-4 block tracking-widest">Bid (₹)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="points" id="points_input" oninput="autoUpdate()" value="<?php echo $current['points'] ?? 0; ?>" class="bg-transparent border-none text-left font-black text-5xl w-full focus:ring-0">
                            <div class="flex flex-col gap-1">
                                <button type="button" onclick="adjustBid('up')" class="h-10 w-12 flex items-center justify-center bg-orange-600 rounded-xl hover:bg-orange-500 transition-all"><i class="fas fa-chevron-up"></i></button>
                                <button type="button" onclick="adjustBid('down')" class="h-10 w-12 flex items-center justify-center bg-slate-700 rounded-xl hover:bg-slate-600 transition-all"><i class="fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                        <div id="status_indicator" class="text-[9px] mt-6 font-black text-slate-500 uppercase">Status: Ready</div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Leading Team</label>
                        <select name="team_id" id="team_select" onchange="autoUpdate()" class="w-full p-8 bg-slate-50 border-none rounded-3xl font-black text-slate-700 text-lg focus:ring-2 focus:ring-orange-500">
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4">
                    <button type="submit" name="action" value="Sold" class="p-5 bg-green-600 text-white rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-green-700">Sold</button>
                    <button type="submit" name="action" value="Unsold" class="p-5 bg-rose-600 text-white rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-rose-700">Unsold</button>
                    <button type="submit" name="action" value="Undo" class="p-5 bg-slate-100 text-slate-400 rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-red-50 hover:text-red-500">Undo</button>
                </div>
            </form>
        </div>

        <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-200">
            <h3 class="text-[10px] font-black uppercase text-slate-400 mb-6 tracking-widest">History</h3>
            <div class="space-y-4">
                <?php
                $recent = mysqli_query($conn, "SELECT p.name, a.points, a.is_sold FROM auction_tracking a JOIN player_master p ON p.player_id = a.player_id WHERE a.tournament_id = '$active_tournament' AND (a.is_sold=1 OR a.is_skip=1) ORDER BY a.auction_tracking_id DESC LIMIT 5");
                while($r = mysqli_fetch_assoc($recent)): ?>
                    <div class="flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <span class="font-bold text-xs"><?php echo $r['name']; ?></span>
                        <span class="font-black <?php echo $r['is_sold'] ? 'text-green-600' : 'text-slate-300'; ?> text-[10px]">
                            <?php echo $r['is_sold'] ? '₹'.number_format($r['points']) : 'UNSOLD'; ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
// --- NEXT PLAYER BUTTON ---
function nextDropdownPlayer() {
    const select = document.getElementById('player_select');
    if (select.selectedIndex < select.options.length - 1) {
        select.selectedIndex = select.selectedIndex + 1;
        document.getElementById('points_input').value = 0;
        document.getElementById('team_select').value = 0;
        autoUpdate();
    }
}

// --- AJAX LIVE SYNC ---
function autoUpdate() {
    const statusTag = document.getElementById('status_indicator');
    statusTag.innerText = "Status: Syncing...";
    
    const formData = {
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
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            statusTag.innerText = "Status: Live & Saved";
            statusTag.style.color = "#10b981"; // Green
        }
    })
    .catch(err => { statusTag.innerText = "Status: Sync Error"; });
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