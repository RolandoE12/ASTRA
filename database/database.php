<?php

$server="localhost";
$username="astra";
$password="Astra123!";
$database="astra_ai";


$conn=mysqli_connect(
$server,
$username,
$password,
$database
);


if(!$conn){

die("Database Connection Failed");

}

?>