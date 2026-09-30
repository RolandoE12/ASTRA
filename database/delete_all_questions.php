<?php

session_start();

include "database.php";

mysqli_query($conn,
"DELETE FROM unanswered_questions");

header("Location:new_questions.php");
exit;