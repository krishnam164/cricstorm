<?php
include 'config.php';

// 1. FETCH LATEST AUCTION DATA
$query = "
    SELECT 
        a.auction_tracking_id, a.player_id, a.points, a.is_sold, a.is_skip, a.sold_team, a.tournament_id,
        p.name, p.photo, p.batsman_type, p.address, p.player_id AS player_no,
        t.name AS team_name, t.team_logo AS team_logo
    FROM auction_tracking a
    LEFT JOIN player_master p ON p.player_id = a.player_id
    LEFT JOIN team_master t ON t.team_id = a.sold_team
    ORDER BY a.auction_tracking_id DESC
    LIMIT 1
";

$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

$player_photo = (!empty($data['photo']) && file_exists('uploads/players/' . $data['photo']))
    ? 'uploads/players/' . $data['photo']
    : 'uploads/players/default.png';
$current_tournament_id = $data['tournament_id'] ?? 0;

// 2. LIVE STATS
$sold_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM auction_tracking WHERE is_sold = 1 AND tournament_id = '$current_tournament_id'");
$sold_count = mysqli_fetch_assoc($sold_res)['total'] ?? 0;

$total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM player_master");
$total_players = mysqli_fetch_assoc($total_res)['total'] ?? 0;

// 3. TEAMS LIST
$teams_res = mysqli_query($conn, "SELECT name, team_logo FROM team_master WHERE tournament_id = '$current_tournament_id' ORDER BY team_id ASC");

