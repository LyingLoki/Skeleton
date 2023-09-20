<?php
session_start();
require 'db.php';

// Get Cart of Current SessionID
$stmt = $conn->prepare("SELECT * FROM Cart JOIN Products ON Cart.product_ID = Products.id WHERE Cart.session_id = ?");
$stmt->execute([session_id()]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Get Total sum of cart
$str = "SELECT sum(Products.price * Cart.quantity) as total FROM Products JOIN Cart ON Products.id = Cart.product_id WHERE Cart.session_id = ?";
$stmt = $conn->prepare($str);
$stmt->execute([session_id()]);
$cart_total = $stmt->fetch(PDO::FETCH_ASSOC);

if($cart_total["total"] != null)
{
    $cart_sum = $cart_total["total"];
}
else
{
    $cart_sum = 0;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>brotherman_Cart</title>

    <style>
        img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
        }
    </style>

</head>
<body>
    <?php include "navbar.php"?>

    <h2>Cart </h2>
    <form action="cart.php" method="post">
        <?php foreach($cart as $product) :?>
            <img src="images/<?=$product["image"]?>" alt="<?= $product["name"]?>"/>
            <?= $product['name'] ?> | €<?= $product['price'] ?>
            <input type="number" value="<?=$product['quantity']?>" name="prod_qty" min=1 required>
            <label>Total: €<?= $product['price']*$product['quantity'] ?></label>
        <?php endforeach; ?>
        <br><button type="submit" name="update_cart">Update Cart</button>
    </form>
    <label>Cart Total: <?=$cart_sum?></label>
    
</body>
</html>