<?php
include '../config.php';

// Get the JSON data from the fetch request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['player_id'])) {
    $p_id = intval($data['player_id']);
    $points = intval($data['points']);
    $t_id = intval($data['tournament_id']);
    $team_id = intval($data['team_id']);

    // --- PART 1: UPDATE LIVE SYNC TABLE (For Instant Dashboard Update) ---
    // This is the most important part for the live view
    $sync_sql = "UPDATE auction_live_sync SET 
                tournament_id = '$t_id', 
                player_id = '$p_id', 
                current_bid = '$points', 
                team_id = '$team_id' 
                WHERE sync_id = 1";
    mysqli_query($conn, $sync_sql);


    // --- PART 2: UPDATE TRACKING TABLE (For Data Logging) ---
    // Check if the most recent row for this tournament is an active bid
    $check = mysqli_query($conn, "SELECT auction_tracking_id FROM auction_tracking 
                                  WHERE tournament_id = '$t_id' 
                                  AND is_sold = 0 AND is_skip = 0 
                                  ORDER BY auction_tracking_id DESC LIMIT 1");
    
    $row = mysqli_fetch_assoc($check);
    
    if ($row) {
        // Update the existing active bid row
        $last_id = $row['auction_tracking_id'];
        $sql = "UPDATE auction_tracking SET 
                player_id = '$p_id', 
                points = '$points', 
                sold_team = '$team_id'
                WHERE auction_tracking_id = '$last_id'";
    } else {
        // Start a new tracking row if none exists
        $sql = "INSERT INTO auction_tracking (player_id, points, tournament_id, sold_team, is_sold, is_skip, auction_tracking_datetime) 
                VALUES ('$p_id', '$points', '$t_id', '$team_id', 0, 0, NOW())";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    exit;
}
?>