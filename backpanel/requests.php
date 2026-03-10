<?php
session_start();
include '../config.php';

// 1. SUPER ADMIN SECURITY GATE
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
    header("Location: ../login.php"); 
    exit();
}

$message = "";

// 2. HANDLE ADMIN ACTIONS (Accept/Reject)
if (isset($_GET['action']) && isset($_GET['uid'])) {
    $uid = intval($_GET['uid']);
    $action = $_GET['action'];
    
    if ($action == 'accept') {
        $sql = "UPDATE users SET organizer_request = 'Accepted' WHERE user_id = '$uid'";
        $message = "Organizer access granted successfully!";
    } else {
        $sql = "UPDATE users SET organizer_request = 'Rejected' WHERE user_id = '$uid'";
        $message = "Request has been declined.";
    }
    mysqli_query($conn, $sql);
}

// 3. FETCH PENDING REQUESTS
$requests = mysqli_query($conn, "SELECT * FROM users WHERE organizer_request = 'Pending' ORDER BY user_id DESC");

$active_page = 'requests';
include 'includes/header.php';
?>

<div class="mb-12">
    <h2 class="text-2xl font-black text-slate-900 italic uppercase">Access <span class="text-indigo-600">Permissions</span></h2>
    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Manage Organizer Requests for Global Monitor</p>
</div>

<?php if($message): ?>
    <div class="mb-8 p-5 bg-indigo-50 text-indigo-700 rounded-3xl border border-indigo-100 font-bold text-sm flex items-center gap-3">
        <i class="fas fa-info-circle"></i> <?php echo $message; ?>
    </div>
<?php endif; ?>



<div class="bg-white rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50">
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Organizer Info</th>
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact</th>
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php if(mysqli_num_rows($requests) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($requests)): ?>
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="px-8 py-6">
                        <p class="font-black text-slate-800"><?php echo $row['user_fullname']; ?></p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase">ID: #USR-<?php echo $row['user_id']; ?></p>
                    </td>
                    <td class="px-8 py-6 text-sm font-bold text-slate-600">
                        <?php echo $row['user_mobile']; ?>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex justify-center gap-3">
                            <a href="?action=accept&uid=<?php echo $row['user_id']; ?>" class="bg-teal-500 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-teal-600 shadow-lg shadow-teal-100 transition-all">
                                Accept
                            </a>
                            <a href="?action=reject&uid=<?php echo $row['user_id']; ?>" class="bg-rose-50 text-rose-500 px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">
                                Cancel
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="p-20 text-center">
                        <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-2xl"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No pending requests at this time</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>