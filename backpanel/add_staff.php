<?php
include '../config.php';

if (isset($_POST['save_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    
    // FIX: Change 'pass' to 'password' to match your HTML input name
    $user_pass = mysqli_real_escape_string($conn, $_POST['user_pass']); 
    
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Check for duplicates
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE user_email = '$email' OR user_mobile = '$mobile'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Email or Mobile Number already registered!";
    } else {
        // Now $password is defined, so line 25 will work perfectly
        $sql = "INSERT INTO users (user_fullname, user_email, user_mobile, user_pass, user_role, user_status, user_datetime) 
                VALUES ('$name', '$email', '$mobile', '$user_pass', '$role', 'Publish', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: manage_staff.php?msg=added");
            exit();
        }
    }
}

include 'includes/header.php';
?>

<div class="max-w-3xl mx-auto p-6">
    <div class="flex items-center gap-2 mb-6">
        <a href="manage_staff.php" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-blue-600">Staff Registry</a>
        <i class="fas fa-chevron-right text-[7px] text-slate-300"></i>
        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Onboard New Member</span>
    </div>

    <div class="bg-white rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
        <div class="p-10 bg-slate-900 text-white relative">
            <h2 class="text-3xl font-black italic uppercase">Add <span class="text-blue-500">Staff</span></h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] mt-3">Create new system credentials</p>
            <i class="fas fa-user-shield absolute top-10 right-10 text-5xl text-white/10"></i>
        </div>

        <form method="POST" class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="md:col-span-2">
                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Full Name</label>
                <input type="text" name="fullname" required placeholder="e.g. Mishra Krishna" 
                       class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Email Identity</label>
                <input type="email" name="email" required placeholder="staff@cricstrome.com" 
                       class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Mobile Contact</label>
                <input type="number" name="mobile" required placeholder="9737XXXXXX" 
                       class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="relative">
                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Access Password</label>
                <input type="user_pass" name="user_pass" id="user_pass" required placeholder="••••••••" 
                       class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                <button type="button" onclick="togglePass()" class="absolute right-5 bottom-5 text-slate-300 hover:text-blue-500">
                    <i class="fas fa-eye text-xs" id="eyeIcon"></i>
                </button>
            </div>

            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 mb-3 block tracking-widest">Authority Level</label>
                <select name="role" required class="w-full p-5 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="manager">Organizer / Manager</option>
                    <option value="staff">Basic Staff Member</option>
                </select>
            </div>

            <div class="md:col-span-2 pt-6 flex gap-4">
                <button type="submit" name="save_staff" class="flex-grow bg-blue-600 text-white py-5 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-blue-200">
                    Register Member
                </button>
                <a href="manage_staff.php" class="px-8 bg-slate-100 text-slate-400 flex items-center justify-center rounded-2xl hover:bg-slate-200 transition-all">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePass() {
    const p = document.getElementById('user_pass');
    const i = document.getElementById('eyeIcon');
    if (p.type === "password") {
        p.type = "text";
        i.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        p.type = "password";
        i.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<?php include 'includes/footer.php'; ?>