<?php
include '../config.php';

/** 1. MASTER SECURITY GATE */
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == ''  || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$admin_mobile = $_SESSION['admin_mobile'] ?? 'System Admin';
$message = "";

/** 2. COMMAND CENTER UPDATE LOGIC */
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

/** 3. SAFE DATA FETCHING */
$settings_query = mysqli_query($conn, "SELECT * FROM settings_master WHERE id = 1 LIMIT 1");
$st = mysqli_fetch_assoc($settings_query) ?? [];

$active_page = 'settings';
include 'includes/header.php'; 
?>

<div class="mb-8 md:mb-12 flex flex-col sm:flex-row justify-between items-center sm:items-end gap-4 px-2">
    <div class="text-center sm:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic uppercase tracking-tighter">
            Command <span class="text-indigo-600">Center</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 mt-1 uppercase tracking-[0.3em] font-bold">Global Infrastructure Oversight</p>
    </div>
    <div class="flex gap-3">
        <div class="bg-white px-5 md:px-6 py-2.5 md:py-3 rounded-xl md:rounded-2xl border border-teal-50 shadow-sm flex items-center gap-3">
            <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
            <span class="text-[9px] md:text-[10px] font-black text-slate-700 uppercase">System: Operational</span>
        </div>
    </div>
</div>

<?php if($message): ?>
    <div class="mx-2 mb-8 p-5 md:p-6 bg-indigo-600 text-white rounded-2xl md:rounded-[2.5rem] shadow-xl shadow-indigo-100 flex items-center gap-4 animate__animated animate__pulse">
        <i class="fas fa-check-circle text-xl md:text-2xl shrink-0"></i>
        <span class="text-xs md:text-sm font-bold tracking-wide"><?php echo $message; ?></span>
    </div>
<?php endif; ?>

