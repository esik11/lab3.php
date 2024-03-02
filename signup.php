<?php
session_start();
include "db_conn.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_verify($username, $emailaddress)
{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = "smtp.gmail.com";
    $mail->Username = "jeorgeandreielevencionado@gmail.com";
    $mail->Password = "jeorgeandrei679";

    $mail->SMTPSecure = "tls";
    $mail->Port = 587;

    $mail->setFrom("jeorgeandreielevencionado@gmail.com", $username);
    $mail->addAddress($emailaddress);
    $mail->isHTML(true);
    $mail->Subject = "Email Verification";
    
    $email_template  = "<h2>You have Registered Successfully</h2>"
                     . "<h5>Verify your email address to login</h5>"
                     . "<br/><br/>"
                     . "<a href='http://localhost/lab3.php/verify-email.php'>Verify Email</a>";
    $mail->Body = $email_template;
    
    if ($mail->send()) {
        echo "Message has been sent";
    } else {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}





if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Validate and sanitize input
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $emailaddress = validate ($_POST['email']);
    $first_name = validate ($_POST ['firstname']);
    $middle_name = validate ($_POST ['middlename']);
    $last_name = validate ($_POST ['lastname']);
    
    $emailaddress = strtolower(validate($_POST['emailaddress']));

    // Check if email already exists
    $check_email_query = "SELECT email FROM users WHERE LOWER(email) = '$emailaddress' LIMIT 1";
    $check_email_query_run = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['status'] = "Email ID already exists PLEASE INPUT ANOTHER";
        header("Location: signup.php");
        exit();
    }

    // Insert user into database
    if (empty($username) || empty($password) || empty($emailaddress) || empty($first_name) || empty($middle_name) || empty($last_name) ) {
        header("Location: signup.php?error=Username, Password and Email Address are required");
        exit();
    } else {
        $sql = "INSERT INTO users (username, password, first_name, middle_name, last_name, email) VALUES ('$username', '$password' , '$first_name' ,'$middle_name', '$last_name', '$emailaddress' )";

        if (mysqli_query($conn, $sql)) {

            sendemail_verify("$username", "$emailaddress");
            header("Location: loginform.php?success=User registered successfully");
            exit();
        } else {
            header("Location: signup.php?error=Error occurred while registering user");
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
            width: 400px; /* Adjust the width as needed */
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
    </style>
</head>
<body>
<div class="container-box">
    <h2>Signup</h2>
    <?php if (isset($_SESSION['status'])) { ?>
        <p class="error"><?php echo $_SESSION['status']; ?></p>
        <?php unset($_SESSION['status']); ?>
    <?php } ?>
    <form action="signup.php" method="post">
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo $_GET['error']; ?></p>
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
            <input type="email" class="form-control" id="emailaddress" name="emailaddress" placeholder="Email Address" required="required">
        </div>              
        <button type="submit" class="btn btn-primary btn-block">Signup</button>
    </form>
</div>
</body>
</html>


