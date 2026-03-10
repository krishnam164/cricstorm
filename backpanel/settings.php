<?php
include '../config.php';

/** * 1. MASTER SECURITY GATE
 * Verifies the Super Admin session against the 'user_master' table.
 */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$admin_mobile = $_SESSION['admin_mobile'] ?? 'System Admin';
$message = "";

/** * 2. COMMAND CENTER UPDATE LOGIC
 * Processes the high-end dashboard form inputs.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_platform'])) {
    $min_bid = mysqli_real_escape_string($conn, $_POST['min_bid']);
    $timer = mysqli_real_escape_string($conn, $_POST['timer']);
    $m_mode = mysqli_real_escape_string($conn, $_POST['m_mode']);
    $bid_status = mysqli_real_escape_string($conn, $_POST['bid_status']);
    $conv_rate = mysqli_real_escape_string($conn, $_POST['conv_rate']);

    $sql = "UPDATE settings_master SET 
            min_bid_increment = '$min_bid', 
            auction_timer = '$timer', 
            maintenance_mode = '$m_mode',
            bidding_status = '$bid_status',
            conversion_rate = '$conv_rate' 
            WHERE id = 1";
            
    if (mysqli_query($conn, $sql)) {
        $message = "Command Center Configuration Updated Successfully!";
    }
}

/** * 3. SAFE DATA FETCHING
 * Uses null coalescing to prevent "Undefined Key" errors.
 */
$settings_query = mysqli_query($conn, "SELECT * FROM settings_master WHERE id = 1 LIMIT 1");
$st = mysqli_fetch_assoc($settings_query) ?? [];

$active_page = 'settings';
include 'includes/header.php'; 
?>

<div class="mb-12 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-black text-slate-900 italic tracking-tight">Command <span class="text-indigo-600">Center</span></h2>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Global Infrastructure Oversight</p>
    </div>
    <div class="hidden md:flex gap-3">
        <div class="bg-white px-6 py-3 rounded-2xl border border-teal-50 shadow-sm flex items-center gap-3">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
            <span class="text-[10px] font-black text-slate-700 uppercase">System: Operational</span>
        </div>
    </div>
</div>

<?php if($message): ?>
    <div class="mb-10 p-6 bg-indigo-600 text-white rounded-[2.5rem] shadow-xl shadow-indigo-200 flex items-center gap-5 animate-bounce">
        <i class="fas fa-check-circle text-2xl"></i>
        <span class="font-bold tracking-wide"><?php echo $message; ?></span>
    </div>
<?php endif; ?>



<form method="POST">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
        
        <div class="bg-white p-10 rounded-[3.5rem] border border-teal-50 shadow-2xl shadow-teal-900/5 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 transition-all group-hover:scale-110"></div>
            <i class="fas fa-gavel text-3xl text-indigo-600 mb-6 relative"></i>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-2 relative">Bidding Engine</h3>
            <p class="text-[11px] text-slate-400 font-bold mb-8 relative">Global transaction kill-switch</p>
            <select name="bid_status" class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-[2rem] font-black text-slate-700 appearance-none focus:border-indigo-500 focus:bg-white transition-all cursor-pointer relative">
                <option value="Enabled" <?php echo (($st['bidding_status'] ?? 'Enabled') == 'Enabled') ? 'selected' : ''; ?>>LIVE / ENABLED</option>
                <option value="Disabled" <?php echo (($st['bidding_status'] ?? 'Enabled') == 'Disabled') ? 'selected' : ''; ?>>FREEZE / DISABLED</option>
            </select>
        </div>

        <div class="bg-white p-10 rounded-[3.5rem] border border-teal-50 shadow-2xl shadow-teal-900/5 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full -mr-16 -mt-16 transition-all group-hover:scale-110"></div>
            <i class="fas fa-tools text-3xl text-rose-500 mb-6 relative"></i>
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest mb-2 relative">Gatekeeper</h3>
            <p class="text-[11px] text-slate-400 font-bold mb-8 relative">Platform access control</p>
            <select name="m_mode" class="w-full px-8 py-5 bg-slate-50 border-2 border-transparent rounded-[2rem] font-black text-slate-700 appearance-none focus:border-rose-500 focus:bg-white transition-all cursor-pointer relative">
                <option value="Off" <?php echo (($st['maintenance_mode'] ?? 'Off') == 'Off') ? 'selected' : ''; ?>>PUBLIC / LIVE</option>
                <option value="On" <?php echo (($st['maintenance_mode'] ?? 'Off') == 'On') ? 'selected' : ''; ?>>ADMIN / MAINTENANCE</option>
            </select>
        </div>

        <div class="bg-[#0F172A] p-10 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-teal-500/10 rounded-full blur-3xl"></div>
            <i class="fas fa-server text-3xl text-teal-400 mb-6"></i>
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Core Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-end border-b border-white/5 pb-3">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Rows</span>
                    <span class="text-lg font-black text-teal-400">21,213</span>
                </div>
                <div class="flex justify-between items-end">
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Data Size</span>
                    <span class="text-lg font-black">2.2 MiB</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
        <div class="bg-white p-12 rounded-[4rem] border border-teal-50 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-10">Economic Variables</h4>
            <div class="space-y-8">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2">Min. Bid Increment (₹)</label>
                    <input type="number" name="min_bid" value="<?php echo $st['min_bid_increment'] ?? 1000; ?>" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] font-black text-slate-800 focus:bg-white focus:border-indigo-500 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2">Conversion Rate (1 Point = ₹)</label>
                    <input type="number" step="0.01" name="conv_rate" value="<?php echo $st['conversion_rate'] ?? 1.00; ?>" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] font-black text-slate-800 focus:bg-white focus:border-indigo-500 transition-all outline-none">
                </div>
            </div>
        </div>

        <div class="bg-white p-12 rounded-[4rem] border border-teal-50 shadow-sm">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-10">Timing Engine</h4>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2">Auction Timer (Seconds)</label>
                <input type="number" name="timer" value="<?php echo $st['auction_timer'] ?? 30; ?>" class="w-full px-8 py-5 bg-slate-50 border border-slate-100 rounded-[2rem] font-black text-slate-800 focus:bg-white focus:border-indigo-500 transition-all outline-none">
            </div>
            
            <button type="submit" name="update_platform" class="mt-12 w-full bg-slate-900 text-white py-6 rounded-[2.5rem] font-black uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-100">
                Update Command Center
            </button>
        </div>
    </div>
</form>

<?php include 'includes/footer.php'; ?>