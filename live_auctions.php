<?php
include 'config.php';

// 1. FETCH LATEST AUCTION DATA
$query = "
    SELECT 
        a.auction_tracking_id, a.player_id, a.points, a.is_sold, a.is_skip, a.sold_team, a.tournament_id,
        p.name, p.photo, p.batsman_type, p.address, p.player_id AS player_no,
        t.name AS team_name, t.logo AS team_logo
    FROM auction_tracking a
    LEFT JOIN player_master p ON p.player_id = a.player_id
    LEFT JOIN team_master t ON t.team_id = a.sold_team
    ORDER BY a.auction_tracking_id DESC
    LIMIT 1
";

$res = mysqli_query($conn, $query);
if(!$res) { die("Database Error: " . mysqli_error($conn)); }
$data = mysqli_fetch_assoc($res);

/**
 * PHOTO LOGIC FIX
 */
$player_photo = !empty($data['photo']) ? 'uploads/players/' . $data['photo'] : 'uploads/players/default.png';

$current_tournament_id = $data['tournament_id'] ?? 0;

// 2. LIVE STATS
$sold_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM auction_tracking WHERE is_sold = 1 AND tournament_id = '$current_tournament_id'");
$sold_count = mysqli_fetch_assoc($sold_res)['total'] ?? 0;

$unsold_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM auction_tracking WHERE is_skip = 1 AND tournament_id = '$current_tournament_id'");
$unsold_count = mysqli_fetch_assoc($unsold_res)['total'] ?? 0;

$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM player_master");
$total_players = mysqli_fetch_assoc($total_res)['total'] ?? 0;

$available_count = $total_players - ($sold_count + $unsold_count);

// 3. TEAMS LIST
$teams_res = mysqli_query($conn, "SELECT name, logo FROM team_master WHERE tournament_id = '$current_tournament_id' ORDER BY team_id ASC");

