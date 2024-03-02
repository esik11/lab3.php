<?php
session_start();

// Check if the user is not logged in, redirect to loginform.php
if (!isset($_SESSION['username'])) {
    header("Location: loginform.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container-box {
            margin-top: 50px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-box">
            <h1>Welcome <?php echo $_SESSION['name']; ?>!!</h1>
            <a href="logout.php" class="btn btn-danger">Logout</a> <!-- Updated logout link -->
        </div>
    </div>
</body>
</html>
