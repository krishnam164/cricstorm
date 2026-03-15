<?php
include '../config.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];

// Handle Sending Request
if (isset($_POST['send_request'])) {

    $res = mysqli_query($conn, "UPDATE users SET organizer_request = 'Pending' WHERE user_id = '$user_id'");

    if(!$res){
        die("Query Failed: " . mysqli_error($conn));
    }

    header("Location: request_access.php");
    exit();
}

// Check current status
$res = mysqli_query($conn, "SELECT organizer_request FROM users WHERE user_id = '$user_id'");

if(!$res){
    die("Query Failed: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($res);
$status = $row['organizer_request'] ?? 'None';

include 'includes/header.php';
?>
<div class="max-w-xl mx-auto mt-20">
    <div class="bg-white p-12 rounded-[3rem] shadow-xl border border-orange-50 text-center">
        <?php if($status == 'Accepted'): ?>
            <div class="w-20 h-20 bg-green-50 text-green-500 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-check-double text-3xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-2 uppercase">Access <span class="text-green-500">Active</span></h2>
            <p class="text-slate-400 text-xs font-bold mb-8 uppercase tracking-widest">You have permanent monitor permissions.</p>
            <a href="all_tournaments.php" class="bg-slate-900 text-white px-10 py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest">Enter Dashboard</a>
            
        <?php elseif($status == 'Pending'): ?>
            <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-hourglass-half text-3xl animate-spin-slow"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-4 uppercase">Request <span class="text-blue-500">Pending</span></h2>
            <p class="text-slate-400 text-sm">The Super Admin is reviewing your access request.</p>
            
        <?php else: ?>
            <div class="w-20 h-20 bg-orange-50 text-orange-500 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <i class="fas fa-lock text-3xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 mb-4 uppercase">Monitor <span class="text-orange-500">Locked</span></h2>
            <form method="POST">
                <button name="send_request" class="bg-slate-900 text-white px-10 py-5 rounded-2xl font-black uppercase text-[10px]">Send Access Request</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include '../backpanel/includes/footer.php'; ?>