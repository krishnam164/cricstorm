<?php
include 'config.php';

// 1. FETCH LIVE SYNC DATA (Handles real-time bid/player)
$sync_res = mysqli_query($conn, "SELECT * FROM auction_live_sync WHERE sync_id = 1");
$sync = mysqli_fetch_assoc($sync_res);

// 2. FETCH LATEST TRACKING STATUS (Check the VERY LAST official action)
$track_res = mysqli_query($conn, "SELECT player_id, is_sold, is_skip FROM auction_tracking ORDER BY auction_tracking_id DESC LIMIT 1");
$track = mysqli_fetch_assoc($track_res);

// 3. FETCH FULL PLAYER & TEAM DETAILS BASED ON SYNC TABLE
$active_p = $sync['player_id'];
$active_t = $sync['team_id'];
$current_tournament_id = $sync['tournament_id'];

$query = "
    SELECT 
        p.name, p.photo, p.batsman_type, p.category, p.address, p.player_id AS player_no,
        t.name AS team_name, t.team_logo AS team_logo,
        tm.tournament_name
    FROM player_master p
    LEFT JOIN team_master t ON t.team_id = '$active_t'
    LEFT JOIN tournament_master tm ON tm.tournament_id = p.tournament_id
    WHERE p.player_id = '$active_p'
";

$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

// 4. CRITICAL FIX: Only show Sold/Skip if IDs match
$data['points'] = $sync['current_bid'];

// Logic: Only set is_sold to 1 if the player in the tracking table is the SAME as the active player
if ($track['player_id'] == $active_p) {
    $data['is_sold'] = $track['is_sold'];
    $data['is_skip'] = $track['is_skip'];
} else {
    // If we are bidding on a new player, force the popups to stay hidden
    $data['is_sold'] = 0;
    $data['is_skip'] = 0;
}

// 5. LIVE STATS & TICKER
$sold_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM auction_tracking WHERE is_sold = 1 AND tournament_id = '$current_tournament_id'");
$sold_count = mysqli_fetch_assoc($sold_res)['total'] ?? 0;

$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM player_master WHERE tournament_id = '$current_tournament_id'");
$total_players = mysqli_fetch_assoc($total_res)['total'] ?? 0;

$teams_res = mysqli_query($conn, "SELECT name, team_logo FROM team_master WHERE tournament_id = '$current_tournament_id' ORDER BY team_id ASC");

$ticker_query = "
    SELECT p.name, a.points 
    FROM auction_tracking a 
    JOIN player_master p ON p.player_id = a.player_id 
    WHERE a.is_sold = 1 
    AND a.tournament_id = '$current_tournament_id' 
    ORDER BY a.auction_tracking_id DESC 
    LIMIT 6
";
$ticker_res = mysqli_query($conn, $ticker_query);

