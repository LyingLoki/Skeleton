<?php
session_start();
require "db.php";

$errmessage = "";

if($_SERVER["REQUEST_METHOD"] === "POST")
{
    // Register Request has been made
    // PW Verification then add user in DB

    $pwClear = $_POST["password"];

    $uppercase = preg_match('@[A-Z]@', $pwClear);
    $lowercase = preg_match('@[a-z]@', $pwClear);
    $number    = preg_match('@[0-9]@', $pwClear);

    // Ignore PW Convetions for now for testing
    // if(!$uppercase || !$lowercase || !$number || strlen($pwClear) < 8) {
    if(false) {
        $errmessage = "Please use the right PW conventions";
    }
    else
    {
        $pw = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $username = $_POST["username"];
        $email = $_POST["email"];

        $stmt = $conn->prepare("INSERT INTO User (username, email, password) VALUES (?,?,?)");
        if($stmt->execute([$username,$email,$pw]))
        {
            $_SESSION["regMsg"] = "Registered successfully";
            header("Location: login.php");
            exit;
        }
        else
        {
            $errmessage = "Something went wrong";
        }
    }

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brotherman_Register</title>
</head>
<body>
    <!-- Include NavBar -->
    <?php include 'navbar.php';
    if ($errmessage) {
        echo "<p>$errmessage</p>";
    }
    ?>

    <!-- Form for register -->
    <form action="register.php" method="post">  
        <div class="container">   
            <label>Username: </label>   
            <input type="text" placeholder="Enter Username" name="username" required>  
            <label>Email: </label>   
            <input type="text" placeholder="Enter EMail" name="email" required>  
            <label>Password: </label>   
            <input type="password" placeholder="Enter Password" name="password" required>  
            <button type="submit">Register</button>
        </div>   
    </form> 

</body>
</html>