<?php
include '../config.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];

// 1. Handle Sending Request
if (isset($_POST['send_request'])) {
    $res = mysqli_query($conn, "UPDATE users SET manager_request = 'Pending' WHERE user_id = '$user_id'");
    if(!$res){ die("Query Failed: " . mysqli_error($conn)); }
    header("Location: request_access.php");
    exit();
}

// 2. Fetch current status
$res = mysqli_query($conn, "SELECT manager_request FROM users WHERE user_id = '$user_id'");
if(!$res){ die("Query Failed: " . mysqli_error($conn)); }

$row = mysqli_fetch_assoc($res);
$status = isset($row['manager_request']) ? $row['manager_request'] : 'None';
$msg = isset($_GET['msg']) ? $_GET['msg'] : ''; 

include 'includes/header.php';
?>

<div class="max-w-xl mx-auto mt-8 md:mt-20 px-4 pb-12">
    
    <?php if($msg == 'denied'): ?>
        <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-2xl text-[10px] md:text-[11px] font-black uppercase text-center border border-red-100 animate__animated animate__shakeX">
            <i class="fas fa-exclamation-triangle mr-2"></i> Action Locked: Admin approval required
        </div>
    <?php endif; ?>

    <div class="bg-white p-8 md:p-12 rounded-[2rem] md:rounded-[3rem] shadow-xl border border-orange-50 text-center">
        
        <?php if($status == 'Accepted'): ?>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-green-50 text-green-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8 shadow-lg shadow-green-100">
                <i class="fas fa-check-double text-2xl md:text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 mb-2 uppercase tracking-tighter">Monitor <span class="text-green-500">Unlocked</span></h2>
            <p class="text-slate-400 text-[10px] font-bold mb-8 uppercase tracking-widest leading-relaxed">
                Your request has been approved.<br>You now have full auction control.
            </p>
            <a href="auction_controller_manager.php" class="block w-full md:inline-block bg-slate-900 text-white px-10 py-5 rounded-xl md:rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-orange-500 transition-all shadow-xl active:scale-95">
                Open Auction Console
            </a>
            
        <?php elseif($status == 'Pending'): ?>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-blue-50 text-blue-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8">
                <i class="fas fa-hourglass-half text-2xl md:text-3xl animate-pulse"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 mb-4 uppercase tracking-tighter">Waiting for <span class="text-blue-500">Admin</span></h2>
            <p class="text-slate-400 text-xs md:text-sm font-medium leading-relaxed">Your request is in the queue. Please wait for the Super Admin to authorize your session.</p>
            
        <?php elseif($status == 'Rejected'): ?>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-red-50 text-red-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8">
                <i class="fas fa-times-circle text-2xl md:text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 mb-4 uppercase tracking-tighter">Access <span class="text-red-500">Rejected</span></h2>
            <p class="text-slate-400 text-xs md:text-sm mb-6 font-medium leading-relaxed">Your request was declined. Contact the administrator for more information.</p>
            <form method="POST">
                <button name="send_request" class="text-[10px] font-black uppercase text-slate-400 border-b border-slate-200 hover:text-orange-500 active:scale-95 transition-all">Try Resubmitting</button>
            </form>

        <?php else: ?>
            <div class="w-16 h-16 md:w-20 md:h-20 bg-orange-50 text-orange-500 rounded-2xl md:rounded-3xl flex items-center justify-center mx-auto mb-6 md:mb-8">
                <i class="fas fa-lock text-2xl md:text-3xl"></i>
            </div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 mb-4 uppercase tracking-tighter">Control <span class="text-orange-500">Restricted</span></h2>
            <p class="text-slate-400 text-xs md:text-sm mb-8 font-medium leading-relaxed">You need Super Admin permission to access the live auction monitor for this league.</p>
            <form method="POST">
                <button name="send_request" class="w-full md:w-auto bg-slate-900 text-white px-10 py-5 rounded-xl md:rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-orange-500 shadow-lg transition-all active:scale-95">
                    Request Monitor Access
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../backpanel/includes/footer.php'; ?>