// 4. TICKER DATA
$ticker_res = mysqli_query($conn, "SELECT p.name, a.points FROM auction_tracking a JOIN player_master p ON p.player_id = a.player_id WHERE a.is_sold = 1 AND a.tournament_id = '$current_tournament_id' ORDER BY a.auction_tracking_id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3"> 
    <title>LIVE AUCTION | CRICSTORM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Rajdhani:wght@500;600;700&display=swap');
        
        :root {
            --ipl-navy: #001C44;
            --ipl-gold: #d4376b;
            --ipl-blue: #00529B;
        }

        body { 
            font-family: 'Rajdhani', sans-serif; 
            background: radial-gradient(circle at center, #002d62 0%, #000b1a 100%);
            color: white; 
            overflow: hidden;
            height: 100vh;
        }

        .ipl-gradient { background: linear-gradient(90deg, #001C44 0%, #00529B 100%); }
        
        /* Glassmorphism Player Card */
        .player-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-left: 5px solid var(--ipl-gold);
        }

        /* Animated Ticker */
        .ticker-container {
            background: rgba(0, 0, 0, 0.8);
            border-top: 2px solid var(--ipl-gold);
            height: 50px;
        }

        @keyframes scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .ticker-move { animation: scroll 25s linear infinite; }

        /* Sold Stamp Style */
        .sold-banner {
            background: linear-gradient(45deg, #facc15, #ca8a04);
            transform: skewX(-15deg);
            box-shadow: 0 0 40px rgba(250, 204, 21, 0.4);
        }

        .font-sync { font-family: 'Syncopate', sans-serif; }

        /* Animation for the Sold Pop-up */
            .scale-up-center {
                animation: scale-up-center 0.5s cubic-bezier(0.390, 0.575, 0.565, 1.000) both;
            }

            @keyframes scale-up-center {
                0% { transform: scale(0.5); opacity: 0; }
                100% { transform: scale(1); opacity: 1; }
            }

            /* Enhancing the banner shine */
            .sold-banner {
                background: linear-gradient(90deg, #FFD700, #FFF9C4, #FFD700);
                background-size: 200% auto;
                animation: shine 2s linear infinite;
            }

            @keyframes shine {
                to { background-position: 200% center; }
            }
    </style>

</head>
<body class="flex flex-col">

    <?php if ($data['is_sold'] == 1): ?>
        <div id="status-overlay" class="fixed inset-0 z-[200] flex items-center justify-center bg-blue-900/90 backdrop-blur-md transition-opacity duration-1000">
            <div class="text-center scale-up-center">
                <div class="sold-banner px-24 py-8 mb-8 relative">
                    <div class="absolute -inset-2 bg-yellow-400 blur-xl opacity-50 animate-pulse"></div>
                    <h1 class="text-8xl font-black text-blue-900 skew-x-[15deg] font-sync uppercase relative">SOLD</h1>
                </div>
                <div class="space-y-4">
                    <img src="uploads/teams/<?php echo $data['team_logo']; ?>" class="h-32 mx-auto drop-shadow-[0_0_30px_rgba(255,255,255,0.5)] mb-4">
                    <h2 class="text-6xl font-bold text-white uppercase tracking-[0.2em] font-sync"><?php echo $data['team_name']; ?></h2>
                    <div class="inline-block bg-white text-blue-900 px-8 py-2 rounded-full font-black text-4xl mt-4">
                        ₹<?php echo number_format($data['points']); ?>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
        <script>
            // Massive Firework Confetti
            var duration = 5 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 300 };

            function randomInRange(min, max) { return Math.random() * (max - min) + min; }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) return clearInterval(interval);
                var particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        </script>

    <?php elseif ($data['is_skip'] == 1): ?>
        <div id="status-overlay" class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-950/95 backdrop-blur-lg">
            <div class="text-center border-y-4 border-slate-700 py-12 w-full bg-slate-900/50">
                <h1 class="text-9xl font-black text-slate-500 font-sync tracking-tighter opacity-50">UNSOLD</h1>
                <p class="text-2xl text-slate-400 uppercase tracking-[1em] mt-4">Better luck next time!</p>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Auto-hide overlay after 5 seconds to show the dashboard again
        setTimeout(() => {
            const overlay = document.getElementById('status-overlay');
            if(overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 1000);
            }
        }, 5000);
    </script>
    <nav class="w-full ipl-gradient border-b-2 border-red-400/50 p-4 flex justify-between items-center shadow-2xl">
        <div class="flex items-center gap-6">
            <div class="bg-white p- rounded">
                <img src="images/favicon.png" class="w-12 h-12 object-contain">
            </div>
            <div class="flex flex-col">
                <span class="text-white font-sync font-bold text-xl">CRIC<span class="text-red-400">STORM</span></span>
            </div>
            <div class="h-10 w-[2px] bg-white/20"></div>
            <div class="flex flex-col">
                <span class="text-xs font-bold tracking-[0.3em] text-red-500 uppercase">Live Player Auction</span>
                <span class="text-xl font-bold uppercase tracking-widest"><?php echo date('Y'); ?> Season</span>
            </div>
        </div>
        
        <div class="flex gap-4">
            <div class="text-right border-r border-white/20 pr-4">
                <p class="text-xs text-slate-400  text-md uppercase italic">Sold Count</p>
                <p class="text-2xl font-bold text-red-400"><?php echo $sold_count; ?> <span class="text-sm text-white">/ <?php echo $total_players; ?></span></p>
            </div>
            <div class="bg-red-600 px-4 py-1 flex items-center rounded animate-pulse">
                <span class="font-bold text-sm uppercase italic">Live Broadcast</span>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center px-12 gap-12">
        <div class="w-1/3 relative">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-yellow-600 to-blue-600 rounded-[2rem] blur opacity-25"></div>
                <div class="relative bg-slate-900 rounded-[2rem] overflow-hidden border-4 border-white/10">
                    <img src="<?php echo $player_photo; ?>" class="w-full aspect-square object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
                        <span class="bg-yellow-500 text-blue-900 font-bold px-3 py-1 rounded text-sm uppercase">Player ID: <?php echo $data['player_no']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-2/3 flex flex-col gap-6">
            <div class="player-card p-10 rounded-2xl">
                <h4 class="text-blue-400 font-bold uppercase tracking-[0.4em] text-sm mb-2">Currently Under the Hammer</h4>
                <h1 class="text-8xl font-black uppercase italic tracking-tighter text-white drop-shadow-lg mb-4">
                    <?php echo $data['name'] ?? 'WAITING...'; ?>
                </h1>
                
                <div class="flex gap-10 mt-6 border-t border-white/10 pt-6">
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Role</p>
                        <p class="text-3xl font-bold text-blue-400 uppercase"><?php echo $data['batsman_type'] ?: 'All Rounder'; ?></p>
                    </div>
                    <div class="h-12 w-[1px] bg-white/10"></div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Base Price</p>
                        <p class="text-3xl font-bold text-white">₹ 20,00,000</p>
                    </div>
                    <div class="h-12 w-[1px] bg-white/10"></div>
                    <div>
                        <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Hometown</p>
                        <p class="text-3xl font-bold text-white"><?php echo $data['address'] ?: 'India'; ?></p>
                    </div>
                </div>
            </div>

            <div class="flex gap-6">
                <div class="flex-grow bg-blue-600/20 border border-blue-500/50 p-6 rounded-2xl flex justify-between items-center">
                    <div>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-widest">Leading Team</p>
                        <p class="text-3xl font-black uppercase italic"><?php echo $data['team_name'] ?: 'No Bids Yet'; ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-widest">Current Bid</p>
                        <p class="text-5xl font-black text-md uppercase">₹<?php echo number_format($data['points']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="mt-auto">
        <div class="bg-white/5 py-4 flex justify-center gap-12 border-t border-white/10">
            <?php while($team = mysqli_fetch_assoc($teams_res)): ?>
                <img src="uploads/teams/<?php echo $team['team_logo']; ?>" class="h-10 grayscale hover:grayscale-0 transition-all opacity-70 hover:opacity-100 cursor-pointer" title="<?php echo $team['name']; ?>">
            <?php endwhile; ?>
        </div>

        <div class="ticker-container flex items-center overflow-hidden">
            <div class="bg-red-400 text-black font-bold px-6 h-full flex items-center z-10 skew-x-[-20deg] -ml-4">
                <span class="skew-x-[20deg] font-sync text-sm">RECENTLY SOLD</span>
            </div>
            <div class="ticker-move whitespace-nowrap flex items-center">
                <?php mysqli_data_seek($ticker_res, 0); while($row = mysqli_fetch_assoc($ticker_res)): ?>
                    <span class="mx-8 font-bold text-lg text-white">
                        <i class="fas fa-gavel text-yellow-500 mr-2"></i>
                        <?php echo strtoupper($row['name']); ?> 
                        <span class="text-#d4376b">@ ₹<?php echo number_format($row['points']); ?></span>
                    </span>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

</body>
</html>