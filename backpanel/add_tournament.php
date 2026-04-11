<?php
require '../config.php';

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }

if (isset($_POST['add_tournament'])) {
    $t_name = mysqli_real_escape_string($conn, $_POST['tournament_name']);
    $folder_name = str_replace(' ', '_', $t_name);
    $base_path = "../uploads/tournaments/" . $folder_name . "/";
    
    if (!file_exists($base_path)) {
        mkdir($base_path, 0777, true);
    }

    $logo_ext = pathinfo($_FILES["tournament_logo"]["name"], PATHINFO_EXTENSION);
    $logo_db_name = "logo." . $logo_ext;
    $logo_target = $base_path . $logo_db_name;
    
    $banner_ext = pathinfo($_FILES["tournament_banner"]["name"], PATHINFO_EXTENSION);
    $banner_db_name = "banner." . $banner_ext;
    $banner_target = $base_path . $banner_db_name;

    move_uploaded_file($_FILES["tournament_logo"]["tmp_name"], $logo_target);
    move_uploaded_file($_FILES["tournament_banner"]["tmp_name"], $banner_target);

    $db_logo_path = "uploads/tournaments/" . $folder_name . "/" . $logo_db_name;
    $db_banner_path = "uploads/tournaments/" . $folder_name . "/" . $banner_db_name;

    $t_auction_title = mysqli_real_escape_string($conn, $_POST['tournament_auction_title']);
    $t_date = $_POST['tournament_date'];
    $t_points = $_POST['tournament_team_points'];
    $t_base = $_POST['tournament_base_value'];
    $t_bid = $_POST['tournament_bid_value'];
    $t_rules = mysqli_real_escape_string($conn, $_POST['tournament_rules']);
    $t_status = $_POST['tournament_status'];

    $sql = "INSERT INTO tournament_master (
                tournament_name, tournament_auction_title, tournament_date, 
                tournament_status, tournament_team_points, tournament_base_value,
                tournament_bid_value, tournament_banner, tournament_logo, 
                tournament_rules
            ) VALUES (
                '$t_name', '$t_auction_title', '$t_date', 
                '$t_status', '$t_points', '$t_base',
                '$t_bid', '$db_banner_path', '$db_logo_path',
                '$t_rules'
            )";

    if (mysqli_query($conn, $sql)) {
        header("Location: all_tournaments.php?success=1");
        exit();
    }
}
include("includes/header.php")
?>

<div class="max-w-5xl mx-auto px-4 md:px-0 pb-10">

    <div class="text-center md:text-left">
        <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic uppercase tracking-tighter">
            Add New <span class="text-teal-500">Tournament</span>
        </h2>
        <p class="text-[9px] md:text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] md:tracking-[0.3em] mt-1">Create & Configure Tournament Details</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-10 mt-8">
        <div class="bg-white p-6 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-teal-50 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-6">
            
            <div class="md:col-span-2">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Tournament Name</label>
                <input type="text" name="tournament_name" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white focus:border-teal-500 border border-transparent transition-all font-bold text-slate-700 text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Auction Title</label>
                <input type="text" name="tournament_auction_title" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white focus:border-teal-500 border border-transparent transition-all font-bold text-slate-700 text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Event Date</label>
                <input type="date" name="tournament_date" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white border border-transparent font-bold text-slate-700 text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Team Points</label>
                <input type="number" name="tournament_team_points" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white border border-transparent font-bold text-slate-700 text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Base Points</label>
                <input type="number" name="tournament_base_value" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white border border-transparent font-bold text-slate-700 text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Increment Points</label>
                <input type="number" name="tournament_bid_value" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white border border-transparent font-bold text-slate-700 text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Status</label>
                <select name="tournament_status" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl focus:bg-white border border-transparent font-bold text-slate-700 appearance-none cursor-pointer text-sm">
                    <option value="Publish">Publish</option>
                    <option value="Draft">Draft</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Tournament Logo</label>
                <div class="relative group">
                    <input type="file" name="tournament_logo" id="logo-input" accept="image/*" required class="hidden" onchange="previewImage(this, 'logo-preview')">
                    <label for="logo-input" class="flex flex-col items-center justify-center w-full h-36 md:h-40 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[1.5rem] md:rounded-[2rem] cursor-pointer hover:bg-teal-50 hover:border-teal-200 transition-all overflow-hidden relative">
                        <div id="logo-placeholder" class="text-center">
                            <i class="fas fa-image text-slate-300 text-2xl mb-2"></i>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Square Logo (1:1)</p>
                        </div>
                        <img id="logo-preview" class="absolute inset-0 w-full h-full object-contain hidden p-4">
                    </label>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Tournament Banner</label>
                <div class="relative group">
                    <input type="file" name="tournament_banner" id="banner-input" accept="image/*" required class="hidden" onchange="previewImage(this, 'banner-preview')">
                    <label for="banner-input" class="flex flex-col items-center justify-center w-full h-36 md:h-40 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[1.5rem] md:rounded-[2rem] cursor-pointer hover:bg-teal-50 hover:border-teal-200 transition-all overflow-hidden relative">
                        <div id="banner-placeholder" class="text-center">
                            <i class="fas fa-panorama text-slate-300 text-2xl mb-2"></i>
                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Horizontal Banner (16:9)</p>
                        </div>
                        <img id="banner-preview" class="absolute inset-0 w-full h-full object-cover hidden">
                    </label>
                </div>
            </div>

            <div class="md:col-span-3">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Tournament Rules & Description</label>
                <textarea name="tournament_rules" rows="4" class="w-full px-6 md:px-8 py-4 md:py-6 bg-slate-50 rounded-xl md:rounded-[2rem] focus:bg-white focus:border-teal-500 border border-transparent transition-all font-bold text-slate-700 text-sm"></textarea>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 md:gap-6">
            <button type="submit" name="add_tournament" class="w-full sm:w-auto bg-slate-900 text-white px-10 md:px-12 py-4 md:py-5 rounded-xl md:rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] hover:bg-teal-500 transition-all shadow-xl active:scale-95">
                Save Tournament Data
            </button>
            <a href="all_tournaments.php" class="text-[10px] font-black uppercase text-slate-400 hover:text-red-500 transition-all py-2">Discard Changes</a>
        </div>
    </form>
</div>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const placeholder = preview.previousElementSibling;
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>