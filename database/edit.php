<?php

include "database.php";

$id=$_GET["id"];

$data=mysqli_query(
$conn,
"SELECT * FROM knowledge WHERE id=$id"
);

$row=mysqli_fetch_assoc($data);

if(isset($_POST["update"])){

$q=$_POST["question"];
$a=$_POST["answer"];
$k=$_POST["keywords"];

mysqli_query($conn,

"UPDATE knowledge SET

question='$q',

answer='$a',

keywords='$k'

WHERE id=$id"

);

header("location:knowledge.php");
exit;

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Edit Knowledge | ASTRA AI</title>

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

display:flex;
justify-content:center;
align-items:center;
min-height:100vh;

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

text-shadow:0 0 12px cyan;

}

label{

display:block;

margin-bottom:8px;

font-weight:bold;

color:#9dfcff;

}

input,
textarea{

width:100%;

padding:14px;

border:none;

outline:none;

border-radius:10px;

background:rgba(255,255,255,.08);

color:white;

font-size:15px;

margin-bottom:22px;

border:1px solid rgba(0,255,255,.15);

transition:.3s;

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

text-decoration:none;

border-radius:8px;

font-weight:bold;

transition:.3s;

cursor:pointer;

border:none;

font-size:15px;

}

.back{

background:#2d2d2d;

color:white;

}

.back:hover{

background:#444;

}

.update{

background:linear-gradient(45deg,#00bfff,#00ffff);

color:black;

box-shadow:0 0 18px cyan;

}

.update:hover{

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

<h1>✏ Edit Knowledge</h1>

<form method="POST">

<label>Question</label>

<input
type="text"
name="question"
value="<?= htmlspecialchars($row['question']) ?>"
required>

<label>Answer</label>

<textarea
name="answer"
required><?= htmlspecialchars($row['answer']) ?></textarea>

<label>Keywords</label>

<input
type="text"
name="keywords"
value="<?= htmlspecialchars($row['keywords']) ?>"
placeholder="Example: library, books, location"
required>

<div class="buttons">

<a href="knowledge.php" class="btn back">
⬅ Back
</a>

<button
class="btn update"
name="update">
💾 Update Knowledge
</button>

</div>

</form>

</div>

</body>

</html>