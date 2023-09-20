<?php
// PDO / MySQLi??
include "config.php";
$conn = new PDO("mysql:host=$DB_host;dbname=$DB_name", $DB_user, $DB_pw);
?>