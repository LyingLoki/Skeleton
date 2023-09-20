<?php
session_start();
require "db.php";

$errmessage = "";


// wenn Post request von login kommt
if($_SERVER["REQUEST_METHOD"] === "POST")
{
    
    // Get User with username
    $stmt = $conn->prepare("SELECT * FROM User Where username = ?");
    $stmt->execute([$_POST["username"]]);    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(password_verify($_POST["password"], $user["password"]))
    {
        // PWs match -> success
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["user_email"] = $user["email"];
        $_SESSION["user_id"] = $user["id"];

        header("Location: index.php");
        exit;
    }
    else
    {
        $errmessage = "No user found with username";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brotherman_Login</title>
</head>
<body>
    <?php include "navbar.php"?>

    <?php echo $_SESSION["regMsg"]; ?>
    <form action="login.php" method="post">  
        <div class="container">   
            <label>Username: </label>   
            <input type="text" placeholder="Enter Username" name="username" required>  
            <label>Password: </label>   
            <input type="password" placeholder="Enter Password" name="password" required>  
            <button type="submit">Login</button>      
        </div>   
    </form> 

</body>
</html>