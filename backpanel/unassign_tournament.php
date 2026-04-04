<?php
include '../config.php';

if (isset($_GET['auc_id'])) {
    $auc_id = intval($_GET['auc_id']);
    
    // Reset the user_id to 0 to "unassign" it
    $sql = "UPDATE auction_master SET user_id = 0 WHERE auction_id = '$auc_id'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: manage_staff.php?msg=unassigned");
    } else {
        header("Location: manage_staff.php?msg=error");
    }
}
exit();