<?php
session_start(); // Start session to manage user data across requests
include "db_conn.php"; // Include the database connection file

// Check if username and password are submitted via POST request
if (isset($_POST['username']) && isset($_POST['password'])) {
    // Function to validate input data
    function validate($data){
        $data = trim($data); // Remove whitespace from the beginning and end of the string
        $data = stripslashes($data); // Remove backslashes (\)
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data;
    }

    // Validate and sanitize username and password
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    // Check if username is empty
    if (empty($username)) {
        header("Location: loginform.php?error=User Name is required"); // Redirect with error message
        exit(); // Terminate script execution
    } elseif (empty($password)) { // Check if password is empty
        header("Location: loginform.php?error=Password is required"); // Redirect with error message
        exit(); // Terminate script execution
    } else {
        // SQL query to select user based on username
        $sql = "SELECT * FROM users WHERE username='$username'";
        // Execute SQL query
        $result = mysqli_query($conn, $sql);

        // Check if only one row is returned (valid username)
        if(mysqli_num_rows($result) === 1){
            // Fetch the associative array containing user data
            $row = mysqli_fetch_assoc($result);
            // Check if the user's email is verified
            if($row['verified'] == 1){
                // Check if the entered password matches the stored password
                if($row['password'] === $password){
                    // Set session variables with user data
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['id'] = $row['user_id'];
                    // Redirect to home page with success message
                    header("Location: home.php?message=Login successful");
                    exit(); // Terminate script execution
                } else {
                    // Redirect with error message for incorrect password
                    header("Location: loginform.php?error=Incorrect Password");
                    exit(); // Terminate script execution
                }
            } else {
                // Redirect with error message for unverified email
                header("Location: loginform.php?error=Please verify your email");
                exit(); // Terminate script execution
            }
        } else {
            // Redirect with error message for incorrect username
            header("Location: loginform.php?error=Incorrect User name");
            exit(); // Terminate script execution
        }
    }
}
?>