<?php
require '../config.php';
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $mobile = $_POST['mobile_no'];
    $password = md5($_POST['password']);

    // 1. PRIMARY CHECK: User Master
    // We check mobile_no AND ensure status is 'Publish' (Draft users cannot log in)
    $sql = "SELECT * FROM user_master WHERE mobile_no='$mobile' AND password='$password'";
    echo "Debug SQL: $sql"; // Debugging line to check the generated SQL query
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // 2. STATUS GATEKEEPER
        if($row['status'] !== 'Publish') {
            echo "<script>alert('Account Pending: Your status is currently {$row['status']}. Contact Admin.'); window.location.href='index.php';</script>";
            exit();
        }

        // 3. ADMIN GATEKEEPER
        if($row['is_admin'] == 1) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['mobile_no'] = $row['mobile_no'];

            // Log the successful entrance
            $id = $row['user_id'];
            $ip = $_SERVER['REMOTE_ADDR'];
            mysqli_query($conn, "INSERT INTO login_tracking(user_id, ip_address, content, is_online) 
                                 VALUES ('$id', '$ip', 'Terminal Access Granted','online')");

            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Denied: You do not have Admin privileges.'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Error: Mobile number or Security Key is incorrect.'); window.location.href='index.php';</script>";
        exit();
    }
}