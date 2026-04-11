<?php
// Protection: Include config and check session
if (!isset($conn)) { include '../config.php'; }

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_fullname'] ?? 'Manager';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Panel | CricStorm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="../images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
        
        /* Mobile Sidebar Drawer Transition */
        #managerSidebar { transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        @media (max-width: 1024px) {
            #managerSidebar { position: fixed; z-index: 100; transform: translateX(-100%); height: 100vh; }
            #managerSidebar.active { transform: translateX(0); }
            #sidebarOverlay.active { display: block; }
        }

        .active-link { background: #fff7ed; color: #f97316 !important; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(249, 115, 22, 0.1); }
        
        /* Smooth Scrollbar for internal data lists */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="flex min-h-screen">

<div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden transition-opacity"></div>

<?php 
/* Note: Ensure your sidebar.php uses <aside id="managerSidebar"> */
include 'sidebar.php'; 
?>

<main class="flex-grow flex flex-col min-w-0">
    <nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-200 px-4 md:px-8 lg:px-12 py-4 flex items-center justify-between">
        
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                <i class="fas fa-bars-staggered text-xl"></i>
            </button>
            
            <div class="hidden sm:block">
                <h1 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">CricStorm Engine</h1>
                <p class="text-sm font-extrabold text-slate-800 italic uppercase">Manager <span class="text-orange-500">Workspace</span></p>
            </div>
        </div>

        <div class="flex items-center gap-3 md:gap-6">
            <button class="relative p-2 text-slate-400 hover:text-orange-500 transition-colors">
                <i class="fas fa-bell"></i>
                <span class="absolute top-2 right-2 w-2 h-2 bg-orange-500 rounded-full border-2 border-white"></span>
            </button>

            <div class="flex items-center gap-3 border-l border-slate-100 pl-3 md:pl-6">
                <div class="text-right hidden md:block">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Online</p>
                    <p class="text-xs font-bold text-slate-700"><?php echo $user_name; ?></p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600 shadow-sm border border-orange-200">
                    <i class="fas fa-user-gear text-sm"></i>
                </div>
            </div>
        </div>
    </nav>

    <div class="p-6 md:p-10 lg:p-12">

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('managerSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    // Toggle the translation class
    sidebar.classList.toggle('-translate-x-full');
    
    // Toggle the overlay visibility
    if (overlay.classList.contains('hidden')) {
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Stop background scrolling
    } else {
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
}
</script>