<?php
session_start();
include "db_conn.php";

if (isset($_POST['username']) && isset($_POST['password'])) {
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    if (empty($username)) {
        header("Location: loginform.php?error=User Name is required");
        exit();
    } elseif (empty($password)) {
        header("Location: loginform.php?error=Password is required");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE username='$username'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) === 1){
            $row = mysqli_fetch_assoc($result);
            if($row['verified'] == 1){ // Check if the user's email is verified
                if($row['password'] === $password){
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['id'] = $row['user_id'];
                    header("Location: home.php?message=Login successful");
                    exit();
                } else {
                    header("Location: loginform.php?error=Incorrect Password");
                    exit();
                }
            } else {
                header("Location: loginform.php?error=Please verify your email");
                exit();
            }
        } else {
            header("Location: loginform.php?error=Incorrect User name");
            exit();
        }
    }
}
?>