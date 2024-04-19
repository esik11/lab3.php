<?php
session_start(); // Start the session to manage user data

include('db_conn.php'); // Include the database connection file

if (isset($_GET['token'])) { // Check if 'token' parameter is present in the URL
    $token = $_GET['token']; // Get the token from the URL

    // Query to select user with the given token and who is not yet verified
    $verify_query = "SELECT * FROM users WHERE verify_token='$token' AND verified='0' LIMIT 1";
    $verify_query_run = mysqli_query($conn, $verify_query); // Execute the query

    // Check if there is a row returned by the query
    if (mysqli_num_rows($verify_query_run) > 0) {
        $row = mysqli_fetch_assoc($verify_query_run); // Fetch the user data
        $user_id = $row['user_id']; // Get the user's ID

        // Update user's verification status to '1' (verified)
        $update_query = "UPDATE users SET verified='1' WHERE user_id='$user_id'";
        $update_query_run = mysqli_query($conn, $update_query); // Execute the update query

        // Check if the update query was successful
        if ($update_query_run) {
            $_SESSION['status'] = "Your account has been verified successfully!"; // Set session status message
            header("Location: loginform.php"); // Redirect to the login page
            exit(); // Terminate script execution
        } else {
            $_SESSION['status'] = "Verification failed!"; // Set session status message
            header("Location: loginform.php"); // Redirect to the login page
            exit(); // Terminate script execution
        }
    } else {
        $_SESSION['status'] = "Invalid or expired token."; // Set session status message
        header("Location: loginform.php"); // Redirect to the login page
        exit(); // Terminate script execution
    }
} else {
    $_SESSION['status'] = "Token not found."; // Set session status message
    header("Location: loginform.php"); // Redirect to the login page
    exit(); // Terminate script execution
}
?>