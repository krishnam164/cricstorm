<?php
include 'config.php';

/**
 * 1. SESSION & ROLE GATE
 * Ensures only Staff (Admin, Manager, Organizer) can access this page.
 */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: login.php?msg=session_expired");
    exit();
}

$session_user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$user_role = strtolower($_SESSION['user_role']);
$allowed_roles = ['administrator', 'manager', 'organizer', 'admin'];

if (!in_array($user_role, $allowed_roles)) {
    header("Location: login.php?msg=unauthorized");
    exit();
}

/**
 * 2. DATABASE QUERY LOGIC
 * Corrected to use 'user_id' based on your database structure.
 */
if ($user_role == 'administrator' || $user_role == 'admin') {
    // Admin View: Sees all auctions + names of the organizers
    $query = "SELECT a.*, u.user_fullname 
              FROM auction_master a 
              LEFT JOIN users u ON a.user_id = u.user_id 
              ORDER BY a.auction_id DESC";
} else {
    // Organizer/Manager View: Sees only auctions they created
    $query = "SELECT a.* FROM auction_master a 
              WHERE a.user_id = '$session_user_id' 
              ORDER BY a.auction_id DESC";
}

$result = mysqli_query($conn, $query);

// Safety Check: If query fails, don't crash, show a clean error.
if (!$result) {
    die("<div style='padding:40px; background:#fff1f2; color:#be123c; font-family:sans-serif; border-radius:20px; margin:50px; border:1px solid #fda4af;'>
            <h2 style='margin-top:0;'>Database Error</h2>
            <p>The query failed because: <b>" . mysqli_error($conn) . "</b></p>
            <p>Please ensure the column <b>user_id</b> exists in both <i>auction_master</i> and <i>users</i> tables.</p>
         </div>");
}

/**
 * 3. DYNAMIC HEADER LOADING
 */
if ($user_role == 'organizer') {
    include 'organizer/includes/header.php';
} else {
    include 'backpanel/includes/header.php';
}
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic tracking-tighter uppercase">
            Auction <span class="text-orange-500">Inventory</span>
        </h2>
        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-1">
            Global Management Feed • Role: <?php echo $user_role; ?>
        </p>
    </div>
    
    <?php if($user_role != 'manager'): // Managers usually just observe ?>
    <a href="create_auction.php" class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all shadow-xl">
        + Create Auction
    </a>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php 
    if (mysqli_num_rows($result) > 0):
        while($auc = mysqli_fetch_assoc($result)): 
            $status = $auc['auction_status'] ?? 'Draft';
            $status_color = ($status == 'Live') ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-400';
    ?>
    <div class="bg-white p-8 rounded-[3.5rem] border border-slate-100 shadow-sm relative group overflow-hidden hover:shadow-md transition-all">
        <div class="absolute top-8 right-8">
            <span class="px-4 py-1.5 <?php echo $status_color; ?> rounded-full text-[9px] font-black uppercase tracking-widest">
                <?php echo $status; ?>
            </span>
        </div>

        <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-6">
            <i class="fas fa-gavel text-xl"></i>
        </div>

        <h3 class="text-xl font-black text-slate-900 mb-1 uppercase italic tracking-tight">
            <?php echo $auc['auction_id']; ?>
        </h3>
        
        <p class="text-[10px] text-slate-400 font-bold mb-8 uppercase tracking-wide">
            <?php 
                if(isset($auc['user_fullname'])) {
                    echo "Organized by: <span class='text-slate-600'>" . $auc['user_fullname'] . "</span>";
                } else {
                    echo "Tournament Ref: #" . $auc['tournament_id'];
                }
            ?>
        </p>

        <div class="flex gap-2 pt-6 border-t border-slate-50">
            <a href="auction_monitor.php?id=<?php echo $auc['auction_id']; ?>" 
               class="flex-grow bg-slate-900 text-white text-center py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all">
                Open Monitor
            </a>
            
            <?php if($user_role == 'administrator' || $user_role == 'admin' || $user_role == 'organizer'): ?>
            <a href="edit_auction.php?id=<?php echo $auc['auction_id']; ?>" 
               class="w-12 h-12 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-all border border-transparent hover:border-slate-200">
                <i class="fas fa-cog text-xs"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php 
        endwhile; 
    else: 
    ?>
    <div class="col-span-full py-24 bg-white rounded-[4rem] border-2 border-dashed border-slate-100 text-center">
        <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-folder-open text-xl"></i>
        </div>
        <h3 class="text-lg font-black text-slate-900 mb-1">No Auctions Found</h3>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest italic">Create a new auction to begin bidding</p>
    </div>
    <?php endif; ?>
</div>

<?php 
// Standardized Footer
include 'includes/footer.php'; 
?>