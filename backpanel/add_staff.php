<?php
include '../config.php';

// 1. SECURITY GATE
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '') {
    header("Location: ../login.php"); exit();
}

$error = "";

if (isset($_POST['save_staff'])) {
    $name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $user_pass = md5(mysqli_real_escape_string($conn, $_POST['user_pass'])); 
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE user_email = '$email' OR user_mobile = '$mobile'");
    
    if (mysqli_num_rows($check) > 0) {
        $error = "Email or Mobile Number already registered!";
    } else {
        $sql = "INSERT INTO users (user_fullname, user_email, user_mobile, user_pass, user_role, user_status, tournament_id) 
                VALUES ('$name', '$email', '$mobile', '$user_pass', '$role', 'Publish', 0)";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: manage_staff.php?msg=added");
            exit();
        } else {
            $error = "SQL Error: " . mysqli_error($conn);
        }
    }
}

include 'includes/header.php';
?>

<div class="max-w-3xl mx-auto p-4 md:p-6">
    <div class="bg-white rounded-[2rem] md:rounded-[3rem] shadow-2xl border border-slate-100 overflow-hidden">
        
        <div class="p-6 md:p-10 bg-slate-900 text-white relative">
            <h2 class="text-2xl md:text-3xl font-black italic uppercase tracking-tighter">
                Add <span class="text-blue-500">Staff</span>
            </h2>
            <?php if($error): ?>
                <div class="bg-red-500/10 text-red-500 p-3 rounded-xl text-[10px] md:text-xs mt-4 font-bold animate__animated animate__shakeX border border-red-500/20">
                    <i class="fas fa-bug mr-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" class="p-6 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8">
            
            <div class="md:col-span-2">
                <label class="text-[9px] md:text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest ml-2">Full Name</label>
                <input type="text" name="fullname" required placeholder="Enter full name" 
                       class="w-full p-4 md:p-5 bg-slate-50 border-none rounded-xl md:rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest ml-2">Email Address</label>
                <input type="email" name="email" required placeholder="staff@cricstorm.com" 
                       class="w-full p-4 md:p-5 bg-slate-50 border-none rounded-xl md:rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest ml-2">Mobile Number</label>
                <input type="tel" name="mobile" required placeholder="Numeric only" 
                       class="w-full p-4 md:p-5 bg-slate-50 border-none rounded-xl md:rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div class="relative">
                <label class="text-[9px] md:text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest ml-2">Set Password</label>
                <input type="password" name="user_pass" id="user_pass" required placeholder="••••••••" 
                       class="w-full p-4 md:p-5 bg-slate-50 border-none rounded-xl md:rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                <button type="button" onclick="togglePass()" class="absolute right-5 bottom-4 md:bottom-5 text-slate-300 hover:text-blue-500 transition-colors">
                    <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                </button>
            </div>

            <div>
                <label class="text-[9px] md:text-[10px] font-black uppercase text-slate-400 mb-2 block tracking-widest ml-2">Assign Role</label>
                <select name="role" required class="w-full p-4 md:p-5 bg-slate-50 border-none rounded-xl md:rounded-2xl font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all appearance-none cursor-pointer">
                    <option value="manager">Manager</option>
                    <option value="staff">Basic Staff Member</option>
                </select>
            </div>

            <div class="md:col-span-2 pt-4 md:pt-6">
                <button type="submit" name="save_staff" class="w-full bg-blue-600 text-white py-4 md:py-5 rounded-xl md:rounded-2xl font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl active:scale-[0.98]">
                    Register Member
                </button>
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