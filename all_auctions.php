<?php
include 'config.php';

/**
 * 1. SESSION & ROLE GATE
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
 */
if ($user_role == 'administrator' || $user_role == 'admin') {
    $query = "SELECT a.*, u.user_fullname 
              FROM auction_master a 
              LEFT JOIN users u ON a.user_id = u.user_id 
              ORDER BY a.auction_id DESC";
} else {
    $query = "SELECT a.* FROM auction_master a 
              WHERE a.user_id = '$session_user_id' 
              ORDER BY a.auction_id DESC";
}

$result = mysqli_query($conn, $query);

if (!$result) {
    die("<div style='padding:20px; background:#fff1f2; color:#be123c; font-family:sans-serif; border-radius:15px; margin:20px; border:1px solid #fda4af;'>
            <h2 style='margin-top:0; font-size:18px;'>Database Error</h2>
            <p style='font-size:14px;'>" . mysqli_error($conn) . "</p>
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

<div class="px-4 md:px-8 py-6">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="text-center md:text-left">
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic tracking-tighter uppercase">
                Auction <span class="text-orange-500">Inventory</span>
            </h2>
            <p class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-[0.3em] mt-1">
                Global Management Feed • Role: <?php echo $user_role; ?>
            </p>
        </div>
        
        <?php if($user_role != 'manager'): ?>
        <a href="create_auction.php" class="bg-slate-900 text-white text-center px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all shadow-xl active:scale-95">
            + Create Auction
        </a>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        <?php 
        if (mysqli_num_rows($result) > 0):
            while($auc = mysqli_fetch_assoc($result)): 
                $status = $auc['auction_status'] ?? 'Draft';
                $status_color = ($status == 'Live') ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-400';
        ?>
        <div class="bg-white p-6 md:p-8 rounded-[2.5rem] md:rounded-[3.5rem] border border-slate-100 shadow-sm relative group overflow-hidden hover:shadow-md transition-all">
            
            <div class="absolute top-6 right-6 md:top-8 md:right-8">
                <span class="px-3 py-1 md:px-4 md:py-1.5 <?php echo $status_color; ?> rounded-full text-[8px] md:text-[9px] font-black uppercase tracking-widest">
                    <?php echo $status; ?>
                </span>
            </div>

            <div class="w-12 h-12 md:w-14 md:h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-6">
                <i class="fas fa-gavel text-lg md:text-xl"></i>
            </div>

            <h3 class="text-lg md:text-xl font-black text-slate-900 mb-1 uppercase italic tracking-tight truncate pr-16">
                <?php echo $auc['auction_id']; ?>
            </h3>
            
            <p class="text-[9px] md:text-[10px] text-slate-400 font-bold mb-8 uppercase tracking-wide">
                <?php 
                    if(isset($auc['user_fullname'])) {
                        echo "By: <span class='text-slate-600'>" . $auc['user_fullname'] . "</span>";
                    } else {
                        echo "Tournament Ref: #" . $auc['tournament_id'];
                    }
                ?>
            </p>

            <div class="flex gap-2 pt-6 border-t border-slate-50">
                <a href="auction_monitor.php?id=<?php echo $auc['auction_id']; ?>" 
                   class="flex-grow bg-slate-900 text-white text-center py-3.5 md:py-4 rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 transition-all">
                    Open Monitor
                </a>
                
                <?php if($user_role == 'administrator' || $user_role == 'admin' || $user_role == 'organizer'): ?>
                <a href="edit_auction.php?id=<?php echo $auc['auction_id']; ?>" 
                   class="w-11 h-11 md:w-12 md:h-12 bg-slate-50 text-slate-400 rounded-xl flex-shrink-0 flex items-center justify-center hover:bg-slate-200 transition-all border border-transparent hover:border-slate-200">
                    <i class="fas fa-cog text-xs"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php 
            endwhile; 
        else: 
        ?>
        <div class="col-span-full py-16 md:py-24 bg-white rounded-[2rem] md:rounded-[4rem] border-2 border-dashed border-slate-100 text-center px-6">
            <div class="w-14 h-14 md:w-16 md:h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-folder-open text-xl"></i>
            </div>
            <h3 class="text-base md:text-lg font-black text-slate-900 mb-1">No Auctions Found</h3>
            <p class="text-slate-400 text-[9px] md:text-xs font-bold uppercase tracking-widest italic">Create a new auction to begin bidding</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
include 'includes/footer.php'; 
?>