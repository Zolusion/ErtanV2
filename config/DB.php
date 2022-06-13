<?php
$host = "localhost";
$user = "";
$pass = "";
$dbname = "ertanv2_login";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $error) {
    echo "Database connection failed!" . $error->getMessage();
}
?>