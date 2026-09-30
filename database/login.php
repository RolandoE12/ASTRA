<?php

session_start();

include "database.php";


if(isset($_POST["login"])){

$username=$_POST["username"];

$password=$_POST["password"];


$sql="
SELECT * FROM admin
WHERE username='$username'
AND password='$password'
";


$result=mysqli_query($conn,$sql);



if(mysqli_num_rows($result)>0){


$_SESSION["admin"]=true;


header("location:dashboard.php");

exit;


}

else{


$error="Invalid Username or Password";


}


}


?>


<!DOCTYPE html>

<html>


<head>


<title>
ASTRA AI ADMIN LOGIN
</title>


<style>


*{

margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;

}



body{


height:100vh;

display:flex;

justify-content:center;

align-items:center;

color:white;


background:

radial-gradient(circle at top,#004d5e,#07131f 40%,#03070d);


overflow:hidden;


}


/* BACKGROUND GRID */


body::before{


content:"";

position:absolute;

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


}


@keyframes gridMove{


from{

transform:translateY(0);

}


to{

transform:translateY(50px);

}


}



/* LOGIN CARD */


.login-card{


position:relative;

z-index:1;


width:420px;


padding:45px;


background:

rgba(255,255,255,.05);


backdrop-filter:blur(20px);



border-radius:25px;



border:1px solid rgba(0,255,255,.25);



box-shadow:


0 0 40px rgba(0,255,255,.25);



text-align:center;



overflow:hidden;


}





.login-card::before{


content:"";


position:absolute;


width:200px;

height:200px;


background:

rgba(0,255,255,.15);


border-radius:50%;


top:-80px;

right:-80px;


filter:blur(10px);



}



/* LOGO */


.logo{


font-size:70px;


margin-bottom:15px;


animation:float 3s infinite ease-in-out;


}



@keyframes float{


50%{

transform:translateY(-10px);

}


}




h1{


color:#00ffff;


font-size:38px;


letter-spacing:3px;


text-shadow:


0 0 15px cyan;


margin-bottom:10px;


}




.subtitle{


color:#b0c9cc;


letter-spacing:2px;


margin-bottom:35px;


}




/* INPUT */


.input-box{


position:relative;


}


input{


width:100%;


padding:15px 18px;


margin:12px 0;


background:

rgba(0,0,0,.4);



border:1px solid rgba(0,255,255,.25);



border-radius:12px;


color:white;


font-size:16px;


outline:none;


transition:.3s;


}




input:focus{


border-color:#00ffff;


box-shadow:


0 0 15px cyan;


}




/* BUTTON */


button{


width:100%;


padding:15px;


margin-top:25px;


border:none;


border-radius:12px;



background:

linear-gradient(
45deg,
#00bfff,
#00ffff
);



font-size:18px;


font-weight:bold;


cursor:pointer;



color:black;


transition:.3s;


box-shadow:


0 0 20px cyan;


}




button:hover{


transform:translateY(-4px);


box-shadow:


0 0 35px cyan;



}




/* ERROR */


.error{


margin-top:20px;


padding:12px;


border-radius:10px;


background:

rgba(255,0,0,.15);



border:1px solid red;


color:#ff5555;


}




/* FOOTER */


.footer{


margin-top:30px;


color:#789;


font-size:13px;


letter-spacing:1px;


}




/* MOBILE */


@media(max-width:500px){


.login-card{


width:90%;


padding:30px;


}



h1{


font-size:30px;


}


}



</style>



</head>



<body>



<div class="login-card">


<div class="logo">

🤖

</div>



<h1>

ASTRA AI

</h1>



<p class="subtitle">

ADMIN CONTROL PANEL

</p>




<form method="POST">



<input

type="text"

name="username"

placeholder="👤 Enter Username"

required>




<input

type="password"

name="password"

placeholder="🔒 Enter Password"

required>



<button name="login">

🚀 LOGIN

</button>



</form>




<?php

if(isset($error)){

echo "<div class='error'>$error</div>";

}

?>





<div class="footer">


ASTRA AI Administration System

<br>

© 2026 Intelligent Knowledge Platform


</div>



</div>



</body>


</html>