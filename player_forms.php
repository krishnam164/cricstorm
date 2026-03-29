<?php
include 'config.php';

$message = "";

// 1. FETCH ACTIVE TOURNAMENTS
$tournaments_query = mysqli_query($conn, "SELECT tournament_id, tournament_name FROM tournament_master WHERE tournament_status = 'Publish'");

if (isset($_POST['register_player'])) {
    $t_id = mysqli_real_escape_string($conn, $_POST['tournament_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile_no']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $dob = $_POST['birth_date'];
    $category = $_POST['category'];
    $batting_type = $_POST['batsman_type'];
    $bowling_type = $_POST['bowler_type'];
    $tshirt = $_POST['tshirt_size'];
    $trouser = $_POST['trouser_size'];

    $form_query = mysqli_query($conn, "SELECT MAX(form_no) as last_no FROM player_master WHERE tournament_id = '$t_id'");
    $form_data = mysqli_fetch_assoc($form_query);
    $next_form_no = ($form_data['last_no']) ? $form_data['last_no'] + 1 : 1;
    
    // Get Tournament Name for Folder Path
    $t_res = mysqli_query($conn, "SELECT tournament_name FROM tournament_master WHERE tournament_id = '$t_id'");
    $t_info = mysqli_fetch_assoc($t_res);
    $folder_name = str_replace(' ', '_', $t_info['tournament_name']);
    $target_dir = "uploads/tournaments/" . $folder_name . "/";

    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

    // 2. FILE HANDLING
    $player_slug = strtolower(str_replace(' ', '_', $name));
    $photo_path = $target_dir . $player_slug . "_photo.png";
    $adhar_f_path = $target_dir . $player_slug . "_adhar_front.png";
    $adhar_b_path = $target_dir . $player_slug . "_adhar_back.png";

    move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path);
    move_uploaded_file($_FILES["adhar_front"]["tmp_name"], $adhar_f_path);
    move_uploaded_file($_FILES["adhar_back"]["tmp_name"], $adhar_b_path);

    // 3. INSERT (All columns from your DB)
    $sql = "INSERT INTO player_master (
                tournament_id, name, address, mobile_no, birth_date, 
                category, batsman_type, bowler_type, size,
                photo, adhar_front, adhar_back, status
            ) VALUES (
                '$t_id', '$name', '$address', '$mobile', '$dob', 
                '$category', '$batting_type', '$bowling_type', '$tshirt',
                '$photo_path', '$adhar_f_path', '$adhar_b_path', 'Pending'
            )";

    if (mysqli_query($conn, $sql)) {
        $message = "Registration Successful! Admin will verify your profile.";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CricStrome | Player Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 py-12 px-4">

<div class="max-w-4xl mx-auto bg-white rounded-[3rem] shadow-xl border border-teal-50 overflow-hidden">
    <div class="bg-slate-900 p-10 text-center">
        <h2 class="text-3xl font-black text-white italic tracking-widest">PLAYER <span class="text-teal-400">REGISTRATION</span></h2>
        <p class="text-slate-400 text-[10px] uppercase font-bold tracking-[0.3em] mt-2">Personalize your CricStrome profile</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="p-8 md:p-12 space-y-8">
        
        <?php if($message): ?>
            <div class="p-4 bg-teal-50 text-teal-600 rounded-2xl text-[10px] font-black uppercase text-center border border-teal-100 italic">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Available Tournaments</label>
                <select name="tournament_id" required class="w-full px-8 py-5 bg-slate-50 rounded-[2rem] border-none font-bold text-slate-700 focus:ring-2 focus:ring-teal-500 transition-all outline-none">
                    <option value="">-- Choose a Tournament --</option>
                    <?php while($t = mysqli_fetch_assoc($tournaments_query)): ?>
                        <option value="<?php echo $t['tournament_id']; ?>"><?php echo $t['tournament_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="md:col-span-2 flex flex-col items-center justify-center pb-4">
                <div class="relative w-32 h-32 group">
                    <input type="file" name="photo" id="photo-input" accept="image/*" required class="hidden" onchange="previewImage(this)">
                    <label for="photo-input" class="cursor-pointer block w-full h-full rounded-full border-4 border-dashed border-slate-200 bg-slate-50 overflow-hidden hover:border-teal-400 transition-all relative">
                        <img id="photo-preview" class="w-full h-full object-cover hidden">
                        <div id="photo-placeholder" class="flex flex-col items-center justify-center h-full text-slate-300">
                            <i class="fas fa-camera text-2xl"></i>
                            <span class="text-[8px] font-black uppercase mt-1">Photo</span>
                        </div>
                    </label>
                    <div class="absolute -bottom-2 -right-2 bg-teal-500 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                        <i class="fas fa-plus text-[10px]"></i>
                    </div>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Full Name</label>
                <input type="text" name="name" required class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none focus:bg-white border border-transparent focus:border-teal-500">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Date of Birth</label>
                <input type="date" name="birth_date" required class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none focus:bg-white border border-transparent focus:border-teal-500">
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Mobile Number</label>
                <input type="text" name="mobile_no" required class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Player Category</label>
                <select name="category" class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
                    <option value="All Rounder">All Rounder</option>
                    <option value="Batsman">Batsman</option>
                    <option value="Bowler">Bowler</option>
                    <option value="Wicket Keeper">Wicket Keeper</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Batting Style</label>
                <select name="batsman_type" class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
                    <option value="Right Handed">Right Handed</option>
                    <option value="Left Handed">Left Handed</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Bowling Style</label>
                <select name="bowler_type" class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
                    <option value="Right Arm Fast">Right Arm Fast</option>
                    <option value="Right Arm Spin">Right Arm Spin</option>
                    <option value="Left Arm Fast">Left Arm Fast</option>
                    <option value="Left Arm Spin">Left Arm Spin</option>
                    <option value="None">None</option>
                </select>
            </div>

            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">T-Shirt Size</label>
                <select name="tshirt_size" class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
                    <option value="S">Small (S)</option>
                    <option value="M">Medium (M)</option>
                    <option value="L">Large (L)</option>
                    <option value="XL">Extra Large (XL)</option>
                    <option value="XXL">Double XL (XXL)</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Trouser Size</label>
                <input type="number" name="trouser_size" placeholder="e.g. 32" class="w-full px-8 py-4 bg-slate-50 rounded-2xl font-bold text-slate-700 outline-none">
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Aadhar Front</label>
                    <input type="file" name="adhar_front" required class="w-full px-6 py-3 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl text-[10px] font-bold text-slate-400">
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Aadhar Back</label>
                    <input type="file" name="adhar_back" required class="w-full px-6 py-3 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl text-[10px] font-bold text-slate-400">
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Full Permanent Address</label>
                <textarea name="address" required class="w-full px-8 py-6 bg-slate-50 rounded-[2rem] font-bold text-slate-700 h-32 outline-none focus:bg-white border border-transparent focus:border-teal-500"></textarea>
            </div>
        </div>

        <button type="submit" name="register_player" class="w-full bg-slate-900 text-white py-6 rounded-[2rem] text-[12px] font-black uppercase tracking-[0.3em] hover:bg-teal-500 transition-all shadow-xl shadow-slate-200">
            Submit Registration
        </button>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('photo-preview');
    const placeholder = document.getElementById('photo-placeholder');
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

</body>
</html>