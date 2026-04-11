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
        $sql = "UPDATE users SET manager_request = 'Accepted', user_role = 'manager' WHERE user_id = '$uid'";
        $message = "Manager access granted successfully!";
    } else {
        $sql = "UPDATE users SET manager_request = 'Rejected' WHERE user_id = '$uid'";
        $message = "Request has been declined.";
    }
    mysqli_query($conn, $sql);
}

// 3. FETCH PENDING REQUESTS
$requests = mysqli_query($conn, "SELECT * FROM users WHERE manager_request = 'Pending' ORDER BY user_id DESC");

$active_page = 'requests';
include 'includes/header.php';
?>

<div class="mb-8 md:mb-12 px-2 md:px-0 text-center md:text-left">
    <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic uppercase tracking-tighter">
        Access <span class="text-indigo-600">Permissions</span>
    </h2>
    <p class="text-[9px] md:text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Manage Manager Requests for Global Monitor</p>
</div>

<?php if($message): ?>
    <div class="mx-2 mb-8 p-4 md:p-5 bg-indigo-50 text-indigo-700 rounded-2xl md:rounded-3xl border border-indigo-100 font-bold text-xs md:text-sm flex items-center gap-3 animate__animated animate__fadeIn">
        <i class="fas fa-info-circle shrink-0"></i> <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[1.5rem] md:rounded-[3rem] border border-teal-50 shadow-sm overflow-hidden mb-12 mx-2 md:mx-0">
    <div class="overflow-x-auto scrollbar-hide">
        <table class="w-full text-left min-w-[600px]">
            <thead>
                <tr class="bg-slate-50">
                    <th class="px-6 md:px-8 py-4 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Manager Info</th>
                    <th class="px-6 md:px-8 py-4 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact</th>
                    <th class="px-6 md:px-8 py-4 md:py-6 text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Operations</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if(mysqli_num_rows($requests) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($requests)): ?>
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-6 md:px-8 py-5 md:py-6">
                            <p class="font-black text-slate-800 text-sm md:text-base uppercase italic truncate"><?php echo $row['user_fullname']; ?></p>
                            <p class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-tighter">ID: #USR-<?php echo $row['user_id']; ?></p>
                        </td>
                        <td class="px-6 md:px-8 py-5 md:py-6">
                            <div class="flex flex-col">
                                <span class="text-xs md:text-sm font-bold text-slate-600"><?php echo $row['user_mobile']; ?></span>
                                <span class="text-[8px] md:text-[9px] text-slate-400 font-medium lowercase truncate max-w-[150px]"><?php echo $row['user_email'] ?? 'No email logged'; ?></span>
                            </div>
                        </td>
                        <td class="px-6 md:px-8 py-5 md:py-6">
                            <div class="flex justify-center gap-2 md:gap-3">
                                <a href="?action=accept&uid=<?php echo $row['user_id']; ?>" 
                                   class="bg-teal-500 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-lg md:rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:bg-teal-600 shadow-md active:scale-95 transition-all">
                                    Accept
                                </a>
                                <a href="?action=reject&uid=<?php echo $row['user_id']; ?>" 
                                   class="bg-rose-50 text-rose-500 px-4 md:px-6 py-2 md:py-2.5 rounded-lg md:rounded-xl text-[9px] md:text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white active:scale-95 transition-all">
                                    Cancel
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="p-16 md:p-24 text-center">
                            <div class="w-12 h-12 md:w-16 md:h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-inbox text-xl md:text-2xl"></i>
                            </div>
                            <p class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">No pending requests at this time</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>