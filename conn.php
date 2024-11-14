<?php 

$host = 'localhost';
$db = 'newyoga';
$username = 'root';
$password = '';
try{
    $conn =  new PDO("mysql:host=$host;dbname=$db",$username,$password);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){
    die("connection fail:" .$e->getMessage());
}
header('Content-Type: application/json');
?>