<form method="POST" class="px-2 md:px-0 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-8 md:mb-12">
        
        <div class="bg-white p-8 md:p-10 rounded-[2rem] md:rounded-[3.5rem] border border-teal-50 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 transition-all group-hover:scale-110"></div>
            <i class="fas fa-gavel text-2xl md:text-3xl text-indigo-600 mb-6 relative"></i>
            <h3 class="text-xs md:text-sm font-black text-slate-800 uppercase tracking-widest mb-1 relative">Bidding Engine</h3>
            <p class="text-[10px] md:text-[11px] text-slate-400 font-bold mb-6 md:mb-8 relative leading-tight">Global transaction kill-switch</p>
            <select name="bid_status" class="w-full px-6 py-4 md:px-8 md:py-5 bg-slate-50 border-2 border-transparent rounded-xl md:rounded-[2rem] font-black text-slate-700 text-sm appearance-none focus:border-indigo-500 focus:bg-white transition-all cursor-pointer relative">
                <option value="Enabled" <?php echo (($st['bidding_status'] ?? 'Enabled') == 'Enabled') ? 'selected' : ''; ?>>LIVE / ENABLED</option>
                <option value="Disabled" <?php echo (($st['bidding_status'] ?? 'Enabled') == 'Disabled') ? 'selected' : ''; ?>>FREEZE / DISABLED</option>
            </select>
        </div>

        <div class="bg-white p-8 md:p-10 rounded-[2rem] md:rounded-[3.5rem] border border-teal-50 shadow-sm relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-full -mr-12 -mt-12 transition-all group-hover:scale-110"></div>
            <i class="fas fa-tools text-2xl md:text-3xl text-rose-500 mb-6 relative"></i>
            <h3 class="text-xs md:text-sm font-black text-slate-800 uppercase tracking-widest mb-1 relative">Gatekeeper</h3>
            <p class="text-[10px] md:text-[11px] text-slate-400 font-bold mb-6 md:mb-8 relative leading-tight">Platform access control</p>
            <select name="m_mode" class="w-full px-6 py-4 md:px-8 md:py-5 bg-slate-50 border-2 border-transparent rounded-xl md:rounded-[2rem] font-black text-slate-700 text-sm appearance-none focus:border-rose-500 focus:bg-white transition-all cursor-pointer relative">
                <option value="Off" <?php echo (($st['maintenance_mode'] ?? 'Off') == 'Off') ? 'selected' : ''; ?>>PUBLIC / LIVE</option>
                <option value="On" <?php echo (($st['maintenance_mode'] ?? 'Off') == 'On') ? 'selected' : ''; ?>>ADMIN / MAINTENANCE</option>
            </select>
        </div>

        <div class="bg-[#0F172A] p-8 md:p-10 rounded-[2rem] md:rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden">
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-teal-500/10 rounded-full blur-3xl"></div>
            <i class="fas fa-server text-2xl md:text-3xl text-teal-400 mb-6"></i>
            <h3 class="text-xs md:text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Core Statistics</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-end border-b border-white/5 pb-3">
                    <span class="text-[9px] md:text-[10px] font-bold text-slate-500 uppercase tracking-wider">Active Rows</span>
                    <span class="text-base md:text-lg font-black text-teal-400">21,213</span>
                </div>
                <div class="flex justify-between items-end">
                    <span class="text-[9px] md:text-[10px] font-bold text-slate-500 uppercase tracking-wider">Data Size</span>
                    <span class="text-base md:text-lg font-black">2.2 MiB</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-10">
        <div class="bg-white p-8 md:p-12 rounded-[2rem] md:rounded-[4rem] border border-teal-50 shadow-sm">
            <h4 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 md:mb-10 text-center md:text-left">Economic Variables</h4>
            <div class="space-y-6 md:space-y-8">
                <div>
                    <label class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2 tracking-widest">Min. Bid Increment (₹)</label>
                    <input type="number" name="min_bid" value="<?php echo $st['min_bid_increment'] ?? 1000; ?>" class="w-full px-6 py-4 md:px-8 md:py-5 bg-slate-50 border border-slate-100 rounded-xl md:rounded-[2rem] font-black text-slate-800 text-sm md:text-base focus:bg-white focus:border-indigo-500 transition-all outline-none">
                </div>
                <div>
                    <label class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2 tracking-widest">Conversion Rate (Point = ₹)</label>
                    <input type="number" step="0.01" name="conv_rate" value="<?php echo $st['conversion_rate'] ?? 1.00; ?>" class="w-full px-6 py-4 md:px-8 md:py-5 bg-slate-50 border border-slate-100 rounded-xl md:rounded-[2rem] font-black text-slate-800 text-sm md:text-base focus:bg-white focus:border-indigo-500 transition-all outline-none">
                </div>
            </div>
        </div>

        <div class="bg-white p-8 md:p-12 rounded-[2rem] md:rounded-[4rem] border border-teal-50 shadow-sm flex flex-col">
            <h4 class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 md:mb-10 text-center md:text-left">Timing Engine</h4>
            <div class="flex-grow">
                <label class="block text-[9px] md:text-[10px] font-bold text-slate-400 uppercase mb-3 ml-2 tracking-widest">Auction Timer (Sec)</label>
                <input type="number" name="timer" value="<?php echo $st['auction_timer'] ?? 30; ?>" class="w-full px-6 py-4 md:px-8 md:py-5 bg-slate-50 border border-slate-100 rounded-xl md:rounded-[2rem] font-black text-slate-800 text-sm md:text-base focus:bg-white focus:border-indigo-500 transition-all outline-none">
            </div>
            
            <button type="submit" name="update_platform" class="mt-8 md:mt-12 w-full bg-slate-900 text-white py-5 md:py-6 rounded-xl md:rounded-[2.5rem] font-black uppercase tracking-[0.15em] md:tracking-[0.2em] text-[10px] md:text-xs hover:bg-indigo-600 transition-all shadow-xl active:scale-95">
                Update Configuration
            </button>
        </div>
    </div>
</form>

<?php include 'includes/footer.php'; ?>