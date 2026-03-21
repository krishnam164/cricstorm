<?php

// 1. MASTER SECURITY GATE
// Verifies session and confirms the user is a Super Admin in the database
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_id'] == '' || !isset($_SESSION['admin_mobile']) || $_SESSION['admin_mobile'] == '' || !mysqli_num_rows(mysqli_query($conn, "SELECT user_id FROM user_master WHERE user_id = '{$_SESSION['admin_id']}' AND is_admin = 1")) > 0) {
    header("Location: ../login.php"); 
    exit();
}

$admin_mobile = $_SESSION['admin_mobile'] ?? 'System Admin';
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
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover { background-color: rgba(20, 184, 166, 0.1); color: #14B8A6; }
        .active-link { background-color: #14B8A6 !important; color: white !important; box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.3); }
        
        /* Premium Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #fafcff; border-radius: 10px; }
    </style>
</head>
<body class="flex min-h-screen">

<?php include 'sidebar.php'; ?>

<main class="flex-grow flex flex-col">
    <div class="p-10">