<?php
include 'config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = $_POST['password']; 

    // Query specifically targeting the 'user_master' table as requested
    // We check for 'Publish' status to ensure only active accounts log in
    $query = "SELECT * FROM user_master WHERE mobile_no = '$mobile' AND status = 'Publish'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Authenticating using the MD5 hash shown in your database rows
        if (md5($password) == $user['password']) {
            
            // To restrict access to specific people (Administrator, Manager, Organizer), 
            // we verify the 'is_admin' flag or cross-reference with the roles in your users table
            if ($user['is_admin'] == 1) {
                $_SESSION['admin_id'] = $user['user_id'];
                $_SESSION['admin_mobile'] = $user['mobile_no'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Access Denied: You do not have administrative permissions.";
            }
        } else {
            $error = "Mobile number not found or account not active.";
        }
    } else {
        $error = "Mobile number not found or account not active.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | CricStrom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FDFCF8] h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-[3rem] shadow-2xl border border-teal-50 p-10 text-center relative overflow-hidden">
        
        <div class="inline-flex items-center justify-center w-14 h-14 bg-[#E6F6F4] rounded-2xl mb-4">
         <img src="../images/favicon.png" class="w-full h-full rounded object-cover">
        </div>
        
        <h1 class="text-3xl font-black text-slate-900 mb-1">Admin <span class="text-[#14B8A6]">Portal</span></h1>
        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em] mb-10">Authorized Access Only</p>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-[11px] font-bold mb-8 border border-red-100 flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-sm"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6 text-left">
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 tracking-widest">Mobile Number</label>
                <div class="relative">
                    <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="mobile" required placeholder="9909146046" 
                           class="w-full pl-12 pr-6 py-4 bg-[#F2F6FF] border border-transparent rounded-2xl focus:outline-none focus:border-[#14B8A6] transition font-medium text-slate-700">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-bold text-slate-400 uppercase ml-2 tracking-widest">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="password" name="password" required placeholder="••••••" 
                           class="w-full pl-12 pr-6 py-4 bg-[#F2F6FF] border border-transparent rounded-2xl focus:outline-none focus:border-[#14B8A6] transition font-medium text-slate-700">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#0F172A] text-white font-black py-5 rounded-2xl hover:bg-[#14B8A6] transition-all duration-300 shadow-xl flex items-center justify-center gap-3 uppercase tracking-widest text-[11px]">
                SECURE LOGIN <i class="fas fa-arrow-right text-[10px]"></i>
            </button>
        </form>

        <div class="mt-10">
            <a href="../index.php" class="text-slate-400 text-[10px] font-bold uppercase tracking-widest hover:text-[#14B8A6] transition">
                <i class="fas fa-chevron-left mr-2"></i> Back to Website
            </a>
        </div>
    </div>

</body>
</html>