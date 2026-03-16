<?php
include 'config.php';

// 1. GET ACTIVE TOURNAMENT
$t_query = mysqli_query($conn, "SELECT tournament_id FROM auction_tracking ORDER BY auction_tracking_id DESC LIMIT 1");
$t_data = mysqli_fetch_assoc($t_query);
$active_tid = $t_data['tournament_id'] ?? 0;

// 2. FETCH TEAM SUMMARY (Purse Spent & Player Count)
$teams_summary_query = "
    SELECT 
        t.team_id, t.name, t.logo,
        IFNULL(SUM(a.points), 0) as total_spent,
        COUNT(a.player_id) as players_bought
    FROM team_master t
    LEFT JOIN auction_tracking a ON t.team_id = a.sold_team AND a.is_sold = 1 AND a.tournament_id = '$active_tid'
    WHERE t.tournament_id = '$active_tid'
    GROUP BY t.team_id
";
$teams_res = mysqli_query($conn, $teams_summary_query);

// 3. RECENT SALES LOG
$history_query = "
    SELECT p.name as player_name, p.photo, t.name as team_name, a.points, a.auction_tracking_datetime
    FROM auction_tracking a
    JOIN player_master p ON a.player_id = p.player_id
    JOIN team_master t ON a.sold_team = t.team_id
    WHERE a.is_sold = 1 AND a.tournament_id = '$active_tid'
    ORDER BY a.auction_tracking_id DESC LIMIT 10
";
$history_res = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5">
    <title>Auction Monitor | CricStrome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: white; }
        .card-glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.05); }
    </style>
</head>
<body class="p-6">

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tighter">Auction <span class="text-orange-500">Monitor</span></h1>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Real-time Squad & Purse Tracking</p>
        </div>
        <div class="bg-orange-600 px-6 py-2 rounded-xl font-bold italic">TOURNAMENT #<?php echo $active_tid; ?></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <h2 class="text-sm font-black uppercase text-slate-500 tracking-widest flex items-center gap-2">
                <i class="fas fa-wallet text-orange-500"></i> Team Financial Status
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php while($team = mysqli_fetch_assoc($teams_res)): ?>
                <div class="card-glass p-6 rounded-[2rem] flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="uploads/teams/<?php echo $team['logo']; ?>" class="w-12 h-12 object-contain bg-white/5 rounded-full p-1">
                        <div>
                            <h3 class="font-black uppercase text-sm leading-tight"><?php echo $team['name']; ?></h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase"><?php echo $team['players_bought']; ?> Players</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-500 uppercase">Purse Spent</p>
                        <p class="text-xl font-black text-teal-400">₹<?php echo number_format($team['total_spent']); ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="space-y-6">
            <h2 class="text-sm font-black uppercase text-slate-500 tracking-widest flex items-center gap-2">
                <i class="fas fa-history text-orange-500"></i> Recent Sales
            </h2>
            <div class="card-glass rounded-[2rem] overflow-hidden">
                <div class="max-h-[600px] overflow-y-auto">
                    <?php if(mysqli_num_rows($history_res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($history_res)): ?>
                        <div class="p-4 border-b border-white/5 flex items-center gap-4 hover:bg-white/5 transition-all">
                            <img src="uploads/players/<?php echo $row['photo']; ?>" class="w-10 h-10 rounded-full object-cover border border-orange-500/30">
                            <div class="flex-grow">
                                <h4 class="text-xs font-black uppercase"><?php echo $row['player_name']; ?></h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase"><?php echo $row['team_name']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-orange-500">₹<?php echo number_format($row['points']); ?></p>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="p-10 text-center text-slate-500 text-xs font-bold uppercase">No players sold yet</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card-glass p-6 rounded-2xl text-center">
            <p class="text-[9px] font-black text-slate-500 uppercase">Top Buy</p>
            <?php 
                $top = mysqli_query($conn, "SELECT points FROM auction_tracking WHERE is_sold = 1 AND tournament_id = '$active_tid' ORDER BY points DESC LIMIT 1");
                $top_val = mysqli_fetch_assoc($top)['points'] ?? 0;
            ?>
            <p class="text-xl font-black text-yellow-500">₹<?php echo number_format($top_val); ?></p>
        </div>
    </div>

</body>
</html>