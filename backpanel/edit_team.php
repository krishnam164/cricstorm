<?php
include '../config.php';

if (!isset($_SESSION['admin_id'])) { header("Location: ../login.php"); exit(); }

$message = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 1. FETCH CURRENT TEAM DATA
$team_query = mysqli_query($conn, "SELECT * FROM team_master WHERE team_id = '$id'");
$team = mysqli_fetch_assoc($team_query);

if (!$team) { header("Location: manage_teams.php"); exit(); }

// 2. UPDATE LOGIC
// 2. UPDATE LOGIC
if (isset($_POST['update_team'])) {
    // Change 'value' to 'string' in all these lines:
    $t_id = mysqli_real_escape_string($conn, $_POST['tournament_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short = mysqli_real_escape_string($conn, $_POST['short_name']);
    $owner = mysqli_real_escape_string($conn, $_POST['owner_name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $total = mysqli_real_escape_string($conn, $_POST['total_points']);
    $remain = mysqli_real_escape_string($conn, $_POST['remaining_points']);
    
    // ... rest of your code

    $logo_db_path = $team['team_logo']; // Keep old logo by default

    // 3. IMAGE UPDATE LOGIC
    if (!empty($_FILES["team_logo"]["name"])) {
        // Get tournament folder name
        $t_res = mysqli_query($conn, "SELECT tournament_name FROM tournament_master WHERE tournament_id = '$t_id'");
        $t_info = mysqli_fetch_assoc($t_res);
        $folder_name = str_replace(' ', '_', $t_info['tournament_name']);
        $target_dir = "../uploads/tournaments/" . $folder_name . "/";

        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        // Delete old file if it exists
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

<div class="mb-10">
    <h2 class="text-2xl font-black text-slate-900 italic">Modify <span class="text-teal-500">Team Details</span></h2>
    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Editing: <?php echo $team['name']; ?></p>
</div>

<div class="max-w-7xl">
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="bg-white p-10 rounded-[3rem] border border-teal-50 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Parent Tournament</label>
                <select name="tournament_id" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
                    <?php
                    $t_list = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master");
                    while($t = mysqli_fetch_assoc($t_list)) {
                        $sel = ($t['tournament_id'] == $team['tournament_id']) ? 'selected' : '';
                        echo "<option value='".$t['tournament_id']."' $sel>".$t['tournament_name']."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Team Full Name</label>
                <input type="text" name="name" value="<?php echo $team['name']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Short Name</label>
                <input type="text" name="short_name" maxlength="3" value="<?php echo $team['short_name']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none uppercase">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Owner Name</label>
                <input type="text" name="owner_name" value="<?php echo $team['owner_name']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Mobile Number</label>
                <input type="text" name="mobile_no" value="<?php echo $team['mobile_no']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Status</label>
                <select name="status" class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
                    <option value="Active" <?php if($team['status'] == 'Active') echo 'selected'; ?>>Active</option>
                    <option value="Inactive" <?php if($team['status'] == 'Inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Total Points</label>
                <input type="number" name="total_points" value="<?php echo $team['total_points']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Remaining Points</label>
                <input type="number" name="remaining_points" value="<?php echo $team['remaining_points']; ?>" required class="w-full px-6 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>

            <div class="md:col-span-1">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Update Logo</label>
                <div class="relative h-40 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[2rem] flex flex-col items-center justify-center overflow-hidden">
                    <input type="file" name="team_logo" id="logo-in" class="hidden" onchange="preview(this, 'logo-p')">
                    <label for="logo-in" class="cursor-pointer text-center w-full h-full flex flex-col items-center justify-center">
                        <img id="logo-p" src="../<?php echo $team['team_logo']; ?>" class="absolute inset-0 w-full h-full object-contain p-4">
                        <p id="logo-text" class="text-[9px] font-black text-slate-400 uppercase bg-white/80 px-2 py-1 rounded relative z-10">Change Image</p>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-6">
            <button type="submit" name="update_team" class="bg-slate-900 text-white px-12 py-5 rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-teal-500 transition-all shadow-xl">
                Update Franchise
            </button>
            <a href="manage_teams.php" class="text-[10px] font-black text-slate-400 uppercase">Back to List</a>
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