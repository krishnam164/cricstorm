<?php
include '../config.php';

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }

$message = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$team_query = mysqli_query($conn, "SELECT * FROM team_master WHERE team_id = '$id'");
$team = mysqli_fetch_assoc($team_query);

if (!$team) { header("Location: manage_teams.php"); exit(); }

if (isset($_POST['update_team'])) {
    $t_id = mysqli_real_escape_string($conn, $_POST['tournament_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short = mysqli_real_escape_string($conn, $_POST['short_name']);
    $owner = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $total = mysqli_real_escape_string($conn, $_POST['total_points']);
    $remain = mysqli_real_escape_string($conn, $_POST['remaining_points']);
    
    $logo_db_path = $team['team_logo']; 

    if (!empty($_FILES["team_logo"]["name"])) {
        $t_res = mysqli_query($conn, "SELECT tournament_name FROM tournament_master WHERE tournament_id = '$t_id'");
        $t_info = mysqli_fetch_assoc($t_res);
        $folder_name = str_replace(' ', '_', $t_info['tournament_name']);
        $target_dir = "../uploads/tournaments/" . $folder_name . "/";

        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        if (file_exists("../" . $team['team_logo'])) {
            @unlink("../" . $team['team_logo']);
        }

        $file_slug = strtolower(str_replace(' ', '_', $name));
        $logo_name = $file_slug . "_logo_" . time() . ".png";
        $logo_db_path = "uploads/tournaments/" . $folder_name . "/" . $logo_name;

        move_uploaded_file($_FILES["team_logo"]["tmp_name"], $target_dir . $logo_name);
    }

    $update_sql = "UPDATE team_master SET 
                    tournament_id = '$t_id', 
                    name = '$name', 
                    short_name = '$short', 
                    owner_name = '$owner', 
                    mobile_no = '$mobile', 
                    status = '$status', 
                    total_points = '$total', 
                    remaining_points = '$remain', 
                    team_logo = '$logo_db_path' 
                   WHERE team_id = '$id'";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>window.location.href='manage_teams.php?msg=updated';</script>";
        exit();
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

include 'includes/header.php';
?>

<div class="mb-8 md:mb-10 px-4 md:px-0 text-center md:text-left">
    <h2 class="text-2xl md:text-3xl font-black text-slate-900 italic tracking-tighter uppercase">
        Modify <span class="text-teal-500">Team Details</span>
    </h2>
    <p class="text-[9px] md:text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Editing: <?php echo $team['name']; ?></p>
</div>

<div class="max-w-7xl px-4 md:px-0 pb-10">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        
        <?php if($message != ""): ?>
            <div class="p-4 bg-red-50 text-red-500 rounded-2xl font-bold text-xs uppercase border border-red-100">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-6 md:p-10 rounded-[2rem] md:rounded-[3rem] border border-teal-50 shadow-sm grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5 md:gap-6">
            
            <div class="sm:col-span-2 md:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Parent Tournament</label>
                <select name="tournament_id" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none focus:ring-2 focus:ring-teal-500 appearance-none text-sm">
                    <?php
                    $t_list = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master");
                    while($t = mysqli_fetch_assoc($t_list)) {
                        $sel = ($t['tournament_id'] == $team['tournament_id']) ? 'selected' : '';
                        echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="sm:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Team Full Name</label>
                <input type="text" name="name" value="<?php echo $team['name']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none focus:bg-white border border-transparent focus:border-teal-500 text-sm">
            </div>

            <div class="sm:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Short Name</label>
                <input type="text" name="short_name" maxlength="3" value="<?php echo $team['short_name']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none uppercase text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Owner Name</label>
                <input type="text" name="owner_name" value="<?php echo $team['owner_name']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Mobile Number</label>
                <input type="tel" name="mobile_no" value="<?php echo $team['mobile_no']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Status</label>
                <select name="status" class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none appearance-none text-sm">
                    <option value="Active" <?php if($team['status'] == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if($team['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Total Points</label>
                <input type="number" name="total_points" value="<?php echo $team['total_points']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Remaining Points</label>
                <input type="number" name="remaining_points" value="<?php echo $team['remaining_points']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-xl md:rounded-2xl font-bold text-slate-700 outline-none text-sm">
            </div>

            <div class="sm:col-span-1">
                <label class="text-[9px] md:text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Update Logo</label>
                <div class="relative h-44 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex flex-col items-center justify-center overflow-hidden group hover:border-teal-500 transition-colors">
                    <input type="file" name="team_logo" id="logo-in" class="hidden" onchange="preview(this, 'logo-p')">
                    <label for="logo-in" class="cursor-pointer text-center w-full h-full flex flex-col items-center justify-center">
                        <img id="logo-p" src="../<?php echo $team['team_logo']; ?>" class="absolute inset-0 w-full h-full object-contain p-4 z-0">
                        <div class="relative z-10 bg-white/90 backdrop-blur-sm px-4 py-2 rounded-xl border border-slate-100 shadow-sm opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                             <p class="text-[9px] font-black text-teal-600 uppercase">Change Image</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-4 md:gap-6">
            <button type="submit" name="update_team" class="w-full sm:w-auto bg-slate-900 text-white px-10 md:px-12 py-4 md:py-5 rounded-xl md:rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all shadow-xl active:scale-95">
                Update Franchise
            </button>
            <a href="manage_teams.php" class="text-[10px] font-black text-slate-400 uppercase hover:text-slate-600 transition-colors py-2">
                Back to List
            </a>
        </div>
    </form>
</div>

<script>
function preview(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(id).src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>