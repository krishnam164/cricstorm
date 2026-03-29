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

// 2. AUTO-SELECT NEXT PLAYER LOGIC
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
    $is_skip = ($action == 'Skip' || $action == 'Unsold') ? 1 : 0;

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
            <h2 class="text-2xl font-black uppercase tracking-tighter">Auction <span class="text-orange-600">Pro-Control</span></h2>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Master Console v3.0</p>
        </div>
        <select onchange="location.href='?tid='+this.value" class="bg-slate-100 border-none rounded-xl font-bold text-sm focus:ring-2 focus:ring-orange-500">
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
                <input type="hidden" name="tournament_id" value="<?php echo $active_tournament; ?>">
                
                <div>
                    <label class="text-[8px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Target Player</label>
                    <select name="player_id" id="player_select" class="w-full p-4 bg-slate-50 border-none rounded-2xl font-bold text-xl focus:ring-2 focus:ring-orange-500">
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
                        <label class="text-[10px] font-bold uppercase text-slate-500 mb-4 block tracking-widest">Bid Amount (₹)</label>
                        <div class="flex items-center gap-2 bg-slate-800/40 rounded-2xl border border-white/5 p-2">
                            <input type="number" name="points" id="points_input" 
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
                        <div id="inc_tag" class="text-left pl-4 text-[9px] mt-4 font-black text-orange-500 uppercase tracking-widest">Jump: +2,000</div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest">Leading Team</label>
                        <select name="team_id" class="w-full p-8 bg-slate-50 border-none rounded-3xl font-black text-slate-700 focus:ring-2 focus:ring-orange-500">
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

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6">
                    <button type="submit" name="action" value="Update" class="p-4 bg-slate-800 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-black transition-all">Update</button>
                    <button type="submit" name="action" value="Sold" onclick="return confirm('Confirm Player Sale?')" class="p-4 bg-green-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-green-700 shadow-xl transition-all">Sold</button>
                    <button type="submit" name="action" value="Unsold" class="p-4 bg-rose-600 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-rose-700 transition-all">Unsold</button>
                    <button type="submit" name="action" value="Undo" class="p-4 bg-slate-100 text-slate-400 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-red-50 hover:text-red-500 transition-all">Undo</button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
    <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-200 shadow-sm">
        <h3 class="text-[10px] font-black uppercase text-slate-400 mb-6 tracking-widest">Recent Activity</h3>
        
        <div class="space-y-4">
            <?php
            // Fetch only for the active tournament
            $recent_query = "SELECT p.name, a.points, a.is_sold 
                             FROM auction_tracking a 
                             JOIN player_master p ON p.player_id = a.player_id 
                             WHERE a.tournament_id = '$active_tournament' 
                             ORDER BY a.auction_tracking_id DESC LIMIT 5";
            $recent = mysqli_query($conn, $recent_query);
            
            while($r = mysqli_fetch_assoc($recent)): 
            ?>
                <div class="flex justify-between items-center bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
                    <span class="font-bold text-xs text-slate-700"><?php echo $r['name']; ?></span>
                    <span class="font-black <?php echo $r['is_sold'] ? 'text-green-600' : 'text-slate-400'; ?> text-xs italic">
                        <?php echo $r['is_sold'] ? '₹' . number_format($r['points']) : 'SKIPPED'; ?>
                    </span>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <a href="../live_auctions.php" target="_blank" class="group block bg-white p-6 rounded-3xl border border-slate-100 text-center font-black uppercase text-[10px] tracking-widest text-slate-400 hover:text-orange-600 hover:border-orange-100 hover:shadow-lg transition-all">
        <i class="fas fa-external-link-alt mr-2 group-hover:scale-110 transition-transform"></i> 
        Launch Broadcast
    </a>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function adjustBid(dir) {
    const input = document.getElementById('points_input');
    const tag = document.getElementById('inc_tag');
    let val = parseInt(input.value) || 0;
    
    // Dynamic Jump Logic
    let step = 2000;
    if (val >= 100000) step = 10000;
    else if (val >= 50000) step = 5000;

    input.value = (dir === 'up') ? val + step : Math.max(0, val - step);
    tag.innerText = `Jump: ${dir === 'up' ? '+' : '-'}${step.toLocaleString()}`;
    
    // Subtle flash effect
    tag.style.opacity = '0.5';
    setTimeout(() => tag.style.opacity = '1', 150);
}

// Global Keyboard Listeners
document.addEventListener('keydown', function(e) {
    // Only trigger if we aren't typing in an input field
    if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'SELECT') {
        if (e.key === "ArrowUp") { e.preventDefault(); adjustBid('up'); }
        if (e.key === "ArrowDown") { e.preventDefault(); adjustBid('down'); }
    }
});

window.onload = () => {
    const pInput = document.getElementById('points_input');
    pInput.focus();
    pInput.select();
}
</script>

<?php include 'includes/footer.php'; ?>