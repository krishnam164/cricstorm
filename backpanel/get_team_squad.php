<?php
include '../config.php';

if (!isset($_GET['team_id'])) exit;

$team_id = intval($_GET['team_id']);

$query = "SELECT p.name, p.photo, a.points, p.batsman_type
          FROM auction_tracking a
          JOIN player_master p ON a.player_id = p.player_id
          WHERE a.sold_team = '$team_id' AND a.is_sold = 1
          ORDER BY a.auction_tracking_id ASC";

$res = mysqli_query($conn, $query);

if(mysqli_num_rows($res) > 0) {
    echo '<div class="grid grid-cols-1 gap-2">';
    while($row = mysqli_fetch_assoc($res)) {
        
        // --- START OF YOUR LOGIC ---
        $photo_name = ($row['photo'] ?? 'default.png');

        if (!empty($row['photo']) && file_exists('../' . $photo_name)) {
            $player_photo = '../' . $photo_name;
        } else {
            // Your fallback logic
            $player_photo = '../uploads/tournaments/default.png'; 
        }
        // --- END OF YOUR LOGIC ---

        echo '
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-slate-200 overflow-hidden border border-slate-100">
                    <img src="'.$player_photo.'" class="w-full h-full object-cover">
                </div>
                <div>
                    <p class="text-[11px] font-black text-slate-800 uppercase tracking-tighter">'.$row['name'].'</p>
                    <p class="text-[8px] font-bold text-slate-400 uppercase">'.$row['batsman_type'].'</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-teal-600 bg-white px-2 py-1 rounded-lg border border-slate-100">
                    ₹'.number_format($row['points']).'
                </p>
            </div>
        </div>';
    }
    echo '</div>';
} else {
    echo '<div class="py-10 text-center"><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No players in squad yet</p></div>';
}
?>