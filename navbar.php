<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <?php if(!isset($_SESSION["username"])):?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php else:?>
            <li><a href="logout.php">logOut</a></li>
            <li><a href="cart.php">cart</a></li>
            <li><a href="orders.php">orders</a></li>
            <?php if($_SESSION["role"] == "admin") :?>
                <li><a href="admin.php">admin</a></li>
                <li><a href="stats.php">stats</a></li>
            <?php endif;?>
        <?php endif;?>
        
    </ul>
</nav>