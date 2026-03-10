<?php
// Protection: Include config and check session
if (!isset($conn)) { include '../config.php'; }

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_fullname'] ?? 'Organizer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Panel | CricStrome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .active-link { background: #fff7ed; color: #f97316 !important; border-radius: 1rem; }
    </style>
</head>
<body class="flex">
    <?php include 'sidebar.php'; ?> <main class="flex-grow min-h-screen p-8 lg:p-12">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-xs font-bold text-slate-400 uppercase tracking-widest">System Online</h1>
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-700"><?php echo $user_name; ?></span>
                <div class="w-10 h-10 bg-slate-900 rounded-xl"></div>
            </div>
        </div>