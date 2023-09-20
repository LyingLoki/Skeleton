<?php
session_start();
require "db.php";

// Check if User is authorized to on Admin site
if (!isset($_SESSION["role"]) || $_SESSION["role"] != "admin")
{
    header("Location: index.php");
    exit;
}

// Get all Products
$stmt = $conn->prepare("SELECT * FROM Products");
$stmt->execute();
$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// set to null, cuz nothing is getting edited yet
$prod_to_edit = null;

// Add Product
if(isset($_POST["add_prod"]))
{
   
    // Set Image Path to not_found.png if no Image was selected
    $img_path = $_POST["prod_image"];
    if($img_path == null)
    {
        $img_path = "not_found.png";
    }

    // Insert Product into DB
    $stmt = $conn->prepare("INSERT INTO Products (name, price, description, image) VALUES (?,?,?,?)");
    if($stmt->execute([$_POST["prod_name"], $_POST["prod_price"], $_POST["prod_desc"], $img_path]))
    {
        header("Location: admin.php?message=Produkt+erfolgreich+hinzugefügt!");
        exit;
    }
    else
    {
        echo "unlucky";
    }

}

// Delete Product
if(isset($_GET["delete_prod"]))
{
    $prod_id = $_GET["delete_prod"];
    $stmt = $conn->prepare("DELETE FROM Products WHERE id = ?");
    if($stmt->execute([$prod_id]))
    {
        header("Location: admin.php?message=Produkt+erfolgreich+gelöscht!");
        exit;
    }
    else
    {
        echo "unable to delete product";
    }
}

// Update Product Details
if(isset($_GET["edit_prod"]))
{
    $prod_id = $_GET["edit_prod"];
    $stmt = $conn->prepare("SELECT * FROM Products WHERE id = ?");
    if($stmt->execute([$prod_id]))
    {
        $prod_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    }    
}

// Update Product with new Details
if(isset($_POST["update_prod"]))
{
    $prod_id = $_POST["update_prod_id"];
    $prod_price = $_POST["update_prod_price"];
    $prod_desc = $_POST["update_prod_desc"];
    $prod_name = $_POST["update_prod_name"];

    $stmt = $conn->prepare("UPDATE Products SET name = ?, price = ?, description = ?  WHERE id = ?");
    if($stmt->execute([$prod_name, $prod_price, $prod_desc,$prod_id]))
    {
        header("Location: admin.php?message=Produkt+erfolgreich+geupdated!");
        exit;
    }    
    else
    {
        echo "Unable to update prod";
    }
}

// Approve Product
if(isset($_GET["approve_prod"]))
{
    $prod_id = $_GET["approve_prod"];
    $stmt = $conn->prepare("UPDATE Products SET approved = 1 WHERE id = ?");
    if($stmt->execute([$prod_id]))
    {
        header("Location: admin.php?message=Produkt+erfolgreich+approved!");
        exit;
    }
}

// Unapprove Product
if(isset($_GET["unapprove_prod"]))
{
    $prod_id = $_GET["unapprove_prod"];
    $stmt = $conn->prepare("UPDATE Products SET approved = 0 WHERE id = ?");
    if($stmt->execute([$prod_id]))
    {
        header("Location: admin.php?message=Produkt+erfolgreich+UNapproved!");
        exit;
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brotherman_Admin</title>
</head>
<body>
    <!-- Include NavBar -->
    <?php include "navbar.php"?>
    <h2>Adminbereich</h2>

    <?= $_GET["message"] ?>

    <h2> Produkt hinzufügen </h2>
    <form action="admin.php" method="post">  
        <div class="container">   
            <label>Produktname: </label>   
            <input type="text" placeholder="Enter Productname" name="prod_name" required>  
            <label>Preis: </label>   
            <input type="number" step="0.01" placeholder="Enter Price" name="prod_price" required>  
            <label>Beschreibung: </label>   
            <input type="text" placeholder="Enter Description" name="prod_desc" required>  
            <label>Bild: </label>   
            <input type="file" name="prod_image">
            
            <button type="submit" name="add_prod">Add</button>      
        </div>   
    </form>
    
    <h2> Produkt Liste </h2>
    <?php foreach($allProducts as $product):?> 
        <label> <?= $product["name"] ?> </label>
        <a href="admin.php?delete_prod=<?=$product["id"]?>">Delete</a>
        <a href="admin.php?edit_prod=<?=$product["id"]?>">Edit</a>
        <?php if($product["approved"] == 0) : ?>
            <a href="admin.php?approve_prod=<?=$product["id"]?>">Approve</a>
        <?php else: ?>
            <a href="admin.php?unapprove_prod=<?=$product["id"]?>">UNapprove</a>
        <?php endif;?>
                
        <br>
    <?php endforeach; ?>
        
    <?php if(isset($prod_to_edit)) :?>
    <h2> Edit Product </h2>
        <form action="admin.php" method="post">  
            <div class="container">   
                <label>Produktname: </label>   
                <input type="text" value="<?= $prod_to_edit["name"]?>" name="update_prod_name">  
                <label>Preis: </label>   
                <input type="number" step="0.01" value="<?= $prod_to_edit["price"]?>" name="update_prod_price" required>  
                <label>Beschreibung: </label>   
                <input type="text" value="<?= $prod_to_edit["description"]?>" name="update_prod_desc" required>    
                <input type="hidden" value="<?= $prod_to_edit["id"]?>" name="update_prod_id" required>  
                
                <button type="submit" name="update_prod">Update</button>   
            </div>   
        </form>
    <?php endif; ?>



</body>
</html>