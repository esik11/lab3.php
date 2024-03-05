<?php
session_start();

include('db_conn.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $verify_query = "SELECT * FROM users WHERE verify_token='$token' AND verified='0' LIMIT 1";
    $verify_query_run = mysqli_query($conn, $verify_query);

    if (mysqli_num_rows($verify_query_run) > 0) {
        $row = mysqli_fetch_assoc($verify_query_run);
        $user_id = $row['user_id'];

        // Update user's verification status to '1' (verified)
        $update_query = "UPDATE users SET verified='1' WHERE user_id='$user_id'";
        $update_query_run = mysqli_query($conn, $update_query);

        if ($update_query_run) {
            $_SESSION['status'] = "Your account has been verified successfully!";
            header("Location: loginform.php");
            exit();
        } else {
            $_SESSION['status'] = "Verification failed!";
            header("Location: loginform.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Invalid or expired token.";
        header("Location: loginform.php");
        exit();
    }
} else {
    $_SESSION['status'] = "Token not found.";
    header("Location: loginform.php");
    exit();
}

?>
