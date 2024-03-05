<?php
session_start();
include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate and sanitize input fields
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $emailaddress = validate($_POST['emailaddress']);
    $firstname = validate($_POST['firstname']);
    $middlename = validate($_POST['middlename']);
    $lastname = validate($_POST['lastname']);
    $gmail_password = validate($_POST['gmail_password']);

    // Check if any field is empty
    if (empty($username) || empty($password) || empty($emailaddress) || empty($firstname) || empty($middlename) || empty($lastname) || empty($gmail_password)) {
        $_SESSION['status'] = "All fields are required.";
        header("Location: signup.php");
        exit();
    } else {
        // Proceed with database operations
        $verify_token = md5(rand());

        // Store email address or username in the 'email' field of the database
        // Since you want to use the email address as the email username, you can directly use it
        $email_to_store = $emailaddress;

        // Check if the email address already exists
        $check_email_query = "SELECT email FROM users WHERE LOWER(email) = LOWER('$email_to_store') LIMIT 1";
        $check_email_query_run = mysqli_query($conn, $check_email_query);

        if (mysqli_num_rows($check_email_query_run) > 0) {
            $_SESSION['status'] = "Email ID already exists. Please use another email address.";
            header("Location: signup.php");
            exit();
        }

        // Insert user data into the database
        $sql = "INSERT INTO users (username, password, first_name, middle_name, last_name, email, verify_token) 
                VALUES ('$username', '$password', '$firstname', '$middlename', '$lastname', '$email_to_store', '$verify_token')";

        if (mysqli_query($conn, $sql)) {
            // Registration successful message
            $_SESSION['status'] = "Registration successful. Please verify your email.";
            // Perform email sending here
            // Configure PHPMailer instance and send verification email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $emailaddress; // Your Gmail address
                $mail->Password = $gmail_password; // Your Gmail password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipient
                $mail->setFrom($emailaddress, 'Your Name');
                $mail->addAddress($emailaddress); // Add recipient

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification';
                $mail->Body    = "Click the following link to verify your email address: http://localhost/lab3.php/verify-email.php?token=$verify_token";
                $mail->AltBody = 'Please verify your email address.';

                $mail->send();
            } catch (Exception $e) {
                $_SESSION['status'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                header("Location: signup.php");
                exit();
            }
            header("Location: signup.php");
            exit();
        } else {
            $_SESSION['status'] = "Error occurred while registering user.";
            header("Location: signup.php");
            exit();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('space.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container-box {
            width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .container-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .container-box .form-group label {
            font-weight: bold;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container-box">
        <h2>Signup</h2>
        <?php if (isset($_SESSION['status'])) { ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['status']; ?>
            </div>
            <?php unset($_SESSION['status']); ?>
        <?php } ?>
        <form action="signup.php" method="post">
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_GET['error']; ?>
                </div>
            <?php } ?>
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name">
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name</label>
                <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middle Name">
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name">
            </div>
            <div class="form-group">
                <label for="username">User Name</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="User Name">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="emailaddress">Email Address</label>
                <input type="email" class="form-control" id="emailaddress" name="emailaddress" placeholder="Email Address">
            </div>
            <div class="form-group">
                <label for="gmail_password">Email Password</label>
                <input type="password" class="form-control" id="gmail_password" name="gmail_password" placeholder="Email Password">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Signup</button>
            <a href="loginform.php" class="btn btn-secondary btn-block">Back to Login</a>
        </form>
    </div>
</body>

</html>