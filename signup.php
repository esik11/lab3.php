<?php
session_start(); // Start the session to manage user data

include "db_conn.php"; // Include the database connection file

use PHPMailer\PHPMailer\PHPMailer; // Import PHPMailer class
use PHPMailer\PHPMailer\SMTP; // Import PHPMailer SMTP class
use PHPMailer\PHPMailer\Exception; // Import PHPMailer Exception class

require 'vendor/autoload.php'; // Require autoload file to load PHPMailer library

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Function to validate and sanitize input data
    function validate($data)
    {
        $data = trim($data); // Remove whitespace from the beginning and end of the string
        $data = stripslashes($data); // Remove backslashes (\)
        $data = htmlspecialchars($data); // Convert special characters to HTML entities
        return $data; // Return the sanitized data
    }

    // Validate and sanitize input fields
    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $confirm_password = validate($_POST['confirm_password']);
    $emailaddress = validate($_POST['emailaddress']);
    $firstname = validate($_POST['firstname']);
    $middlename = validate($_POST['middlename']);
    $lastname = validate($_POST['lastname']);
    $gmail_password = validate($_POST['gmail_password']);

    // Checking if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['status'] = "Passwords do not match."; // Set session status message
        header("Location: signup.php"); // Redirect back to signup page
        exit(); // Terminate script execution
    }

    // check if any field is empty
    if (empty($username) || empty($password) || empty($confirm_password) || empty($emailaddress) || empty($firstname) || empty($middlename) || empty($lastname) || empty($gmail_password)) {
        $_SESSION['status'] = "All fields are required."; // Set session status message
        $_SESSION['username'] = $username;
        $_SESSION['emailaddress'] = $emailaddress;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['middlename'] = $middlename;
        $_SESSION['lastname'] = $lastname;
        header("Location: signup.php"); //redirect back to signup page
        exit(); //terminate script execution
    } elseif ($password !== $confirm_password) { //check again for passwords match
        $_SESSION['status'] = "Passwords do not match."; //set session status message
        $_SESSION['username'] = $username;
        $_SESSION['emailaddress'] = $emailaddress;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['middlename'] = $middlename;
        $_SESSION['lastname'] = $lastname;
        header("Location: signup.php"); //redirect back to signup page
        exit(); //terminate script execution
    }elseif ($firstname === $middlename || $middlename === $lastname || $firstname === $lastname) {
        $_SESSION['status'] = "First name, middle name, and last name cannot be the same."; // Set session status message
        $_SESSION['username'] = $username;
        $_SESSION['emailaddress'] = $emailaddress;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['middlename'] = $middlename;
        $_SESSION['lastname'] = $lastname;
        header("Location: signup.php"); // Redirect back to signup page
        exit(); // Terminate script execution
    } 
    else {
        // database operations
        $verify_token = md5(rand()); // Generate a verification token

        // Storing email address or username in the 'email' field of the database
        $email_to_store = $emailaddress;

        // check if the email address already exists in the database
        $check_email_query = "SELECT email FROM users WHERE LOWER(email) = LOWER('$email_to_store') LIMIT 1";
        $check_email_query_run = mysqli_query($conn, $check_email_query);

        if (mysqli_num_rows($check_email_query_run) > 0) {
            $_SESSION['status'] = "Email ID already exists. Please use another email address."; // Set session status message
            header("Location: signup.php"); // Redirect back to signup page
            exit(); // Terminate script execution
        }

        // Insert user data into the database
        $sql = "INSERT INTO users (username, password, first_name, middle_name, last_name, email, verify_token) 
                VALUES ('$username', '$password', '$firstname', '$middlename', '$lastname', '$email_to_store', '$verify_token')";

        if (mysqli_query($conn, $sql)) {
            // Registration successful message
            $_SESSION['status'] = "Registration successful. Please verify your email."; // Set session status message
            
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
                $_SESSION['status'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; // Set session status message
                header("Location: signup.php"); // Redirect back to signup page
                exit(); // Terminate script execution
            }
            header("Location: signup.php"); // Redirect back to signup page
            exit(); // Terminate script execution
        } else {
            $_SESSION['status'] = "Error occurred while registering user."; // Set session status message
            header("Location: signup.php"); // Redirect back to signup page
            exit(); // Terminate script execution
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
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['status']; ?>
            </div>
            <?php unset($_SESSION['status']); ?>
        <?php } ?>
        <form action="signup.php" method="post">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="First Name" value="<?php echo isset($_SESSION['firstname']) ? $_SESSION['firstname'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="middlename">Middle Name</label>
                <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middle Name" value="<?php echo isset($_SESSION['middlename']) ? $_SESSION['middlename'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Last Name" value="<?php echo isset($_SESSION['lastname']) ? $_SESSION['lastname'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="username">User Name</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="User Name" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
            </div>
            <div class="form-group">
                <label for="emailaddress">Email Address</label>
                <input type="email" class="form-control" id="emailaddress" name="emailaddress" placeholder="Email Address" value="<?php echo isset($_SESSION['emailaddress']) ? $_SESSION['emailaddress'] : ''; ?>">
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

