<?php
include "database.php";
include "personnel_org.php";

header("Content-Type:text/html; charset=UTF-8");

$department = isset($_GET["department"]) ? (int)$_GET["department"] : 0;

$stmt = mysqli_prepare($conn, "SELECT id,department_id,parent_id,name,position,image FROM personnel WHERE department_id=? ORDER BY sort_order ASC,name ASC");
mysqli_stmt_bind_param($stmt, "i", $department);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result)>0){
    while($row=mysqli_fetch_assoc($result)){
?>
<div class="person-card" data-parent-id="<?php echo (int)$row["parent_id"]; ?>">
    <img src="assets/personnel/<?php echo htmlspecialchars($row["image"], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'); ?>">
    <h3><?php echo htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8'); ?></h3>
    <p><?php echo htmlspecialchars($row["position"], ENT_QUOTES, 'UTF-8'); ?></p>
</div>
<?php
    }
}else{
?>
<div class="no-personnel">No personnel found.</div>
<?php
}
mysqli_stmt_close($stmt);
?>
