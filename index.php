<?php
session_start();
require "db.php";

if(isset($_GET["product_id"]))
{
    // Get only one Product info
    $stmt = $conn->prepare("SELECT * FROM Products WHERE id = ?");
    $stmt->execute([$_GET["product_id"]]);
    $productDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}
else
{
    // Get all approved Products
    $stmt = $conn->prepare("SELECT * FROM Products WHERE approved = 1");
    $stmt->execute();
    $allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if(isset($_POST["addToCart"]))
{
    // Check if Product already in Cart
    $stmt = $conn->prepare("SELECT * FROM Cart WHERE session_ID = ? AND product_ID = ?");
    $stmt->execute([session_id(), $_POST["prod_id"]]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    // Already in Cart
    if($item)
    {
        $newQty = $item["quantity"] + $_POST["prod_qty"];
        $stmt = $conn->prepare("UPDATE Cart SET quantity = ? WHERE session_ID = ? AND product_ID = ?");
        $stmt->execute([$newQty, session_id(), $_POST["prod_id"]]);
        header("Location: cart.php");
        exit;
    }
    else
    {
        // Product not in Cart
        $stmt = $conn->prepare("INSERT INTO Cart (session_ID, product_ID, quantity) VALUES (?,?,?)");
        $stmt->execute([session_id(),$_POST["prod_id"],$_POST["prod_qty"]]);
        header("Location: cart.php");
        exit;
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brotherman</title>
    <style>
        img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
        }

        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Abstand zwischen den Produkten */
            justify-content: flex-start;
            /* Produkte werden am Beginn des Containers ausgerichtet */
        }

        .product {
            flex: 1;
            /* Jedes Produkt kann wachsen, um verfügbaren Platz auszufüllen */
            min-width: calc(50% - 20px);
            /* Es werden maximal 2 Produkte pro Zeile angezeigt. 20px ist der Abstand zwischen den Produkten. */
        }
    </style>
</head>
<body>

    <!-- <?= $_SESSION["username"]?>
    <?= $_SESSION["role"]?>
    <?= $_SESSION["user_email"]?>
    <?= $_SESSION["user_id"]?> -->

    <?php include "navbar.php"?>


    <?php if (isset($productDetails)) :?>
        <h2> Produkt Details </h2>
        <h2> <?= $productDetails["name"] ?> </h2>
        <img src="images/<?=$productDetails["image"]?>" alt="<?= $productDetails["name"]?>"/>
        <p>€<?= number_format($productDetails["price"], 2, ",",".") ?> </p>
        <p><?= $productDetails["description"]?></p>
        <form action="index.php" method="POST">
            <input type="number" value="1" name="prod_qty" min=1 required> 
            <input type="hidden" name="prod_id" value="<?= $productDetails["id"]?>"> 
            <button type="submit" name="addToCart"> Add To Cart </button>
        </form>

    <?php else: ?>
        <h2> Produkt Liste </h2>
        <div class="products">
            <?php foreach($allProducts as $product): ?>
                <div class="product">
                    <a href="index.php?product_id=<?=$product["id"]?>"> <?= $product["name"] ?> </a>
                    <img src="images/<?=$product["image"]?>" alt="<?= $product["name"]?>"/>
                    <p>€<?= number_format($product["price"], 2, ",",".") ?> </p>
                    <p><?= $product["description"]?></p>
                    <br>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>