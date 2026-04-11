<?php
include '../config.php';

if (!isset($_GET['team_id'])) exit;

$team_id = intval($_GET['team_id']);

// JOIN: Pulling player data based on tracking records
$query = "SELECT p.name, p.photo, a.points, p.batsman_type, p.category
          FROM auction_tracking a
          JOIN player_master p ON a.player_id = p.player_id
          WHERE a.sold_team = '$team_id' AND a.is_sold = 1
          ORDER BY a.auction_tracking_id ASC";

$res = mysqli_query($conn, $query);

if(mysqli_num_rows($res) > 0) {
    // flex-col for a vertical list that fits mobile viewports perfectly
    echo '<div class="flex flex-col gap-2">';
    while($row = mysqli_fetch_assoc($res)) {
        
        // --- PHOTO LOGIC ---
        $photo_name = ($row['photo'] ?? '');
        $player_photo = (!empty($photo_name) && file_exists('../' . $photo_name)) 
                        ? '../' . $photo_name 
                        : '../uploads/tournaments/default.png';

        echo '
        <div class="flex items-center justify-between p-2.5 md:p-3 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-teal-200 transition-colors">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 md:w-11 md:h-11 rounded-lg bg-slate-100 overflow-hidden border border-slate-50 flex-shrink-0">
                    <img src="'.$player_photo.'" class="w-full h-full object-cover" onerror="this.src=\'../uploads/tournaments/default.png\'">
                </div>
                
                <div class="min-w-0">
                    <p class="text-[11px] md:text-xs font-black text-slate-800 uppercase italic tracking-tighter truncate">'.$row['name'].'</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-wide">'.($row['category'] ?? $row['batsman_type']).'</p>
                </div>
            </div>

            <div class="flex-shrink-0 ml-2">
                <p class="text-[9px] md:text-[10px] font-black text-teal-600 bg-teal-50/50 px-2.5 py-1 rounded-lg border border-teal-100 shadow-sm">
                    ₹'.number_format($row['points']).'
                </p>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo '
    <div class="py-12 text-center">
        <div class="w-14 h-14 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users-slash text-slate-200 text-xl"></i>
        </div>
        <p class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Squad is currently empty</p>
    </div>';
}
?>