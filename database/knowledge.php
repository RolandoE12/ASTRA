<?php

session_start();

if(!isset($_SESSION["admin"])){

header("location:login.php");
exit;

}

include "database.php";


$knowledge=mysqli_query(
$conn,
"SELECT * FROM knowledge ORDER BY id DESC"
);

?>


<!DOCTYPE html>

<html>

<head>

<title>ASTRA KNOWLEDGE DATABASE</title>


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

/* ANIMATED GRID BACKGROUND */

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

.container{

    width:96%;
    margin:auto;

}

.top-bar{

    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    margin-bottom:20px;

}

.top-bar a{

    text-decoration:none;

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

.add{

    background:linear-gradient(45deg,#00d2ff,#00ffff);
    color:black;
    box-shadow:0 0 15px cyan;

}

.add:hover{

    transform:translateY(-3px);
    box-shadow:0 0 25px cyan;

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

    /* Vertical divider */
    border-right:2px solid rgba(0,0,0,.25);

}

th:last-child{

    border-right:none;

}

td{

    padding:14px;
    border-bottom:1px solid rgba(255,255,255,.08);

    /* Vertical divider */
    border-right:1px solid rgba(0,255,255,.25);

}

td:last-child{

    border-right:none;

}

tr:hover{

    background:rgba(0,255,255,.08);

}

.action{

    display:flex;
    gap:10px;

}

.edit-btn{

    background:#00bfff;
    color:white;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    transition:.3s;

}

.edit-btn:hover{

    background:#0095cc;
    transform:scale(1.08);

}

.delete-btn{

    background:#ff3b3b;
    color:white;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    transition:.3s;

}

.delete-btn:hover{

    background:#d90000;
    transform:scale(1.08);

}

.total{

    margin-top:20px;
    display:inline-block;
    padding:12px 20px;
    background:#00ffff;
    color:#000;
    border-radius:8px;
    font-weight:bold;
    box-shadow:0 0 15px cyan;

}

@media(max-width:900px){

table{

    display:block;
    overflow-x:auto;

}

.top-bar{

    gap:10px;

}

}

.search-box{

    flex:1;
    display:flex;
    justify-content:center;
    margin:0 20px;

}

.search-box input{

    width:100%;
    max-width:500px;
    padding:12px 18px;
    border:none;
    outline:none;
    border-radius:30px;
    font-size:15px;
    background:rgba(255,255,255,.08);
    color:white;
    border:2px solid rgba(0,255,255,.4);
    transition:.3s;
    box-shadow:0 0 10px rgba(0,255,255,.2);

}

.search-box input::placeholder{

    color:#bbb;

}

.search-box input:focus{

    border-color:#00ffff;
    box-shadow:0 0 18px cyan;

}


</style>


</head>


<body>


<div class="container">

<h1>ASTRA KNOWLEDGE DATABASE</h1>

<div class="top-bar">

<a href="dashboard.php" class="btn back">
← Dashboard
</a>

<div class="search-box">

<input
type="text"
id="searchInput"
placeholder="🔍 Search Question, Keywords, or Answer..."
onkeyup="searchKnowledge()">

</div>

<a href="add.php" class="btn add">
＋ Add Information
</a>

</div>

<div class="table-box">

<table id="knowledgeTable">

<tr>

<th>ID</th>

<th>Question</th>

<th>Keywords</th>

<th>Answer</th>

<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($knowledge)){ ?>

<tr>

<td><?=$row["id"]?></td>

<td><?=$row["question"]?></td>

<td><?=$row["keywords"]?></td>

<td><?=$row["answer"]?></td>

<td>

<div class="action">

<a class="edit-btn"
href="edit.php?id=<?=$row['id']?>">

 Edit

</a>

<a class="delete-btn"
onclick="return confirm('Delete this information?');"
href="delete.php?id=<?=$row['id']?>">

 Delete

</a>

</div>

</td>

</tr>

<?php } ?>

</table>

</div>

<div class="total">

<?php

$result=mysqli_query($conn,"SELECT * FROM knowledge");

echo "Total Knowledge: ".mysqli_num_rows($result);

?>

</div>

</div>


<script>

function searchKnowledge(){

    let input = document.getElementById("searchInput").value.toLowerCase();

    let table = document.getElementById("knowledgeTable");

    let rows = table.getElementsByTagName("tr");

    for(let i = 1; i < rows.length; i++){

        let question = rows[i].cells[1].textContent.toLowerCase();
        let keywords = rows[i].cells[2].textContent.toLowerCase();
        let answer = rows[i].cells[3].textContent.toLowerCase();

        if(question.includes(input) ||
           keywords.includes(input) ||
           answer.includes(input))
        {

            rows[i].style.display = "";

        }

        else{

            rows[i].style.display = "none";

        }

    }

}

</script>

</body>

</html>