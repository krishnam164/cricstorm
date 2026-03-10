<?php
// 1. Initialize the session
session_start();

// 2. Unset all of the session variables
// This clears user_id, user_role, admin_id, and is_admin
$_SESSION = array();

// 3. If it's desired to kill the session, also delete the session cookie
// This is an extra layer of security for professional portals
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finally, destroy the session
session_destroy();

// 5. Redirect to the main login page
header("Location: index.php");
exit();
?>