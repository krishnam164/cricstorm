<?php
// (Your existing PHP Security Gate remains identical)
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == '' || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$admin_name = $_SESSION['user_fullname'] ?? 'System Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin | CricStorm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f5f5f5; }
        
        /* Mobile Sidebar Logic */
        #main-sidebar { transition: transform 0.3s ease; }
        @media (max-width: 1024px) {
            #main-sidebar { position: fixed; z-index: 50; transform: translateX(-100%); height: 100vh; }
            #main-sidebar.active { transform: translateX(0); }
            #overlay.active { display: block; }
        }

        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover { background-color: rgba(20, 184, 166, 0.1); color: #14B8A6; }
        .active-link { background-color: #14B8A6 !important; color: white !important; box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.3); }
        
        /* Premium Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="flex min-h-screen">

<div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity"></div>

<?php 
// Ensure your sidebar.php uses <aside id="main-sidebar">
include 'sidebar.php'; 
?>

<main class="flex-grow flex flex-col min-w-0">
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                <i class="fas fa-bars-staggered text-xl"></i>
            </button>
            <h1 class="text-sm md:text-base font-extrabold text-slate-800 uppercase tracking-tight">
                Control <span class="text-teal-500">Center</span>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Super Admin</p>
                <p class="text-xs font-bold text-slate-700"><?php echo $admin_name; ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center text-white shadow-lg shadow-teal-200">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
    </header>

    <div class="p-4 md:p-10">

<script>
function toggleSidebar() {
    document.getElementById('main-sidebar').classList.toggle('active');
    document.getElementById('overlay').classList.toggle('active');
}
</script>