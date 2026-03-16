<?php
include '../config.php';

// 1. SECURITY GATE
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
    header("Location: ../login.php");
    exit();
}

/** * 2. TOURNAMENT OVERRIDE 
 * If a tournament is selected via GET, use it. 
 * Otherwise, default to the latest one in tracking.
 */
if (isset($_GET['tid'])) {
    $active_tournament = intval($_GET['tid']);
} else {
    $latest = mysqli_query($conn, "SELECT tournament_id FROM auction_tracking ORDER BY auction_tracking_id DESC LIMIT 1");
    $latest_data = mysqli_fetch_assoc($latest);
    $active_tournament = $latest_data['tournament_id'] ?? 0;
}

// 3. FETCH CURRENT DATA FOR PREVIEW
$preview_res = mysqli_query($conn, "SELECT * FROM auction_tracking WHERE tournament_id = '$active_tournament' ORDER BY auction_tracking_id DESC LIMIT 1");
$current = mysqli_fetch_assoc($preview_res);

// 4. ACTION HANDLER
if (isset($_POST['action'])) {
    $p_id = intval($_POST['player_id']);
    $points = intval($_POST['points']);
    $t_id = intval($_POST['tournament_id']); // This comes from the hidden input below
    $team_id = intval($_POST['team_id']);
    $action_type = $_POST['action'];

    $is_sold = ($action_type == 'Sold') ? 1 : 0;
    $is_skip = ($action_type == 'Skip' || $action_type == 'Unsold') ? 1 : 0;

    $sql = "INSERT INTO auction_tracking (player_id, points, tournament_id, sold_team, is_sold, is_skip, auction_tracking_datetime) 
            VALUES ('$p_id', '$points', '$t_id', '$team_id', '$is_sold', '$is_skip', NOW())";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: auction_controller.php?tid=$t_id&msg=success");
        exit();
    }
}

$active_page = 'auction_controller';
include 'includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic uppercase">Auction <span class="text-orange-600">Command Center</span></h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Tournament Master Console</p>
    </div>

    <div class="bg-slate-100 p-2 rounded-2xl flex items-center gap-3 border border-slate-200">
        <span class="text-[10px] font-black uppercase text-slate-500 ml-4">Switch Tournament:</span>
        <select onchange="location.href='auction_controller.php?tid=' + this.value" class="bg-white border-none rounded-xl px-4 py-2 font-bold text-slate-700 shadow-sm focus:ring-0">
            <?php
            $tourneys = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_id DESC");
            while($t = mysqli_fetch_assoc($tourneys)) {
                $sel = ($t['tournament_id'] == $active_tournament) ? 'selected' : '';
                echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
            }
            ?>
        </select>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <div class="lg:col-span-2 bg-white rounded-[3rem] border border-slate-100 shadow-sm p-10">
        <form method="POST" id="auctionForm" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest text-center md:text-left">Target Player</label>
                    <select name="player_id" id="player_select" class="w-full">
                        <?php
                        $players = mysqli_query($conn, "SELECT player_id, name FROM player_master WHERE tournament_id = '$active_tournament' ORDER BY name ASC");
                        while($p = mysqli_fetch_assoc($players)) {
                            $sel = ($p['player_id'] == ($current['player_id'] ?? '')) ? 'selected' : '';
                            echo "<option value='".$p['player_id']."' $sel>#".$p['player_id']." - ".$p['name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest">Bid Points</label>
                    <input type="number" name="points" id="points_input" value="<?php echo $current['points'] ?? 0; ?>" class="w-full bg-slate-50 border-none p-5 rounded-2xl font-black text-2xl text-slate-700 focus:ring-2 focus:ring-orange-500">
                    
                    <div class="flex gap-2 mt-3">
                        <button type="button" onclick="addPoints(1000)" class="flex-grow bg-slate-100 text-[10px] font-bold py-2 rounded-lg hover:bg-orange-100 transition-colors">+1k</button>
                        <button type="button" onclick="addPoints(5000)" class="flex-grow bg-slate-100 text-[10px] font-bold py-2 rounded-lg hover:bg-orange-100 transition-colors">+5k</button>
                        <button type="button" onclick="addPoints(10000)" class="flex-grow bg-slate-100 text-[10px] font-bold py-2 rounded-lg hover:bg-orange-100 transition-colors">+10k</button>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 mb-3 tracking-widest">Current Bidder</label>
                    <select name="team_id" class="w-full bg-slate-50 border-none p-5 rounded-2xl font-bold text-slate-700">
                        <option value="0">No Bid / Opening</option>
                        <?php
                        $teams = mysqli_query($conn, "SELECT team_id, name FROM team_master WHERE tournament_id = '$active_tournament'");
                        while($t = mysqli_fetch_assoc($teams)) {
                            $sel = ($t['team_id'] == ($current['sold_team'] ?? '')) ? 'selected' : '';
                            echo "<option value='".$t['team_id']."' $sel>".$t['name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <input type="hidden" name="tournament_id" value="<?php echo $active_tournament; ?>">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-8 border-t border-slate-50">
                <button type="submit" name="action" value="Update" class="bg-slate-900 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-teal-500 shadow-xl transition-all">Update Bid</button>
                <button type="submit" name="action" value="Sold" class="bg-green-600 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-green-700 shadow-xl transition-all">Sold</button>
                <button type="submit" name="action" value="Skip" class="bg-amber-500 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-amber-600 shadow-xl transition-all">Skip</button>
                <button type="submit" name="action" value="Unsold" class="bg-rose-600 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-rose-700 shadow-xl transition-all">Unsold</button>
            </div>
        </form>
    </div>

    <div class="space-y-6">
        <div class="bg-slate-900 p-8 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-orange-500/10 rounded-full blur-3xl"></div>
            <p class="text-[9px] font-black uppercase text-slate-500 mb-6 tracking-widest">Broadcast Preview</p>
            <div class="space-y-4">
                <div class="flex justify-between border-b border-white/5 pb-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">T-ID</span>
                    <span class="text-xl font-black text-orange-500">#<?php echo $active_tournament; ?></span>
                </div>
                <div class="flex justify-between border-b border-white/5 pb-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Points</span>
                    <span class="text-2xl font-black text-teal-400">₹<?php echo number_format($current['points'] ?? 0); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Status</span>
                    <span class="bg-white/10 text-white px-4 py-1 rounded-full text-[9px] font-black uppercase italic">
                        <?php echo ($current['is_sold'] ?? 0) ? 'SOLD' : (($current['is_skip'] ?? 0) ? 'SKIPPED' : 'BIDDING'); ?>
                    </span>
                </div>
            </div>
        </div>
        <a href="../live_auctions.php" target="_blank" class="block bg-white p-6 rounded-3xl border border-slate-100 text-center font-black uppercase text-[10px] tracking-[0.2em] text-slate-400 hover:text-orange-500 transition-all shadow-sm">
            <i class="fas fa-external-link-alt mr-2"></i> Launch Broadcast
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#player_select').select2({
            placeholder: "Search player for this tournament..."
        });
    });

    function addPoints(val) {
        let input = document.getElementById('points_input');
        input.value = (parseInt(input.value) || 0) + val;
    }
</script>

<?php include 'includes/footer.php'; ?>