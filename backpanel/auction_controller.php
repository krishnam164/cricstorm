<?php
include '../config.php';

// 1. SECURITY & TOURNAMENT INITIALIZATION
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
    header("Location: ../login.php"); exit();
}

if (isset($_GET['tid'])) {
    $active_tournament = intval($_GET['tid']);
} else {
    $latest = mysqli_query($conn, "SELECT tournament_id FROM auction_tracking ORDER BY auction_tracking_id DESC LIMIT 1");
    $active_tournament = mysqli_fetch_assoc($latest)['tournament_id'] ?? 0;
}

// 2. AUTO-SELECT LOGIC
$current = null;
if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
    // Automatically find the next player ID that is NOT in auction_tracking
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

// 3. ACTION HANDLER
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

<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black uppercase tracking-tighter">Auction <span class="text-orange-600">Live-Sync</span></h2>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Auto-Updating Console Active</p>
        </div>
        <select onchange="location.href='?tid='+this.value" class="bg-slate-100 border-none rounded-xl font-bold text-sm focus:ring-2 focus:ring-orange-500 cursor-pointer">
            <?php
            $tourneys = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_id DESC");
            while($t = mysqli_fetch_assoc($tourneys)) {
                $sel = ($t['tournament_id'] == $active_tournament) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-7">
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-2xl border border-slate-800 p-8">
            <form method="POST" id="auctionForm" class="space-y-6">
                <input type="hidden" name="tournament_id" id="tournament_id" value="<?php echo $active_tournament; ?>">
                
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-[8px] font-black uppercase text-slate-400 block tracking-widest">Target Player</label>
                        <!-- NEXT PLAYER BUTTON -->
                        <button type="button" onclick="nextDropdownPlayer()" class="text-[9px] font-black uppercase bg-orange-100 text-orange-600 px-3 py-1 rounded-lg hover:bg-orange-600 hover:text-white transition-all">
                            Next Player <i class="fas fa-forward ml-1"></i>
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
                    <div class="bg-slate-900 rounded-2xl p-6 text-white shadow-inner">
                        <label class="text-[10px] font-bold uppercase text-slate-500 mb-4 block tracking-widest">Current Bid (₹)</label>
                        <div class="flex items-center gap-2 bg-slate-800/40 rounded-2xl border border-white/5 p-2">
                            <input type="number" name="points" id="points_input" oninput="autoUpdate()"
                                   value="<?php echo $current['points'] ?? 0; ?>" 
                                   class="bg-transparent border-none text-left pl-4 font-black text-5xl w-full focus:ring-0">
                            
                            <div class="flex flex-col gap-1">
                                <button type="button" onclick="adjustBid('up')" class="h-10 w-12 flex items-center justify-center bg-orange-600 rounded-t-xl hover:bg-orange-500 transition-all active:scale-90">
                                    <i class="fas fa-chevron-up text-xs"></i>
                                </button>
                                <button type="button" onclick="adjustBid('down')" class="h-10 w-12 flex items-center justify-center bg-slate-700 rounded-b-xl hover:bg-slate-600 transition-all active:scale-90">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </div>
                        </div>
                        <div id="status_indicator" class="text-left pl-4 text-[9px] mt-4 font-black text-slate-500 uppercase tracking-widest">Status: Ready</div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Leading Team</label>
                        <select name="team_id" id="team_select" onchange="autoUpdate()" class="w-full p-8 bg-slate-50 border-none rounded-3xl font-black text-slate-700 focus:ring-2 focus:ring-orange-500">
                            <option value="0">WAITING FOR BIDS</option>
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6">
                    <button type="submit" name="action" value="Sold" onclick="return confirm('Confirm Player Sale?')" class="p-5 bg-green-600 text-white rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-green-700 shadow-xl transition-all">Sold</button>
                    <button type="submit" name="action" value="Unsold" class="p-5 bg-rose-600 text-white rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-rose-700 transition-all">Unsold</button>
                    <button type="submit" name="action" value="Undo" class="p-5 bg-slate-100 text-slate-400 rounded-2xl font-black uppercase text-[12px] tracking-widest hover:bg-red-50 hover:text-red-500 transition-all">Undo</button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-200 shadow-sm">
                <h3 class="text-[10px] font-black uppercase text-slate-400 mb-6 tracking-widest">Recent Activity</h3>
                <div class="space-y-4" id="recent_activity_box">
                    <?php
                    $recent_query = "SELECT p.name, a.points, a.is_sold FROM auction_tracking a JOIN player_master p ON p.player_id = a.player_id WHERE a.tournament_id = '$active_tournament' ORDER BY a.auction_tracking_id DESC LIMIT 5";
                    $recent = mysqli_query($conn, $recent_query);
                    while($r = mysqli_fetch_assoc($recent)): ?>
                        <div class="flex justify-between items-center bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                            <span class="font-bold text-xs text-slate-700"><?php echo $r['name']; ?></span>
                            <span class="font-black <?php echo $r['is_sold'] ? 'text-green-600' : 'text-slate-400'; ?> text-xs italic">
                                <?php echo $r['is_sold'] ? '₹' . number_format($r['points']) : 'SKIPPED'; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <a href="../live_auctions.php" target="_blank" class="group block bg-white p-6 rounded-3xl border border-slate-100 text-center font-black uppercase text-[10px] tracking-widest text-slate-400 hover:text-orange-600 transition-all">
                Launch Broadcast
            </a>
        </div>
    </div>
</div>

<script>
// --- Next Player in Dropdown ---
function nextDropdownPlayer() {
    const select = document.getElementById('player_select');
    if (select.selectedIndex < select.options.length - 1) {
        select.selectedIndex = select.selectedIndex + 1;
        // Reset values for new player
        document.getElementById('points_input').value = 0;
        document.getElementById('team_select').value = 0;
        autoUpdate(); // Sync immediately
    } else {
        alert("End of player list reached!");
    }
}

// --- AJAX Auto Update Logic ---
function autoUpdate() {
    const statusTag = document.getElementById('status_indicator');
    statusTag.innerText = "Status: Syncing...";
    statusTag.classList.remove('text-green-500', 'text-slate-500');
    statusTag.classList.add('text-orange-500');

    const formData = {
        ajax_update: true,
        player_id: document.getElementById('player_select').value,
        points: document.getElementById('points_input').value,
        team_id: document.getElementById('team_select').value,
        tournament_id: document.getElementById('tournament_id').value
    };

    fetch('ajax_update_auction.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            statusTag.innerText = "Status: Live & Saved";
            statusTag.classList.remove('text-orange-500');
            statusTag.classList.add('text-green-500');
        }
    })
    .catch(error => {
        statusTag.innerText = "Status: Sync Error";
        console.error('Error:', error);
    });
}

function adjustBid(dir) {
    const input = document.getElementById('points_input');
    let val = parseInt(input.value) || 0;
    
    let step = 2000;
    if (val >= 100000) step = 10000;
    else if (val >= 50000) step = 5000;

    input.value = (dir === 'up') ? val + step : Math.max(0, val - step);
    autoUpdate(); 
}

document.addEventListener('keydown', function(e) {
    if (document.activeElement.tagName !== 'INPUT') {
        if (e.key === "ArrowUp") { e.preventDefault(); adjustBid('up'); }
        if (e.key === "ArrowDown") { e.preventDefault(); adjustBid('down'); }
        if (e.key === "n" || e.key === "N") { nextDropdownPlayer(); } // Hotkey for Next Player
    }
});
</script>

<?php include 'includes/footer.php'; ?>