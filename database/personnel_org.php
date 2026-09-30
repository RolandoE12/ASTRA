<?php
/*
 * Keeps the personnel table compatible with the organizational-chart UI.
 * Safe to include from both the public UI and the admin page.
 */
if (!isset($conn) || !($conn instanceof mysqli)) {
    return;
}

function personnel_column_exists(mysqli $conn, string $column): bool {
    $column = mysqli_real_escape_string($conn, $column);
    $res = mysqli_query($conn, "SHOW COLUMNS FROM personnel LIKE '{$column}'");
    return $res && mysqli_num_rows($res) > 0;
}

if (!personnel_column_exists($conn, 'parent_id')) {
    @mysqli_query($conn, "ALTER TABLE personnel ADD COLUMN parent_id INT NULL DEFAULT NULL AFTER department_id");
}

if (!personnel_column_exists($conn, 'sort_order')) {
    @mysqli_query($conn, "ALTER TABLE personnel ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER image");
}

// A simple index improves department/parent tree queries.
$idxCheck = mysqli_query($conn, "SHOW INDEX FROM personnel WHERE Key_name='idx_personnel_parent'");
if ($idxCheck && mysqli_num_rows($idxCheck) === 0) {
    @mysqli_query($conn, "CREATE INDEX idx_personnel_parent ON personnel(parent_id)");
}

// Add the self-reference only when it is not already present. The UI also
// validates parent/department relationships, so older databases remain usable.
$fkCheck = mysqli_query($conn, "
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'personnel'
      AND COLUMN_NAME = 'parent_id'
      AND REFERENCED_TABLE_NAME = 'personnel'
    LIMIT 1
");
if ($fkCheck && mysqli_num_rows($fkCheck) === 0) {
    @mysqli_query($conn, "ALTER TABLE personnel ADD CONSTRAINT fk_personnel_parent FOREIGN KEY (parent_id) REFERENCES personnel(id) ON DELETE SET NULL");
}
?>
