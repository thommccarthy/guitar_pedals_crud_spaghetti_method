<?php

//new instance of PDO (php data object)
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
//PDO error handling - !!read docs
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//if id exists, assign. otherwise null
$id = $_POST['id'] ?? null;

//if no id exists, redirect user back to index 
if (!$id) {
    header('Location: index.php');
    exit;
}

$statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();
//redirect user to index
header("Location: index.php");
