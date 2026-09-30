<?php

session_start();

if(!isset($_SESSION["admin"])){

header("location:login.php");
exit;

}

include "database.php";


// COUNT KNOWLEDGE DATA
$knowledge_count = mysqli_num_rows(
mysqli_query($conn,"SELECT * FROM knowledge")
);


// COUNT LEARNED Q&A
$learned_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM learned_qa"));


// COUNT NEW QUESTIONS
$question_count = mysqli_num_rows(
mysqli_query($conn,
"SELECT * FROM unanswered_questions WHERE status='pending'")
);


?>


<!DOCTYPE html>

<html>

<head>

<title>ASTRA AI ADMIN DASHBOARD</title>


<style>


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

/* FUTURISTIC GRID BACKGROUND */

body{

    background:
    radial-gradient(circle at top,#004d5e,#07131f 40%,#03070d);

    color:white;
    min-height:100vh;
    overflow-x:hidden;
    position:relative;

}

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

    z-index:-2;

}

@keyframes gridMove{

    from{

        transform:translateY(0);

    }

    to{

        transform:translateY(50px);

    }

}

/* Header */

.header{

    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:25px 40px;

    background:rgba(255,255,255,.05);

    backdrop-filter:blur(18px);

    border-bottom:1px solid rgba(0,255,255,.25);

    box-shadow:0 0 20px rgba(0,255,255,.15);

}

.header h1{

    color:#00ffff;

    font-size:34px;

    letter-spacing:2px;

    text-shadow:0 0 15px cyan;

}

/* Logout */

.logout a{

    display:inline-block;

    padding:12px 24px;

    text-decoration:none;

    border-radius:10px;

    color:white;

    background:linear-gradient(45deg,#ff3333,#ff6666);

    font-weight:bold;

    transition:.3s;

}

.logout a:hover{

    transform:translateY(-3px);

    box-shadow:0 0 20px red;

}

/* Dashboard */

.container{

    width:95%;

    margin:40px auto;

    display:grid;

    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));

    gap:30px;

}

/* Cards */

.card{

    position:relative;

    background:rgba(255,255,255,.05);

    backdrop-filter:blur(18px);

    border:1px solid rgba(0,255,255,.15);

    border-radius:20px;

    padding:35px;

    height:260px;

    display:flex;

    flex-direction:column;

    justify-content:center;

    align-items:center;

    cursor:pointer;

    overflow:hidden;

    transition:.35s;

    box-shadow:0 0 25px rgba(0,255,255,.15);

}

.card::before{

    content:"";

    position:absolute;

    width:180px;
    height:180px;

    background:rgba(0,255,255,.08);

    border-radius:50%;

    top:-60px;
    right:-60px;

    transition:.4s;

}

.card:hover{

    transform:translateY(-10px);

    border-color:cyan;

    box-shadow:0 0 35px cyan;

}

.card:hover::before{

    transform:scale(1.4);

}

.card h2{

    color:#00ffff;

    margin-bottom:18px;

    text-align:center;

    font-size:25px;

}

/* Number */

.card p{

    font-size:60px;

    font-weight:bold;

    margin-bottom:10px;

}

/* Description */

.card span{

    color:#cfd8dc;

    text-align:center;

    font-size:15px;

    line-height:1.6;

}

/* Footer */

.footer{

    text-align:center;

    padding:25px;

    color:#7d8b99;

    font-size:14px;

}

/* Mobile */

@media(max-width:768px){

.header{

    flex-direction:column;

    gap:20px;

}

.header h1{

    font-size:28px;

    text-align:center;

}

.card{

    height:230px;

}

.card p{

    font-size:48px;

}

}



</style>


</head>



<body>



<div class="header">


<h1>
🤖 ASTRA | ADMIN DASHBOARD
</h1>


<div class="logout">

<a href="logout.php">
LOGOUT
</a>

</div>


</div>





<div class="container">





<!-- KNOWLEDGE DATABASE CARD -->


<div class="card" onclick="location.href='knowledge.php'">

<h2>Knowledge Database</h2>

<p><?=$knowledge_count?></p>

<span>Stored AI Questions & Answers</span>

</div>





<!-- STUDENT QUESTIONS CARD -->


<div class="card" onclick="location.href='questions.php'">

<h2>Student Questions</h2>

<p><?=$question_count?></p>

<span>Questions Waiting for an Admin Response</span>

</div>



<div class="card" onclick="location.href='learned_qa.php'">

<h2>Learned Q&A</h2>

<p><?=$learned_count?></p>

<span>Review, edit, approve, disable, or delete AI-learned answers</span>

</div>


<div class="card" onclick="location.href='ml_train.php'">

<h2>Machine Learning</h2>

<p>🧠</p>

<span>Generate training data, train the Naive Bayes model, and evaluate accuracy</span>

</div>


<div class="card" onclick="location.href='surveillance.php'">

<h2>Surveillance Camera</h2>

<p>📹</p>

<span>View the Raspberry Pi USB camera live feed</span>

</div>



<div class="card"
onclick="location.href='personnel_admin.php'">


<h2>
CGCI Personnel
</h2>


<p>
👥
</p>


<span>
Manage Departments and Faculty Staff
</span>


</div>




</div>

<div class="footer">
© 2026 ASTRA AI • Intelligent Knowledge Management System
</div>

</body>


</html>