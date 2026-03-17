<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "roomora";

$db = mysqli_connect($host,$username,$password,$database);

if(!$db){
    die("Error:".mysqli_connect_error());

}

?>