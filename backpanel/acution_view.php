<?php
include '../config.php';
// Admin Security Check...

if(isset($_POST['give_control'])) {
    $target_user = mysqli_real_escape_string($conn, $_POST['target_user_id']);
    $auc_id = mysqli_real_escape_string($conn, $_POST['auction_id']);
    
    $update = mysqli_query($conn, "UPDATE auction_master SET auction_controller_id = '$target_user' WHERE auction_id = '$auc_id'");
    
    if($update) {
        $msg = "Control transferred successfully!";
    }
}
?>

<div class="max-w-2xl">
    <form method="POST" class="bg-white p-6 md:p-8 rounded-2xl md:rounded-[2rem] border border-teal-50 shadow-sm relative overflow-hidden">
        
        <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
            <i class="fas fa-tower-broadcast text-6xl text-teal-500"></i>
        </div>

        <div class="relative z-10">
            <h3 class="font-black text-slate-900 uppercase text-[10px] md:text-xs mb-1 tracking-[0.2em]">Delegate Live Control</h3>
            <p class="text-slate-400 text-[9px] font-bold uppercase mb-6">Assign an official to manage the hammer logic</p>

            <?php if(isset($msg)): ?>
                <div class="mb-6 p-4 bg-teal-50 text-teal-600 rounded-xl text-[10px] font-black uppercase flex items-center gap-3 border border-teal-100">
                    <i class="fas fa-check-circle"></i> <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-slate-400 uppercase ml-2 tracking-widest">Select Controller</label>
                    <select name="target_user_id" class="w-full p-4 md:p-5 bg-slate-50 rounded-xl md:rounded-2xl border-none font-bold text-slate-700 focus:ring-2 focus:ring-teal-500 transition-all outline-none appearance-none cursor-pointer">
                        <option value="1">Super Admin (System Control)</option>
                        <?php
                        $staff = mysqli_query($conn, "SELECT user_id, user_fullname, user_role FROM users WHERE user_role IN ('organizer', 'manager') AND user_status = 'Publish'");
                        while($s = mysqli_fetch_assoc($staff)) {
                            echo "<option value='".$s['user_id']."'>".$s['user_fullname']." (".ucfirst($s['user_role']).")</option>";
                        }
                        ?>
                    </select>
                </div>

                <input type="hidden" name="auction_id" value="<?php echo $_GET['id'] ?? '1'; ?>">

                <button type="submit" name="give_control" class="w-full md:w-auto bg-slate-900 text-white px-10 py-4 rounded-xl md:rounded-2xl font-black uppercase text-[10px] md:text-xs tracking-widest hover:bg-teal-500 transition-all shadow-lg active:scale-95 flex items-center justify-center gap-3">
                    <i class="fas fa-sync-alt text-[10px]"></i> Update Controller
                </button>
            </div>
        </div>
    </form>
</div>