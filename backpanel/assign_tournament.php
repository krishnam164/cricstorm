<?php
include '../config.php';

// 1. SECURITY & DATA INITIALIZATION
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
    header("Location: ../login.php"); exit();
}

// Get the User ID from the URL (?uid=12)
$target_user_id = isset($_GET['uid']) ? intval($_GET['uid']) : 0;

// --- FIX: Fetch User Data immediately ---
$user_data = ['user_fullname' => 'Unknown Staff']; // Default value to prevent "Undefined" error
if ($target_user_id > 0) {
    $user_res = mysqli_query($conn, "SELECT user_fullname FROM users WHERE user_id = '$target_user_id'");
    if ($user_res && mysqli_num_rows($user_res) > 0) {
        $user_data = mysqli_fetch_assoc($user_res);
    }
}

// 2. HANDLE ASSIGNMENT ACTION (POST)
if (isset($_POST['assign_now'])) {
    $raw_id = $_POST['auction_id'];
    
    if (strpos($raw_id, 'NEW_') !== false) {
        $t_id = str_replace('NEW_', '', $raw_id);
        mysqli_query($conn, "INSERT INTO auction_master (tournament_id, user_id) VALUES ('$t_id', '$target_user_id')");
    } else {
        $auc_id = intval($raw_id);
        mysqli_query($conn, "UPDATE auction_master SET user_id = '$target_user_id' WHERE auction_id = '$auc_id'");
    }
    header("Location: manage_staff.php?msg=assigned");
    exit();
}

include 'includes/header.php';
?>
<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-10 bg-slate-900 text-white relative">
            <h2 class="text-3xl font-black italic uppercase leading-none">Assign <span class="text-blue-500">Control</span></h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-3">Staff: <?php echo $user_data['user_fullname']; ?></p>
            <i class="fas fa-link absolute top-10 right-10 text-4xl text-white/10"></i>
        </div>

        <form method="POST" class="p-10 space-y-8">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 mb-4 block tracking-widest">Select Available Tournament</label>
                    <select name="auction_id" required class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Choose a League --</option>
                    <?php
                    // 1. Ensure the connection is global and available
                    global $conn; 

                    // 2. Double check if $conn is still null (Emergency check)
                    if (!$conn) {
                        // If config.php didn't work, try including it again with a hard path
                        include_once('../config.php'); 
                    }

                    // 3. The Optimized SQL (Tournament Master Focus)
                    $sql = "SELECT 
                                tm.tournament_id, 
                                tm.tournament_name, 
                                MAX(am.auction_id) as latest_auc_id, 
                                u.user_fullname 
                            FROM tournament_master tm
                            LEFT JOIN auction_master am ON tm.tournament_id = am.tournament_id
                            LEFT JOIN users u ON am.user_id = u.user_id 
                            GROUP BY tm.tournament_id
                            ORDER BY tm.tournament_id DESC";
                            
                    $res = mysqli_query($conn, $sql);
                    
                    if($res && mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)) {
                            // Status Logic
                            if (!empty($row['user_fullname'])) {
                                $status = " (Managed by: " . $row['user_fullname'] . ")";
                            } elseif (!empty($row['latest_auc_id'])) {
                                $status = " (FREE)";
                            } else {
                                $status = " (Ready for Setup)";
                            }

                            // Value Logic: Use Auction ID if exists, else Tournament ID
                            $val = $row['latest_auc_id'] ?: "NEW_" . $row['tournament_id'];
                            
                            echo "<option value='".$val."'>" . $row['tournament_name'] . $status . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No tournaments found or DB Error</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" name="assign_now" class="flex-grow bg-blue-600 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-900 transition-all shadow-xl">
                    Confirm Assignment
                </button>
                <a href="manage_staff.php" class="px-8 bg-slate-100 text-slate-400 flex items-center justify-center rounded-2xl hover:bg-slate-200 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>