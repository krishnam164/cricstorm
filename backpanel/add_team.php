<?php
include '../config.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }

$message = "";

// 1. FETCH TOURNAMENTS
$tournaments = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master ORDER BY tournament_id DESC");

if (isset($_POST['add_team'])) {
    $t_id = mysqli_real_escape_string($conn, $_POST['tournament_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short = mysqli_real_escape_string($conn, $_POST['short_name']);
    $owner_name = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $mobile_no = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $total_points = mysqli_real_escape_string($conn, $_POST['total_points']);
    $remaining_points = mysqli_real_escape_string($conn, $_POST['remaining_points']);

    $t_res = mysqli_query($conn, "SELECT tournament_name FROM tournament_master WHERE tournament_id = '$t_id'");
    $t_info = mysqli_fetch_assoc($t_res);
    $folder_name = str_replace(' ', '_', $t_info['tournament_name']);
    $target_dir = "../uploads/tournaments/" . $folder_name . "/";

    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    $file_slug = strtolower(str_replace(' ', '_', $name));
    $logo_name = $file_slug . "_logo_" . time() . ".png";
    $logo_db_path = "uploads/tournaments/" . $folder_name . "/" . $logo_name;

    if (move_uploaded_file($_FILES["team_logo"]["tmp_name"], $target_dir . $logo_name)) {
        $sql = "INSERT INTO team_master (tournament_id, name, short_name, owner_name, mobile_no, status, total_points, remaining_points, team_logo) 
                VALUES ('$t_id', '$name', '$short', '$owner_name', '$mobile_no', '$status', '$total_points', '$remaining_points', '$logo_db_path')";    

        if (mysqli_query($conn, $sql)) {
            header("Location: manage_teams.php?msg=added");
            exit();
        } else {
            $message = "Database Error: " . mysqli_error($conn);
        }
    } else {
        $message = "File Upload Error: Please check folder permissions.";
    }
}

include 'includes/header.php';
?>

<div class="mb-8 md:mb-10 px-4 md:px-0 text-center md:text-left">
    <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic tracking-tighter uppercase">
        Register <span class="text-teal-500">New Team</span>
    </h2>
    <p class="text-[9px] md:text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Assigning a squad to a specific league</p>
</div>

<div class="max-w-7xl px-4 md:px-0">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        
        <?php if($message != ""): ?>
            <div class="p-4 bg-red-50 text-red-500 rounded-2xl font-bold text-xs uppercase border border-red-100 animate__animated animate__shakeX">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-teal-50 shadow-sm grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 md:gap-6">
            
            <div class="sm:col-span-2 md:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Parent Tournament</label>
                <select name="tournament_id" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-teal-500 appearance-none cursor-pointer text-sm">
                    <option value="">Select Tournament</option>
                    <?php while($row = mysqli_fetch_assoc($tournaments)): ?>
                        <option value="<?php echo $row['tournament_id']; ?>"><?php echo $row['tournament_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="sm:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Team Full Name</label>
                <input type="text" name="name" required placeholder="e.g. Chennai Super Kings" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none focus:bg-white border border-transparent focus:border-teal-500 text-sm">
            </div>

            <div class="sm:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Short Name</label>
                <input type="text" name="short_name" maxlength="5" required placeholder="CSK" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none uppercase text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Owner Name</label>
                <input type="text" name="owner_name" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Owner Mobile</label>
                <input type="tel" name="mobile_no" required placeholder="00000 00000" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Status</label>
                <select name="status" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none appearance-none cursor-pointer text-sm">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Total Points</label>
                <input type="number" name="total_points" id="t_points" required placeholder="10000" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm" oninput="document.getElementById('r_points').value = this.value">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Remaining Points</label>
                <input type="number" name="remaining_points" id="r_points" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div class="sm:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Team Logo</label>
                <div class="relative h-44 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex flex-col items-center justify-center overflow-hidden group hover:border-teal-500 transition-colors">
                    <input type="file" name="team_logo" required id="logo-in" class="hidden" onchange="preview(this, 'logo-p')">
                    <label for="logo-in" class="cursor-pointer text-center w-full h-full flex flex-col items-center justify-center">
                        <img id="logo-p" class="absolute inset-0 w-full h-full object-contain hidden p-4">
                        <i id="logo-icon" class="fas fa-shield-alt text-slate-300 text-3xl mb-2 transition-transform group-hover:scale-110"></i>
                        <p id="logo-text" class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Upload PNG/JPG</p>
                    </label>
                </div>
            </div>
        </div> 

        <div class="flex flex-col sm:flex-row items-center gap-4 md:gap-6 pb-10">
            <button type="submit" name="add_team" class="w-full sm:w-auto bg-slate-900 text-white px-12 py-5 rounded-xl md:rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all shadow-xl active:scale-95">
                Initialize Team
            </button>
            <a href="all_teams.php" class="text-[10px] font-black text-slate-400 uppercase hover:text-slate-600 transition-colors py-2">
                Cancel Registration
            </a>
        </div>
    </form>
</div>

<script>
function preview(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(id);
            img.src = e.target.result;
            img.classList.remove('hidden');
            document.getElementById('logo-icon').classList.add('hidden');
            document.getElementById('logo-text').classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>