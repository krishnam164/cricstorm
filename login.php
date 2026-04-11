<?php
include 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = $_POST['password']; 

    $query = "SELECT * FROM users WHERE user_mobile = '$mobile' AND user_status = 'Publish'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (md5($password) == $user['user_pass']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_role'] = strtolower($user['user_role']);
            $_SESSION['user_fullname'] = $user['user_fullname'];

            if ($_SESSION['user_role'] == 'administrator' || $_SESSION['user_role'] == 'admin') {
                header("Location: backpanel/index.php");
            } elseif ($_SESSION['user_role'] == 'manager') {
                header("Location: manager/dashboard.php");
            } else {
                $error = "Access Denied: Unauthorized role.";
            }
            exit();
        } else {
            $error = "Invalid Password."; 
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
    <title>Secure Login | CricStorm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        /* Prevent layout shift on mobile keyboards */
        body { min-height: 100dvh; }
    </style>
</head>
<body class="bg-[#FDFCF8] flex items-center justify-center p-4 md:p-6">

    <div class="w-full max-w-md bg-white rounded-[2rem] md:rounded-[3.5rem] shadow-2xl border border-teal-50 p-8 md:p-12 text-center relative overflow-hidden animate__animated animate__zoomIn">
        
        <div class="inline-flex items-center justify-center w-12 h-12 md:w-16 md:h-16 bg-[#E6F6F4] rounded-2xl mb-4 animate__animated animate__bounceInDown delay-1">
            <img src="images/favicon.png" class="w-full h-full rounded object-cover p-2">
        </div>
        
        <h1 class="text-2xl md:text-3xl font-black text-slate-900 mb-1 animate__animated animate__fadeInUp delay-1">Secure <span class="text-[#14B8A6]">Login</span></h1>
        <p class="text-slate-400 text-[9px] md:text-[10px] font-bold uppercase tracking-[0.2em] mb-8 md:mb-10 animate__animated animate__fadeInUp delay-1">Role-Based Access Control</p>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-3 md:p-4 rounded-xl md:rounded-2xl text-[10px] md:text-[11px] font-bold mb-6 md:mb-8 border border-red-100 flex items-center gap-3 animate__animated animate__shakeX">
                <i class="fas fa-exclamation-circle text-sm"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5 md:space-y-6 text-left">
            <div class="space-y-2 animate__animated animate__fadeInUp delay-2">
                <label for="mobile" class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase ml-2 tracking-widest">Mobile Number</label>
                <div class="relative group">
                    <i class="fas fa-phone absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#14B8A6] transition-colors"></i>
                    <input type="tel" id="mobile" name="mobile" required placeholder="Enter mobile number" 
                       class="w-full pl-12 pr-6 py-3.5 md:py-4 bg-[#F2F6FF] border border-transparent rounded-xl md:rounded-2xl focus:outline-none focus:border-[#14B8A6] focus:bg-white transition-all duration-300 font-medium text-slate-700 text-sm md:text-base shadow-sm">
                </div>
            </div>

            <div class="space-y-2 animate__animated animate__fadeInUp delay-2">
                <label for="password" class="text-[9px] md:text-[10px] font-bold text-slate-400 uppercase ml-2 tracking-widest">Password</label>
                <div class="relative group">
                    <i class="fas fa-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#14B8A6] transition-colors"></i>
                    <input type="password" id="password" name="password" required placeholder="••••••" 
                        class="w-full pl-12 pr-6 py-3.5 md:py-4 bg-[#F2F6FF] border border-transparent rounded-xl md:rounded-2xl focus:outline-none focus:border-[#14B8A6] focus:bg-white transition-all duration-300 font-medium text-slate-700 text-sm md:text-base shadow-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#0F172A] text-white font-black py-4 md:py-5 rounded-xl md:rounded-2xl hover:bg-[#14B8A6] hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 shadow-xl flex items-center justify-center gap-3 uppercase tracking-widest text-[10px] md:text-[11px] animate__animated animate__fadeInUp delay-3">
                AUTHENTICATE <i class="fas fa-arrow-right text-[10px]"></i>
            </button>
        </form>

        <div class="mt-8 md:mt-10 animate__animated animate__fadeIn delay-3">
            <a href="index.php" class="text-slate-400 text-[9px] md:text-[10px] font-bold uppercase tracking-widest hover:text-[#14B8A6] transition-all inline-block hover:-translate-x-1">
                <i class="fas fa-chevron-left mr-2"></i> Back to Home
            </a>
        </div>
    </div>

</body>
</html>