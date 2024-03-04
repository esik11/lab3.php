<?php
session_start();

include('db_conn.php');

if(isset($_GET['token'])) {
    $token = $_GET['token'];
    $verify_query = "SELECT verify_token, verified FROM users WHERE verify_token='$token' LIMIT 1";
    $verify_query_run = mysqli_query($conn, $verify_query);

    if(mysqli_num_rows($verify_query_run) > 0) {
        $row = mysqli_fetch_array($verify_query_run);
        if($row['verified'] == "0") {
            $clicked_token = $row['verify_token'];
            $update_query = "UPDATE users SET verified='1' WHERE verify_token='$clicked_token' LIMIT 1";
            $update_query_run = mysqli_query($conn, $update_query);

            if($update_query_run) {
                $_SESSION['status'] = "Your account has been verified successfully!";
                header("Location: loginform.php");
                exit();
            } else {
                $_SESSION['status'] = "Verification failed!";
                header("Location: loginform.php");
                exit();
            }
        } else {
            $_SESSION['status'] = "Email already verified, please log in";
            header("Location: loginform.php");
            exit();
        }
    } else {
        $_SESSION['status'] = "Token does not exist";
        header("Location: loginform.php");
        exit();
    }
} else {
    $_SESSION['status'] = "Not allowed";
    header("Location: loginform.php");
    exit();
}
?>
