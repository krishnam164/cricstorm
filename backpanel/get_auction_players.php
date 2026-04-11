<?php
include '../config.php';

// Check if the required parameters are provided
if (!isset($_GET['t_id']) || !isset($_GET['status'])) {
    echo '<p class="text-center text-slate-400 font-bold uppercase text-[9px] py-10">Invalid Request</p>';
    exit;
}

$t_id = intval($_GET['t_id']);
$status = intval($_GET['status']); // 1 for Sold, 0 for Unsold

$query = "
    SELECT p.name, p.photo, a.points, t.name as team_name
    FROM auction_tracking a
    JOIN player_master p ON a.player_id = p.player_id
    LEFT JOIN team_master t ON a.sold_team = t.team_id
    WHERE a.tournament_id = '$t_id' 
    AND a.is_sold = '$status'
";

if($status == 0) {
    $query .= " AND a.is_skip = 1"; 
}

$res = mysqli_query($conn, $query);

if($res && mysqli_num_rows($res) > 0) {
    echo '<div class="flex flex-col gap-2 md:gap-3">'; // Tighter gap for mobile
    while($row = mysqli_fetch_assoc($res)) {
        
        $sub_text = ($status == 1) ? "Sold to: <span class='text-teal-500'>".$row['team_name']."</span>" : "Unsold / Skipped";
        
        // Handle Photo Path for display inside admin folder
        $photo = !empty($row['photo']) ? "../".$row['photo'] : '../images/default_player.png';

        echo '
        <div class="flex items-center gap-3 p-3 md:p-4 bg-slate-50 rounded-xl md:rounded-2xl border border-slate-100 hover:border-teal-200 transition-colors">
            <div class="w-10 h-10 md:w-12 md:h-12 rounded-full overflow-hidden bg-white border border-slate-200 flex-shrink-0">
                <img src="'.$photo.'" class="w-full h-full object-cover" onerror="this.src=\'../images/default_player.png\'">
            </div>

            <div class="flex-grow min-w-0">
                <div class="text-xs md:text-sm font-black text-slate-800 uppercase italic tracking-tighter truncate">'.$row['name'].'</div>
                <div class="text-[8px] md:text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5 truncate">'.$sub_text.'</div>
            </div>';
            
        // Only show price if the player was sold
        if($status == 1) {
            echo '<div class="flex-shrink-0 text-[10px] md:text-xs font-black text-teal-600 bg-white px-2 md:px-3 py-1 rounded-lg border border-teal-100 shadow-sm">
                    ₹'.number_format($row['points']).'
                  </div>';
        }
        
        echo '</div>';
    }
    echo '</div>';
} else {
    $label = ($status == 1) ? "Sold" : "Unsold";
    echo '<div class="text-center py-10 px-4 md:py-12 md:px-6">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-slash text-slate-200 text-2xl"></i>
            </div>
            <p class="text-slate-400 font-bold uppercase text-[9px] md:text-[10px] tracking-[0.2em]">No '.$label.' Players Found</p>
          </div>';
}
?>