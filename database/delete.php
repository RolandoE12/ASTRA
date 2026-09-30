<?php

include "database.php";


$id=$_GET["id"];


mysqli_query(
$conn,
"DELETE FROM knowledge WHERE id=$id"
);


header(
"location:knowledge.php"
);


?>