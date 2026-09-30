<?php

session_start();

if(!isset($_SESSION["admin"])){

header("location:login.php");
exit;

}


include "database.php";


$id=$_POST["id"];



$sql="DELETE FROM unanswered_questions
WHERE id='$id'";


if(mysqli_query($conn,$sql)){


header("location:".$_SERVER['HTTP_REFERER']);


}else{


echo "Error deleting question";


}


?>