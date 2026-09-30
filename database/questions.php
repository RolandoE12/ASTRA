<?php

session_start();

if(!isset($_SESSION["admin"])){

    header("location:login.php");
    exit;

}

include "database.php";

$questions=mysqli_query(
$conn,
"SELECT * FROM unanswered_questions
WHERE status='pending'
ORDER BY id DESC"
);

?>

<!DOCTYPE html>

<html>

<head>

<title>New Student Questions | ASTRA AI</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    height:100vh;
    min-height:100vh;
    padding:30px;
    color:white;
    background:
    radial-gradient(circle at top,#004d5e,#07131f 40%,#03070d);
    overflow-x:hidden;
    position:relative;
}

/* Animated Grid Background */

body::before{

    content:"";
    position:fixed;
    inset:0;

    background:
    linear-gradient(
    90deg,
    transparent 95%,
    rgba(0,255,255,.15) 96%
    ),
    linear-gradient(
    transparent 95%,
    rgba(0,255,255,.15) 96%
    );

    background-size:50px 50px;

    animation:gridMove 10s linear infinite;

    opacity:.4;

    pointer-events:none;

    z-index:-1;

}

@keyframes gridMove{

from{
transform:translateY(0);
}

to{
transform:translateY(50px);
}

}

.container{

    width:96%;
    margin:auto;
    position:relative;
    z-index:1;

}

h1{

    text-align:center;
    margin-bottom:25px;
    color:#00ffff;
    letter-spacing:2px;
    text-shadow:0 0 15px cyan;

}

.top-bar{

    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    margin-bottom:20px;

}

.btn{

    display:inline-block;
    padding:12px 22px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
    transition:.3s;

}

.back{

    background:#222;
    color:white;
    border:1px solid #555;

}

.back:hover{

    background:#333;

}

.table-box{

    background:rgba(255,255,255,.05);
    backdrop-filter:blur(15px);
    border:1px solid rgba(0,255,255,.2);
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 0 30px rgba(0,255,255,.2);

}

table{

    width:100%;
    border-collapse:collapse;

}

th{

    background:#00d9ff;
    color:#000;
    padding:15px;
    text-align:left;

    border-right:2px solid rgba(0,0,0,.25);

}

th:last-child{

    border-right:none;

}

td{

    padding:15px;
    border-bottom:1px solid rgba(255,255,255,.08);
    border-right:1px solid rgba(0,255,255,.25);
    vertical-align:top;

}

td:last-child{

    border-right:none;

}

tr:hover{

    background:rgba(0,255,255,.08);

}

textarea{

    width:100%;
    min-height:140px;
    padding:14px;
    border:none;
    outline:none;
    border-radius:10px;
    resize:vertical;
    color:white;
    font-size:15px;

    background:rgba(255,255,255,.08);

    border:2px solid rgba(0,255,255,.25);

    transition:.3s;

}

textarea::placeholder{

    color:#bbb;

}

textarea:focus{

    border-color:#00ffff;

    box-shadow:0 0 18px cyan;

}

.action-buttons{

    display:flex;
    flex-direction:column;
    gap:12px;

}

button{

    width:100%;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;

}

.save-btn{

    background:linear-gradient(45deg,#00d2ff,#00ffff);
    color:black;
    box-shadow:0 0 15px cyan;

}

.save-btn:hover{

    transform:translateY(-3px);
    box-shadow:0 0 25px cyan;

}

.delete-btn{

    background:#ff3b3b;
    color:white;

}

.delete-btn:hover{

    background:#d90000;
    transform:translateY(-3px);
    box-shadow:0 0 20px red;

}

.action-buttons form{

    width:100%;

}

@media(max-width:900px){

.table-box{

overflow-x:auto;

}

table{

min-width:950px;

}

}

</style>

</head>

<body>

<div class="container">

<h1>NEW STUDENT QUESTIONS</h1>

<div class="top-bar">

<a href="dashboard.php" class="btn back">
⬅ Dashboard
</a>

</div>

<div class="table-box">

<table>

<tr>

<th width="30%">Student Question</th>

<th width="50%">ASTRA Answer</th>

<th width="20%">Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($questions)){ ?>

<tr>

<form method="POST" action="answer_question.php">

<td>

<strong><?= htmlspecialchars($row["question"]) ?></strong>

<input
type="hidden"
name="id"
value="<?= $row["id"] ?>">

</td>

<td>

<textarea
name="answer"
placeholder="Type ASTRA answer..."
required></textarea>

</td>

<td>

<div class="action-buttons">

<button
class="save-btn"
type="submit">

 Save

</button>

</form>

<form
method="POST"
action="delete_question.php"
onsubmit="return confirm('Delete this question?');">

<input
type="hidden"
name="id"
value="<?= $row["id"] ?>">

<button
class="delete-btn"
type="submit">

 Delete

</button>

</form>

</div>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>