// Handle Photo Path
$photo_path = ($data['photo'] ?? 'default.png');
$player_photo = (!empty($data['photo']) && file_exists($photo_path)) ? $photo_path : 'uploads/tournaments/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IPL AUCTION DASHBOARD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Bebas+Neue&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
            color: #f8fafc;
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            position: relative;
            margin: 0;
        }

        .action-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(1);
            font-size: 40rem;
            color: rgba(59, 130, 246, 0.03);
            z-index: -1;
            animation: rotateGear 20s linear infinite;
            pointer-events: none;
        }

        @keyframes rotateGear {
            from { transform: translate(-50%, -50%) rotate(0deg); }
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .glass {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .font-bebas { font-family: 'Bebas Neue', sans-serif; letter-spacing: 1px; }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-scroll { display: flex; width: max-content; animation: scroll 30s linear infinite; }

                /* Sold Popup Entry Animation */
        .sold-popup { 
            animation: iplPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; 
        }

        @keyframes iplPop {
            0% { transform: scale(0.8) translateY(50px); opacity: 0; filter: blur(10px); }
            100% { transform: scale(1) translateY(0); opacity: 1; filter: blur(0); }
        }

        /* Background pulse for Unsold */
        .bg-slate-950\/98 {
            background-image: radial-gradient(circle at center, #1e1e1e 0%, #000 100%);
        }
        
    </style>
</head>
<body class="flex flex-col">

    <i class="fas fa-cog action-ring"></i>

  <?php if ($data['is_sold'] == 1): ?>
    <div id="overlay" class="fixed inset-0 z-[100] bg-slate-950/95 backdrop-blur-xl flex items-center justify-center overflow-hidden">
        <div id="trigger-crackers" class="hidden"></div>

        <div class="absolute w-[600px] h-[600px] bg-yellow-500/20 rounded-full blur-[120px] animate-pulse"></div>
        
        <div class="text-center sold-popup relative z-10">
            <div class="mb-6 text-yellow-500 text-6xl animate-bounce">
                <i class="fas fa-trophy"></i>
            </div>
            
            <h3 class="text-blue-400 font-bold tracking-[0.3em] uppercase mb-2 animate-pulse">Congratulations</h3>
            
            <div class="relative inline-block bg-gradient-to-r from-yellow-600 via-yellow-400 to-yellow-600 text-slate-950 px-20 py-6 mb-8 -rotate-1 shadow-[0_0_50px_rgba(234,179,8,0.4)] border-y-4 border-white/30">
                <h1 class="text-9xl font-black font-bebas leading-none tracking-tighter">SOLD!</h1>
            </div>

            <div class="flex flex-col items-center gap-2">
                <p class="text-slate-400 uppercase tracking-widest text-sm">New Addition to</p>
                <h2 class="text-6xl font-bold uppercase text-white drop-shadow-lg">
                    <?php echo $data['team_name']; ?>
                </h2>
                <div class="h-1 w-24 bg-yellow-500 my-4"></div>
                <p class="text-8xl font-bebas text-yellow-400 drop-shadow-2xl">
                    ₹<?php echo number_format($data['points']); ?>
                </p>
            </div>
        </div>
    </div>


    <?php elseif ($data['is_skip'] == 1): ?>
        <div id="overlay" class="fixed inset-0 z-[100] bg-slate-950/98 flex items-center justify-center">
            <div class="text-center sold-popup">
                <div class="mb-6 text-slate-600 text-6xl">
                    <i class="fas fa-gavel"></i>
                </div>
                <h1 class="text-9xl font-bebas text-white/20 tracking-tighter">UNSOLD</h1>
                <div class="bg-white/5 h-[1px] w-full my-6"></div>
                <p class="text-slate-400 text-2xl font-light tracking-[0.2em] uppercase">
                    Better Luck <span class="text-red-500/80">Next Time</span>
                </p>
                <p class="text-slate-600 text-xs mt-4 tracking-widest uppercase">Returning to the acceleration pool</p>
            </div>
        </div>
    <?php endif; ?>

    <header class="h-16 border-b border-white/5 bg-slate-900/50 px-6 flex justify-between items-center z-10">
        <div class="flex items-center gap-4">
            <img src="images/favicon.png" class="h-10 w-10">
            <span class="font-bebas text-2xl tracking-widest">CRIC<span class="text-red-500">STORM</span></span>
        </div>
        <div class="flex gap-10 items-center">
        <div class="text-right">
            <span class="text-[10px] text-slate-400 uppercase font-bold block">Progress</span>
            <span class="text-xl font-bebas text-white"><?php echo $sold_count; ?> / <?php echo $total_players; ?> SOLD</span>
        </div>
        <div class="bg-red-600/20 text-red-500 px-3 py-1 rounded-md text-xs font-bold animate-pulse border border-red-500/30">LIVE</div>
    </div>
    </header>

    <main class="flex-grow flex p-6 gap-6 overflow-hidden z-10">
        <div class="w-1/3 flex flex-col">
            <div class="relative glass rounded-3xl overflow-hidden p-2 h-full">
                <i class="fas fa-gear absolute -bottom-10 -right-10 text-white/5 text-[15rem] animate-spin" style="animation-duration: 10s;"></i>
                <img src="<?php echo $player_photo; ?>" class="relative z-10 w-full h-full object-cover rounded-2xl">
                <div class="absolute top-6 left-6 z-20 bg-blue-600 px-4 py-1 rounded-full border border-white/20 text-[10px] font-black uppercase tracking-widest">
                    Player #<?php echo $data['player_no']; ?>
                </div>
            </div>
        </div>

        <div class="flex-grow flex flex-col gap-6">
            <div class="glass rounded-3xl p-8 relative overflow-hidden">
                <span class="text-red-500 font-bold text-xs uppercase tracking-widest block mb-2">Under the Hammer</span>
                <h1 class="text-7xl font-bebas text-white leading-tight mb-2"><?php echo $data['name'] ?? 'WAITING...'; ?></h1>
                <div class="flex items-center gap-2 text-slate-400">
                    <i class="fas fa-tag text-xs"></i>
                    <span class="text-sm font-semibold uppercase tracking-tighter"><?php echo $data['category'] ?></span>
                </div>
            </div>
                <div class="glass rounded-3xl p-8 flex justify-between items-center border-l-8 border-blue-500 shadow-[0_0_30px_rgba(59,130,246,0.2)]">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Current Leader</p>
                        <div class="flex items-center gap-4">
                            <?php if (!empty($data['team_logo'])): ?>
                                <img src="<?php echo $data['team_logo']; ?>" class="h-16 w-16 object-contain drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]">
                            <?php else: ?>
                                <div class="h-16 w-16 rounded-full bg-slate-800 flex items-center justify-center border border-dashed border-slate-600">
                                    <i class="fas fa-users text-slate-600"></i>
                                </div>
                            <?php endif; ?>
                            
                            <p class="text-5xl font-bebas text-white italic tracking-wide">
                                <?php echo $data['team_name'] ?: 'WAITING FOR BIDS'; ?>
                            </p>
                        </div>
                    </div>
                <div class="text-right">
                    <p class="text-yellow-500 text-xs font-bold uppercase mb-2">Current Offer</p>
                    <p class="text-8xl font-bebas text-white leading-none">
                        ₹<?php echo number_format($data['points']); ?>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="glass rounded-3xl p-6 flex items-center gap-4">
                    <div class="h-14 w-14 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                        <i class="fas fa-cricket-bat-ball text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Player Role</p>
                        <p class="text-3xl font-bebas text-white uppercase tracking-wider"><?php echo $data['batsman_type'] ?: 'Premium'; ?></p>
                    </div>
                </div>
                <div class="glass rounded-3xl p-6 flex items-center gap-4">
                    <div class="h-14 w-14 rounded-full bg-yellow-500/10 flex items-center justify-center text-yellow-500 border border-yellow-500/20">
                        <i class="fas fa-location-dot text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 uppercase font-bold">Hometown</p>
                        <p class="text-3xl font-bebas text-white uppercase tracking-wider"><?php echo $data['address'] ?: 'Global'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-slate-900 border-t border-white/10 z-50">
        <div class="px-6 py-3 flex justify-center gap-8 border-b border-white/5 bg-slate-950/50">
            <?php mysqli_data_seek($teams_res, 0); while($team = mysqli_fetch_assoc($teams_res)): ?>
                <img src="<?php echo $team['team_logo']; ?>" class="h-10 w-10 object-contain opacity-80" title">
            <?php endwhile; ?>
        </div>
        <div class="h-12 overflow-hidden flex items-center relative">
            <div class="absolute left-0 top-0 h-full bg-blue-600 px-6 flex items-center z-10 font-bold text-xs uppercase skew-x-[-15deg] -ml-2 ">
                <span class="skew-x-[15deg]">Recent Sales</span>
            </div>
           <div class="animate-scroll">
        <?php if (mysqli_num_rows($ticker_res) > 0): ?>
            <?php for($i=0; $i<2; $i++): ?>
                <?php mysqli_data_seek($ticker_res, 0); while($row = mysqli_fetch_assoc($ticker_res)): ?>
                    <div class="flex items-center gap-3 border-r border-white/5 px-6">
                        <span class="text-slate-400 font-bold text-[11px] uppercase"><?php echo $row['name']; ?></span>
                        <span class="text-yellow-500 font-bebas text-xl">₹<?php echo number_format($row['points']); ?></span>
                    </div>
                <?php endwhile; ?>
            <?php endfor; ?>
        <?php else: ?>
            <div class="px-6 text-slate-500 text-xs uppercase italic">Awaiting first successful bid...</div>
        <?php endif; ?>
    </div>
        </div>
    </footer>

    <script>
        setTimeout(() => {
            const overlay = document.getElementById('overlay');
            if(overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 1000);
            }
        }, 5000);
    </script>
    <script>
    function fireCrackers() {
        var duration = 5 * 1000;
        var animationEnd = Date.now() + duration;
        var defaults = { startVelocity: 45, spread: 70, ticks: 60, zIndex: 200, gravity: 1 };

        var interval = setInterval(function() {
            var timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            var particleCount = 80 * (timeLeft / duration);
            
            // Firing from Left Bottom
            confetti(Object.assign({}, defaults, { 
                particleCount, 
                origin: { x: 0, y: 0.9 } 
            }));
            
            // Firing from Right Bottom
            confetti(Object.assign({}, defaults, { 
                particleCount, 
                origin: { x: 1, y: 0.9 } 
            }));
        }, 350);
    }

    window.onload = function() {
        // Check if the sold overlay is active
        if (document.getElementById('trigger-crackers')) {
            fireCrackers();
        }

        // Auto-remove overlay after 5 seconds
        setTimeout(() => {
            const overlay = document.getElementById('overlay');
            if(overlay) {
                overlay.style.transition = 'opacity 1s ease-out';
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 1000);
            }
        }, 5000);
    };
</script>
</body>
</html>