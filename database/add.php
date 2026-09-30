<?php

include "database.php";

if(isset($_POST["save"])){

    $q = $_POST["question"];
    $a = $_POST["answer"];
    $k = $_POST["keywords"];

    mysqli_query($conn,

    "INSERT INTO knowledge(question,answer,keywords)

    VALUES

    ('$q','$a','$k')"

    );

    header("location:knowledge.php");
    exit;

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Add Knowledge | ASTRA AI</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    background:
    radial-gradient(circle at top,#003b46,#08111f 45%,#05070d);

    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;

}

.card{

    width:700px;
    max-width:95%;

    background:rgba(255,255,255,.05);

    backdrop-filter:blur(18px);

    border:1px solid rgba(0,255,255,.2);

    border-radius:18px;

    padding:35px;

    box-shadow:0 0 30px rgba(0,255,255,.25);

}

h1{

    text-align:center;

    color:#00ffff;

    margin-bottom:30px;

    letter-spacing:2px;

    text-shadow:0 0 15px cyan;

}

label{

    display:block;

    margin-bottom:8px;

    color:#9dfcff;

    font-weight:bold;

}

input,
textarea{

    width:100%;

    padding:14px;

    margin-bottom:22px;

    border:none;

    outline:none;

    border-radius:10px;

    background:rgba(255,255,255,.08);

    color:white;

    border:1px solid rgba(0,255,255,.15);

    transition:.3s;

    font-size:15px;

}

textarea{

    height:180px;

    resize:vertical;

}

input:focus,
textarea:focus{

    border-color:cyan;

    box-shadow:0 0 15px cyan;

}

.buttons{

    display:flex;

    justify-content:space-between;

    margin-top:15px;

}

.btn{

    padding:14px 28px;

    border:none;

    border-radius:8px;

    text-decoration:none;

    font-weight:bold;

    cursor:pointer;

    transition:.3s;

    font-size:15px;

}

.back{

    background:#2b2b2b;

    color:white;

}

.back:hover{

    background:#444;

}

.save{

    background:linear-gradient(45deg,#00bfff,#00ffff);

    color:black;

    box-shadow:0 0 18px cyan;

}

.save:hover{

    transform:translateY(-3px);

    box-shadow:0 0 28px cyan;

}

@media(max-width:700px){

.card{

padding:25px;

}

.buttons{

flex-direction:column;

gap:15px;

}

.btn{

width:100%;
text-align:center;

}

}

</style>

</head>

<body>

<div class="card">

<h1>📚 Add ASTRA Knowledge</h1>

<form method="POST">

<label>Question</label>

<input
type="text"
name="question"
placeholder="Enter the question..."
required>

<label>Answer</label>

<textarea
name="answer"
placeholder="Enter the answer..."
required></textarea>

<label>Keywords</label>

<input
type="text"
name="keywords"
placeholder="Example: library, books, location">

<div class="buttons">

<a href="knowledge.php" class="btn back">
⬅ Back
</a>

<button
type="submit"
name="save"
class="btn save">

💾 Save Knowledge

</button>

</div>

</form>

</div>

</body>

</html>