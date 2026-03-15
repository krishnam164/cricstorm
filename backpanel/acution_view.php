<?php
include '../config.php';
// Admin Security Check...

if(isset($_POST['give_control'])) {
    $target_user = $_POST['target_user_id'];
    $auc_id = $_POST['auction_id'];
    mysqli_query($conn, "UPDATE auction_master SET auction_controller_id = '$target_user' WHERE auction_id = '$auc_id'");
    echo "Control transferred!";
}
?>

<form method="POST" class="bg-white p-8 rounded-3xl border border-teal-50 shadow-sm">
    <h3 class="font-black uppercase text-xs mb-4">Delegate Live Control</h3>
    <select name="target_user_id" class="w-full p-4 bg-slate-50 rounded-xl mb-4 font-bold">
        <option value="1">Super Admin (Take Back Control)</option>
        <?php
        $staff = mysqli_query($conn, "SELECT id, user_fullname, user_role FROM users WHERE user_role IN ('organizer', 'manager')");
        while($s = mysqli_fetch_assoc($staff)) {
            echo "<option value='".$s['id']."'>".$s['user_fullname']." (".$s['user_role'].")</option>";
        }
        ?>
    </select>
    <button name="give_control" class="bg-teal-500 text-white px-8 py-3 rounded-xl font-black uppercase text-[10px]">Update Controller</button>
</form>