// --- NEW FEATURE: DATA FOR TICKER ---
$ticker_res = mysqli_query($conn, "SELECT p.name, a.points FROM auction_tracking a JOIN player_master p ON p.player_id = a.player_id WHERE a.is_sold = 1 AND a.tournament_id = '$current_tournament_id' ORDER BY a.auction_tracking_id DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Broadcast | CricStrome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #020617; color: white; overflow: hidden; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .skew-element { transform: skew-x(-12deg); }
        .skew-fix { transform: skew-x(12deg); }
        .bid-glow { text-shadow: 0 0 20px rgba(45, 212, 191, 0.6); }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
        .animate-bid { animation: pulse 1s infinite; }

        .sold-overlay {
            position: fixed; inset: 0; background: rgba(2, 6, 23, 0.9);
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            z-index: 100; animation: fadeIn 0.5s ease forwards;
        }
        .sold-stamp {
            font-size: 10rem; font-weight: 900; color: #fbbf24; text-transform: uppercase;
            transform: rotate(-10deg); border: 15px solid #fbbf24; padding: 1rem 4rem;
            border-radius: 2rem; box-shadow: 0 0 50px rgba(251, 191, 36, 0.3);
            animation: stampIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        /* Gavel Animation */
        .gavel-icon { font-size: 8rem; color: #fbbf24; margin-bottom: 20px; animation: gavelStrike 0.5s ease infinite alternate; }
        @keyframes gavelStrike { from { transform: rotate(0deg); } to { transform: rotate(-45deg); } }
        
        /* NEW FEATURE: Ticker Style */
        .ticker-wrap { width: 100%; overflow: hidden; background: rgba(0,0,0,0.3); padding: 8px 0; border-top: 1px solid rgba(255,255,255,0.1); }
        .ticker { display: flex; animation: tickerScroll 20s linear infinite; white-space: nowrap; }
        @keyframes tickerScroll { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }

        @keyframes stampIn { from { transform: scale(5) rotate(0deg); opacity: 0; } to { transform: scale(1) rotate(-10deg); opacity: 1; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="p-8 h-screen flex flex-col justify-between">

    <?php if ($data['is_sold'] == 1): ?>
        <audio autoplay><source src="https://www.myinstants.com/media/sounds/gavel-敲槌聲.mp3" type="audio/mpeg"></audio>
        <div class="sold-overlay">
            <i class="fas fa-gavel gavel-icon"></i>
            <canvas id="confetti-canvas" class="absolute inset-0 w-full h-full"></canvas>
            <div class="relative z-10 text-center">
                <div class="sold-stamp">SOLD</div>
                <h2 class="text-6xl font-black mt-12 text-white uppercase italic tracking-tighter">
                    Congratulations <br>
                    <span class="text-teal-400 text-7xl"><?php echo $data['team_name']; ?></span>
                </h2>
                <div class="mt-8 glass py-4 px-10 rounded-full inline-block">
                    <p class="text-2xl font-black text-yellow-400 italic">₹<?php echo number_format($data['points']); ?></p>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
        <script>
            var end = Date.now() + (5 * 1000);
            var colors = ['#fbbf24', '#2dd4bf', '#ffffff'];
            (function frame() {
              confetti({ particleCount: 3, angle: 60, spread: 55, origin: { x: 0 }, colors: colors });
              confetti({ particleCount: 3, angle: 120, spread: 55, origin: { x: 1 }, colors: colors });
              if (Date.now() < end) { requestAnimationFrame(frame); }
            }());
        </script>
    <?php endif; ?>

    <?php if ($data['is_skip'] == 1): ?>
        <audio autoplay><source src="https://www.myinstants.com/media/sounds/buzzer.mp3" type="audio/mpeg"></audio>
        <div class="fixed inset-0 bg-red-950/95 flex flex-col items-center justify-center z-[110] animate-pulse">
            <h1 class="text-[12rem] font-black text-white italic border-[20px] border-white p-12 uppercase transform -rotate-12 shadow-2xl">
                Unsold
            </h1>
            <p class="text-2xl font-bold mt-10 tracking-[1em] uppercase text-red-200">Better Luck Next Time</p>
        </div>
    <?php endif; ?>

    <header class="flex justify-between items-center relative z-10">
        <div class="flex items-center gap-4">
            <div class="bg-white px-6 py-2 rounded-2xl shadow-xl">
                <h1 class="text-slate-900 font-black italic text-2xl uppercase tracking-tighter">
                    CRIC<span class="text-orange-600">STROME</span>
                </h1>
            </div>
            <div class="glass px-6 py-2 rounded-2xl border-l-4 border-orange-500">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Live Broadcast Engine</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="glass px-8 py-3 rounded-2xl text-center border-b-4 border-green-500">
                <p class="text-[10px] font-black text-green-400 uppercase tracking-widest">Sold</p>
                <p class="text-2xl font-black"><?php echo $sold_count; ?></p>
            </div>
            <div class="glass px-8 py-3 rounded-2xl text-center border-b-4 border-red-500">
                <p class="text-[10px] font-black text-red-400 uppercase tracking-widest">Unsold</p>
                <p class="text-2xl font-black"><?php echo $unsold_count; ?></p>
            </div>
        </div>
    </header>

    <main class="flex items-center justify-between gap-10 relative z-10">
        <div class="w-1/4 relative">
            <div class="relative z-10 w-80 h-80 rounded-[3rem] overflow-hidden border-8 border-white/5 shadow-2xl bg-slate-800 flex items-center justify-center">
                <img src="<?php echo $player_photo; ?>" class="w-full h-full object-cover">
            </div>
            <div class="absolute -top-4 -left-4 bg-orange-600 text-white w-16 h-16 rounded-2xl flex items-center justify-center font-black text-2xl shadow-xl z-20">
                <?php echo $data['player_no'] ?? '0'; ?>
            </div>
        </div>

        <div class="w-2/4 space-y-4">
            <div class="skew-element bg-orange-600 px-10 py-6 rounded-2xl shadow-2xl">
                <div class="skew-fix">
                    <p class="text-[10px] font-black text-orange-950 uppercase tracking-[0.4em] mb-1">Now Auctioning</p>
                    <h2 class="text-6xl font-black italic uppercase tracking-tighter text-white">
                        <?php echo $data['name'] ?? 'Ready to Start'; ?>
                    </h2>
                </div>
            </div>

            <div class="glass skew-element p-8 rounded-2xl border-l-8 border-teal-500">
                <div class="skew-fix flex gap-12">
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Type</p>
                        <p class="text-2xl font-black text-teal-400">
                            <?php echo !empty($data['batsman_type']) ? $data['batsman_type'] : 'All Rounder'; ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">points</p>
                        <p class="text-2xl font-black text-white italic">₹<?php echo number_format($data['points'] ?? 0); ?></p>
                    </div>
                </div>
            </div>

            <div class="glass skew-element p-6 rounded-2xl">
                <div class="skew-fix">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Hometown / Location</p>
                    <p class="text-xl font-bold text-slate-300"><?php echo $data['address'] ?? 'India'; ?></p>
                </div>
            </div>
        </div>

        <div class="w-1/4">
            <div class="glass p-12 rounded-[4rem] text-center relative border-t-8 border-teal-500 shadow-2xl">
                <p class="text-[11px] font-black uppercase tracking-[0.5em] text-slate-500 mb-6">Current Bid</p>
                <h3 class="text-5xl font-black italic tracking-tighter text-teal-400 bid-glow animate-bid mb-8">
                    ₹<?php echo number_format($data['points'] ?? 0); ?>
                </h3>
                <div class="bg-white/5 p-6 rounded-3xl border border-white/10">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2">Leading Team</p>
                    <div class="flex items-center justify-center gap-3">
                        <?php if(!empty($data['team_logo'])): ?>
                            <img src="uploads/teams/<?php echo $data['team_logo']; ?>" class="w-8 h-8 object-contain">
                        <?php endif; ?>
                        <p class="text-lg font-black uppercase italic tracking-tighter">
                            <?php echo $data['team_name'] ?? 'Waiting for Bids'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="ticker-wrap relative z-10">
        <div class="ticker">
            <?php while($row = mysqli_fetch_assoc($ticker_res)): ?>
                <span class="mx-10 text-orange-400 font-bold uppercase">
                    <i class="fas fa-check-circle mr-2"></i> <?php echo $row['name']; ?> SOLD @ ₹<?php echo number_format($row['points']); ?>
                </span>
            <?php endwhile; ?>
        </div>
    </div>

    <footer class="glass p-6 rounded-[2.5rem] flex items-center justify-between relative z-10">
        <div class="flex items-center gap-4 px-6 border-r border-white/10">
            <i class="fas fa-users text-orange-500"></i>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">League Teams</span>
        </div>
        <div class="flex-grow flex justify-center gap-10">
            <?php while($team = mysqli_fetch_assoc($teams_res)): ?>
            <div class="flex items-center gap-3 opacity-60 hover:opacity-100 transition-all">
                <img src="uploads/teams/<?php echo $team['logo']; ?>" class="w-8 h-8 object-contain grayscale hover:grayscale-0" onerror="this.src='https://via.placeholder.com/32'">
                <span class="text-[10px] font-black uppercase tracking-widest hidden lg:block"><?php echo $team['name']; ?></span>
            </div>
            <?php endwhile; ?>
        </div>
    </footer>

</body>
</html>