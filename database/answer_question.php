<?php

session_start();

if(!isset($_SESSION["admin"])){

header("location:login.php");
exit;

}


include "database.php";


$id = $_POST["id"];
$answer = $_POST["answer"];


// GET QUESTION FIRST

$getQuestion = mysqli_query(
$conn,
"SELECT question FROM unanswered_questions WHERE id='$id'"
);


$row = mysqli_fetch_assoc($getQuestion);


$question = $row["question"];



// SAVE TO KNOWLEDGE DATABASE

$safeQuestion = mysqli_real_escape_string($conn, $question);
$safeAnswer = mysqli_real_escape_string($conn, $answer);

$saveKnowledge = mysqli_query(
$conn,
"INSERT INTO knowledge
(question, answer, keywords)
VALUES
('$safeQuestion','$safeAnswer','')"
);



// UPDATE UNANSWERED QUESTION

$updateQuestion = mysqli_query(
$conn,
"UPDATE unanswered_questions

SET

answer='$answer',

status='answered'

WHERE id='$id'"
);



if($saveKnowledge && $updateQuestion){


header("location:".$_SERVER['HTTP_REFERER']);


}

else{


echo "Error saving answer";


}



?>