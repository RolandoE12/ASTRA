<?php

include "database.php";

$username = "admin";
$password = "admin";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin (username, password) VALUES (?, ?)";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>