<?php
include '../config.php';

// Check if the required parameters are provided
if (!isset($_GET['t_id']) || !isset($_GET['status'])) {
    echo '<p class="text-center text-slate-400 font-bold uppercase text-[10px] py-10">Invalid Request: Missing Parameters.</p>';
    exit;
}

$t_id = intval($_GET['t_id']);
$status = intval($_GET['status']); // 1 for Sold, 0 for Unsold

// JOIN: a = auction_tracking, p = player_master, t = team_master
// We pull the player name and the team they were sold to
$query = "
    SELECT p.name, p.photo, a.points, t.name as team_name
    FROM auction_tracking a
    JOIN player_master p ON a.player_id = p.player_id
    LEFT JOIN team_master t ON a.sold_team = t.team_id
    WHERE a.tournament_id = '$t_id' 
    AND a.is_sold = '$status'
";

// If checking for unsold, ensure we only pull players who were actually skipped
if($status == 0) {
    $query .= " AND a.is_skip = 1"; 
}

$res = mysqli_query($conn, $query);

if($res && mysqli_num_rows($res) > 0) {
    echo '<div class="flex flex-col gap-3">';
    while($row = mysqli_fetch_assoc($res)) {
        // Clean team name for unsold players
        $sub_text = ($status == 1) ? "Sold to: <span class='text-blue-500'>".$row['team_name']."</span>" : "Unsold / Skipped";
        
        echo '
        <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-200 transition-colors">
            <div class="flex-grow">
                <div class="text-sm font-black text-slate-800 uppercase italic tracking-tighter">'.$row['name'].'</div>
                <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">'.$sub_text.'</div>
            </div>';
            
        // Only show price if the player was sold
        if($status == 1) {
            echo '<div class="text-xs font-black text-teal-600 bg-teal-50 px-3 py-1 rounded-lg border border-teal-100">
                    ₹'.number_format($row['points']).'
                  </div>';
        }
        
        echo '</div>';
    }
    echo '</div>';
} else {
    $label = ($status == 1) ? "Sold" : "Unsold";
    echo '<div class="text-center py-12 px-6">
            <i class="fas fa-user-slash text-slate-200 text-3xl mb-4"></i>
            <p class="text-slate-400 font-bold uppercase text-[10px] tracking-[0.2em]">No '.$label.' Players Found for this Tournament</p>
          </div>';
}
?>