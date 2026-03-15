<?php
include 'includes/header.php'; // In either organizer/ or backpanel/

$user_id = $_SESSION['user_id'];
$auc_id = $_GET['id'];

// Fetch the current authorized controller
$res = mysqli_query($conn, "SELECT auction_controller_id FROM auction_master WHERE auction_id = '$auc_id'");
$auc_data = mysqli_fetch_assoc($res);

$has_permission = ($auc_data['auction_controller_id'] == $user_id);
?>

<div class="p-10 bg-slate-900 rounded-[3rem]">
    <div class="flex justify-between items-center mb-10">
        <h2 class="text-white font-black italic text-2xl uppercase">Live <span class="text-orange-500">Control</span></h2>
        
        <?php if($has_permission): ?>
            <span class="bg-green-500/20 text-green-400 px-4 py-2 rounded-full text-[10px] font-black uppercase">Control Active</span>
        <?php else: ?>
            <span class="bg-rose-500/20 text-rose-400 px-4 py-2 rounded-full text-[10px] font-black uppercase">View Only Mode</span>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-2 gap-6">
        <button <?php echo !$has_permission ? 'disabled' : ''; ?> 
                class="<?php echo $has_permission ? 'bg-orange-500 hover:bg-orange-600' : 'bg-slate-700 cursor-not-allowed'; ?> py-8 rounded-3xl text-white font-black text-xl uppercase transition-all">
            Place Bid (+500)
        </button>
        
        <button <?php echo !$has_permission ? 'disabled' : ''; ?> 
                class="<?php echo $has_permission ? 'bg-teal-500 hover:bg-teal-600' : 'bg-slate-700 cursor-not-allowed'; ?> py-8 rounded-3xl text-white font-black text-xl uppercase transition-all">
            Sold Player
        </button>
    </div>

    <?php if(!$has_permission): ?>
        <p class="text-center text-slate-500 text-[10px] uppercase font-bold mt-6">
            <i class="fas fa-lock mr-2"></i> Only the Super Admin or Authorized Controller can take actions.
        </p>
    <?php endif; ?>
</div>