<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit('Admin access required.');
}

require_once 'database.php';
require_once 'personnel_org.php';

$uploadFolder = __DIR__ . '/../assets/personnel/';
$uploadWeb = '../assets/personnel/';
if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);

function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function uploadImage($field, $folder) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) return '';
    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) return '';
    $allowed = ['jpg','jpeg','png','gif','webp','avif'];
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) return '';
    $name = uniqid('personnel_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $folder . $name)) return $name;
    return '';
}
function removeImage($folder, $name) {
    if ($name && $name !== 'default.png') {
        $path = $folder . basename($name);
        if (is_file($path)) @unlink($path);
    }
}
function validParent(mysqli $conn, int $parentId, int $departmentId, int $selfId = 0): bool {
    if ($parentId <= 0) return true;
    if ($parentId === $selfId) return false;
    $stmt = mysqli_prepare($conn, 'SELECT department_id FROM personnel WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $parentId);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $row && (int)$row['department_id'] === $departmentId;
}
function createsCycle(mysqli $conn, int $selfId, int $parentId): bool {
    if ($parentId <= 0) return false;
    $seen = [];
    $current = $parentId;
    while ($current > 0 && !isset($seen[$current])) {
        if ($current === $selfId) return true;
        $seen[$current] = true;
        $stmt = mysqli_prepare($conn, 'SELECT parent_id FROM personnel WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'i', $current);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        $current = $row && $row['parent_id'] !== null ? (int)$row['parent_id'] : 0;
    }
    return false;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_personnel') {
        $department = (int)($_POST['department_id'] ?? 0);
        $name = trim($_POST['personnel_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $parent = (int)($_POST['parent_id'] ?? 0);
        if ($department <= 0 || $name === '' || $position === '') {
            $error = 'Please complete the department, full name, and position.';
        } elseif (!validParent($conn, $parent, $department)) {
            $error = 'The selected supervisor must belong to the same department.';
        } else {
            $image = uploadImage('personnel_image', $uploadFolder) ?: 'default.png';
            $stmt = mysqli_prepare($conn, 'INSERT INTO personnel (department_id,parent_id,name,position,image) VALUES (?,NULLIF(?,0),?,?,?)');
            $parentValue = $parent > 0 ? $parent : null;
            mysqli_stmt_bind_param($stmt, 'iisss', $department, $parentValue, $name, $position, $image);
            if (mysqli_stmt_execute($stmt)) $message = 'Employee/staff member added successfully.';
            else { $error = 'Unable to add the employee/staff member.'; removeImage($uploadFolder, $image); }
            mysqli_stmt_close($stmt);
        }
    }

    if ($action === 'update_personnel') {
        $id = (int)($_POST['personnel_id'] ?? 0);
        $department = (int)($_POST['department_id'] ?? 0);
        $name = trim($_POST['personnel_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $parent = (int)($_POST['parent_id'] ?? 0);
        if ($id <= 0 || $department <= 0 || $name === '' || $position === '') {
            $error = 'Please complete all required fields.';
        } elseif (!validParent($conn, $parent, $department, $id)) {
            $error = 'The selected supervisor must belong to the same department and cannot be this employee.';
        } elseif (createsCycle($conn, $id, $parent)) {
            $error = 'That supervisor would create an organizational cycle. Please choose another supervisor.';
        } else {
            $old = null;
            $stmt = mysqli_prepare($conn, 'SELECT image FROM personnel WHERE id=?');
            mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt); $old = mysqli_fetch_assoc($res); mysqli_stmt_close($stmt);
            $newImage = uploadImage('personnel_image', $uploadFolder);
            if ($newImage !== '') {
                $stmt = mysqli_prepare($conn, 'UPDATE personnel SET department_id=?,parent_id=NULLIF(?,0),name=?,position=?,image=? WHERE id=?');
                $parentValue = $parent > 0 ? $parent : null;
                mysqli_stmt_bind_param($stmt, 'iisssi', $department, $parentValue, $name, $position, $newImage, $id);
            } else {
                $stmt = mysqli_prepare($conn, 'UPDATE personnel SET department_id=?,parent_id=NULLIF(?,0),name=?,position=? WHERE id=?');
                $parentValue = $parent > 0 ? $parent : null;
                mysqli_stmt_bind_param($stmt, 'iissi', $department, $parentValue, $name, $position, $id);
            }
            if (mysqli_stmt_execute($stmt)) {
                $message = 'Employee/staff member updated successfully.';
                if ($newImage !== '' && $old) removeImage($uploadFolder, $old['image']);
            } else {
                $error = 'Unable to update the employee/staff member.';
                if ($newImage !== '') removeImage($uploadFolder, $newImage);
            }
            mysqli_stmt_close($stmt);
        }
    }

    if ($action === 'delete_personnel') {
        $id = (int)($_POST['personnel_id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'SELECT image FROM personnel WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt); $old = mysqli_fetch_assoc($res); mysqli_stmt_close($stmt);
        mysqli_query($conn, 'UPDATE personnel SET parent_id=NULL WHERE parent_id=' . $id);
        $stmt = mysqli_prepare($conn, 'DELETE FROM personnel WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            $message = 'Employee/staff member deleted successfully.';
            if ($old) removeImage($uploadFolder, $old['image']);
        } else $error = 'Unable to delete the employee/staff member.';
        mysqli_stmt_close($stmt);
    }

    if ($action === 'add_department') {
        $name = trim($_POST['department_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($name === '') $error = 'Department name is required.';
        else {
            $image = uploadImage('department_image', $uploadFolder) ?: 'default.png';
            $stmt = mysqli_prepare($conn, 'INSERT INTO departments (name,description,image) VALUES (?,?,?)');
            mysqli_stmt_bind_param($stmt, 'sss', $name, $description, $image);
            if (mysqli_stmt_execute($stmt)) $message = 'Department added successfully.';
            else { $error = 'Unable to add department.'; removeImage($uploadFolder, $image); }
            mysqli_stmt_close($stmt);
        }
    }

    if ($action === 'delete_department') {
        $id = (int)($_POST['department_id'] ?? 0);
        $stmt = mysqli_prepare($conn, 'DELETE FROM departments WHERE id=?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) $message = 'Department deleted successfully.';
        else $error = 'Unable to delete department.';
        mysqli_stmt_close($stmt);
    }
}

$departments = [];
$res = mysqli_query($conn, 'SELECT id,name,description,image FROM departments ORDER BY name');
while ($row = mysqli_fetch_assoc($res)) $departments[] = $row;

$personnel = [];
$res = mysqli_query($conn, 'SELECT p.id,p.department_id,p.parent_id,p.name,p.position,p.image,p.sort_order,d.name AS department FROM personnel p JOIN departments d ON d.id=p.department_id ORDER BY d.name,p.sort_order,p.name');
while ($row = mysqli_fetch_assoc($res)) $personnel[] = $row;

$editId = (int)($_GET['edit_personnel'] ?? 0);
$editPerson = null;
if ($editId > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT * FROM personnel WHERE id=?');
    mysqli_stmt_bind_param($stmt, 'i', $editId); mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt); $editPerson = mysqli_fetch_assoc($res); mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>ASTRA | CGCI Personnel</title>
<style>
*{box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{margin:0;min-height:100vh;color:#fff;background:radial-gradient(circle at top,#004d5e,#07131f 45%,#03070d);padding:26px}
.wrap{max-width:1400px;margin:auto}.header{display:flex;justify-content:space-between;align-items:center;gap:15px;flex-wrap:wrap;margin-bottom:22px}
h1{margin:0;color:#00ffff;text-shadow:0 0 15px cyan;letter-spacing:2px}.sub{color:#a9c9ce;margin-top:6px}.btn{border:0;border-radius:9px;padding:11px 16px;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:7px}.primary{background:linear-gradient(45deg,#00d2ff,#00ffff);color:#001116}.secondary{background:#18232c;color:#fff;border:1px solid #34515a}.danger{background:#e7354b;color:#fff}.small{padding:7px 11px;font-size:13px}
.panel{background:rgba(255,255,255,.055);border:1px solid rgba(0,255,255,.2);border-radius:16px;padding:20px;margin-bottom:20px;box-shadow:0 0 25px rgba(0,255,255,.08);backdrop-filter:blur(12px)}
.toolbar{display:flex;gap:12px;justify-content:space-between;align-items:center;flex-wrap:wrap}.search{width:min(520px,100%);padding:13px 15px;border-radius:10px;border:1px solid rgba(0,255,255,.25);background:rgba(0,0,0,.2);color:#fff;outline:none}.stats{display:flex;gap:10px;flex-wrap:wrap}.stat{padding:10px 15px;border-radius:10px;background:rgba(0,255,255,.08);border:1px solid rgba(0,255,255,.15)}.stat b{color:#00ffff;font-size:20px;margin-right:4px}
.notice{padding:13px 16px;border-radius:10px;margin-bottom:18px}.ok{background:rgba(0,255,150,.1);border:1px solid rgba(0,255,150,.35)}.err{background:rgba(255,50,70,.12);border:1px solid rgba(255,50,70,.4)}
.department{border:1px solid rgba(0,255,255,.22);border-radius:15px;background:rgba(0,20,40,.32);margin:15px 0;overflow:hidden}.department.hidden{display:none}.dept-head{padding:16px 18px;display:flex;align-items:center;justify-content:space-between;gap:15px;background:rgba(0,255,255,.055);border-bottom:1px solid rgba(0,255,255,.13)}.dept-title{display:flex;align-items:center;gap:13px}.dept-title img{width:52px;height:52px;border-radius:10px;object-fit:cover;background:#fff}.dept-title h2{margin:0;color:#00ffff;font-size:20px}.dept-title span{color:#9bb2b7;font-size:13px}.dept-actions{display:flex;gap:7px;flex-wrap:wrap}.employee-grid{padding:18px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.employee{display:flex;gap:12px;padding:13px;border:1px solid rgba(255,255,255,.1);border-radius:12px;background:rgba(255,255,255,.035);min-width:0}.employee img{width:70px;height:82px;border-radius:9px;object-fit:cover;background:#101b22;flex:none}.employee-main{min-width:0;flex:1}.employee-name{font-weight:800;font-size:15px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.employee-position{color:#00ffff;font-size:13px;margin:4px 0 10px;line-height:1.3}.employee-actions{display:flex;gap:6px;flex-wrap:wrap}.empty{padding:25px;text-align:center;color:#8fa8ae}.count{background:#07343e;color:#8ffcff;border:1px solid rgba(0,255,255,.25);padding:4px 9px;border-radius:20px;font-size:12px}
.dept-manage{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}.dept-mini{padding:12px;border:1px solid rgba(255,255,255,.1);border-radius:10px;background:rgba(255,255,255,.03);display:flex;justify-content:space-between;gap:10px;align-items:center}.dept-mini strong{font-size:13px}.modal{position:fixed;inset:0;background:rgba(0,0,0,.78);display:none;align-items:center;justify-content:center;padding:20px;z-index:100}.modal.show{display:flex}.modalbox{width:520px;max-width:100%;max-height:90vh;overflow:auto;background:#07131f;border:1px solid #00ffff;border-radius:16px;padding:24px;box-shadow:0 0 35px rgba(0,255,255,.35)}.modalbox h2{color:#00ffff;margin-top:0}label{display:block;color:#a7f9ff;font-weight:700;margin:13px 0 7px}input,select{width:100%;padding:12px;border-radius:9px;border:1px solid rgba(0,255,255,.25);background:#0d2029;color:#fff;outline:none}select option{background:#0d2029}.modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px}
@media(max-width:1100px){.employee-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dept-manage{grid-template-columns:repeat(2,1fr)}}
@media(max-width:700px){body{padding:14px}.employee-grid{grid-template-columns:1fr}.dept-manage{grid-template-columns:1fr}.dept-head{align-items:flex-start;flex-direction:column}.search{width:100%}.header .btn{width:100%;justify-content:center}}
</style>
</head>
<body>
<div class="wrap">
<div class="header">
  <div><h1>CGCI PERSONNEL</h1><div class="sub">Manage employees and staff organized by department or office.</div></div>
  <div style="display:flex;gap:8px;flex-wrap:wrap"><a class="btn secondary" href="dashboard.php">← Dashboard</a><button class="btn primary" onclick="openAdd()">＋ Add Employee / Staff</button></div>
</div>
<?php if($message): ?><div class="notice ok">✓ <?=e($message)?></div><?php endif; ?>
<?php if($error): ?><div class="notice err">⚠ <?=e($error)?></div><?php endif; ?>
<div class="panel">
  <div class="toolbar">
    <div class="stats"><div class="stat"><b><?=count($personnel)?></b> Employees / Staff</div><div class="stat"><b><?=count($departments)?></b> Departments / Offices</div></div>
    <input id="search" class="search" placeholder="Search employee, position, or department..." oninput="filterDepartments()">
  </div>
</div>

<div id="departmentsContainer">
<?php foreach($departments as $d):
  $members=array_values(array_filter($personnel,fn($p)=>(int)$p['department_id']===(int)$d['id']));
?>
<section class="department" data-department="<?=e(strtolower($d['name']))?>">
  <div class="dept-head">
    <div class="dept-title">
      <img src="<?=$uploadWeb.e($d['image'])?>" alt="">
      <div><h2><?=e($d['name'])?> <span class="count"><?=count($members)?> staff</span></h2><span><?=e($d['description'])?></span></div>
    </div>
    <div class="dept-actions"><button class="btn primary small" onclick="openAdd(<?= (int)$d['id']?>)">＋ Add to this department</button><button class="btn secondary small" onclick="toggleDepartment(this)">Collapse</button></div>
  </div>
  <div class="employee-grid">
  <?php if(!$members): ?><div class="empty" style="grid-column:1/-1">No employee or staff member assigned to this department yet.</div>
  <?php else: foreach($members as $p): ?>
    <div class="employee" data-search="<?=e(strtolower($p['name'].' '.$p['position'].' '.$d['name']))?>">
      <img src="<?=$uploadWeb.e($p['image'])?>" alt="">
      <div class="employee-main"><div class="employee-name" title="<?=e($p['name'])?>"><?=e($p['name'])?></div><div class="employee-position"><?=e($p['position'])?></div>
        <div class="employee-actions"><a class="btn primary small" href="?edit_personnel=<?=$p['id']?>">✏ Edit</a><form method="post" style="display:inline" onsubmit="return confirm('Delete <?=e($p['name'])?>? This cannot be undone.');"><input type="hidden" name="action" value="delete_personnel"><input type="hidden" name="personnel_id" value="<?=$p['id']?>"><button class="btn danger small">🗑 Delete</button></form></div>
      </div>
    </div>
  <?php endforeach; endif; ?>
  </div>
</section>
<?php endforeach; ?>
<?php if(!$departments): ?><div class="panel empty">No departments found. Add a department first.</div><?php endif; ?>
</div>

<div class="panel">
  <div class="toolbar"><h2 style="margin:0;color:#00ffff">Departments / Offices</h2><button class="btn secondary small" onclick="openModal('departmentModal')">＋ Add Department</button></div>
  <div class="dept-manage" style="margin-top:14px">
  <?php foreach($departments as $d): ?><div class="dept-mini"><strong><?=e($d['name'])?></strong><form method="post" onsubmit="return confirm('Delete <?=e($d['name'])?>? Personnel assigned to it may also be deleted because of the database relationship.');"><input type="hidden" name="action" value="delete_department"><input type="hidden" name="department_id" value="<?=$d['id']?>"><button class="btn danger small">Delete</button></form></div><?php endforeach; ?>
  </div>
</div>
</div>

<div class="modal" id="addModal"><div class="modalbox"><h2>＋ Add Employee / Staff</h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="add_personnel"><label>Department / Office *</label><select id="addDepartment" name="department_id" required><option value="">Select department</option><?php foreach($departments as $d): ?><option value="<?=$d['id']?>"><?=e($d['name'])?></option><?php endforeach; ?></select><label>Reports To</label><select id="addParent" name="parent_id"><option value="0">Top level / Department Head</option><?php foreach($personnel as $pp): ?><option value="<?=$pp['id']?>" data-dept="<?=$pp['department_id']?>"><?=e($pp['name'])?> — <?=e($pp['position'])?> (<?=e($pp['department'])?>)</option><?php endforeach; ?></select><small style="display:block;color:#89a6ad;margin-top:6px">Choose the person this employee reports to. Use Top level for a department head or peer.</small><label>Full Name *</label><input name="personnel_name" placeholder="e.g. Juan Dela Cruz" required><label>Position *</label><input name="position" placeholder="e.g. Faculty Member / Registrar" required><label>Photo</label><input type="file" name="personnel_image" accept="image/*"><div class="modal-actions"><button type="button" class="btn secondary" onclick="closeModal('addModal')">Cancel</button><button class="btn primary">Save Employee</button></div></form></div></div>

<div class="modal" id="departmentModal"><div class="modalbox"><h2>＋ Add Department / Office</h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="add_department"><label>Name *</label><input name="department_name" placeholder="e.g. Registrar" required><label>Description</label><input name="description" placeholder="Short description"><label>Image</label><input type="file" name="department_image" accept="image/*"><div class="modal-actions"><button type="button" class="btn secondary" onclick="closeModal('departmentModal')">Cancel</button><button class="btn primary">Save Department</button></div></form></div></div>

<?php if($editPerson): ?><div class="modal show" id="editModal"><div class="modalbox"><h2>✏ Edit Employee / Staff</h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="action" value="update_personnel"><input type="hidden" name="personnel_id" value="<?=$editPerson['id']?>"><label>Department / Office *</label><select name="department_id" required><?php foreach($departments as $d): ?><option value="<?=$d['id']?>" <?=$d['id']==$editPerson['department_id']?'selected':''?>><?=e($d['name'])?></option><?php endforeach; ?></select><label>Reports To</label><select id="editParent" name="parent_id"><option value="0">Top level / Department Head</option><?php foreach($personnel as $pp): if((int)$pp['id'] !== (int)$editPerson['id']): ?><option value="<?=$pp['id']?>" data-dept="<?=$pp['department_id']?>" <?=$editPerson['parent_id']!==null && (int)$editPerson['parent_id']===(int)$pp['id']?'selected':''?>><?=e($pp['name'])?> — <?=e($pp['position'])?> (<?=e($pp['department'])?>)</option><?php endif; endforeach; ?></select><small style="display:block;color:#89a6ad;margin-top:6px">The organizational chart will draw this employee below the selected supervisor.</small><label>Full Name *</label><input name="personnel_name" value="<?=e($editPerson['name'])?>" required><label>Position *</label><input name="position" value="<?=e($editPerson['position'])?>" required><label>Replace Photo (optional)</label><input type="file" name="personnel_image" accept="image/*"><div class="modal-actions"><a class="btn secondary" href="personnel_admin.php">Cancel</a><button class="btn primary">Save Changes</button></div></form></div></div><?php endif; ?>

<script>
function openModal(id){document.getElementById(id).classList.add('show')}
function closeModal(id){document.getElementById(id).classList.remove('show')}
function filterParentOptions(selectId, deptId, selfId){const sel=document.getElementById(selectId);if(!sel)return;Array.from(sel.options).forEach(o=>{if(o.value==='0'){o.hidden=false;return}const same=String(o.dataset.dept)===String(deptId);const self=selfId&&String(o.value)===String(selfId);o.hidden=!same||self;o.disabled=!same||self});if(sel.value!=='0'&&sel.selectedOptions[0]&&sel.selectedOptions[0].hidden)sel.value='0'}
function openAdd(dept){openModal('addModal');if(dept){document.getElementById('addDepartment').value=dept;filterParentOptions('addParent',dept,0)}else{filterParentOptions('addParent',document.getElementById('addDepartment').value,0)}}
document.getElementById('addDepartment')?.addEventListener('change',e=>filterParentOptions('addParent',e.target.value,0));
document.getElementById('editParent')?.closest('form')?.querySelector('select[name=department_id]')?.addEventListener('change',e=>filterParentOptions('editParent',e.target.value,document.querySelector('#editModal input[name=personnel_id]')?.value));
if(document.getElementById('editParent')){const ds=document.querySelector('#editModal select[name=department_id]');if(ds)filterParentOptions('editParent',ds.value,document.querySelector('#editModal input[name=personnel_id]')?.value)}
function toggleDepartment(btn){const sec=btn.closest('.department');const grid=sec.querySelector('.employee-grid');if(grid.style.display==='none'){grid.style.display='grid';btn.textContent='Collapse'}else{grid.style.display='none';btn.textContent='Expand'}}
function filterDepartments(){const q=document.getElementById('search').value.toLowerCase().trim();document.querySelectorAll('.department').forEach(sec=>{let matches=sec.dataset.department.includes(q);sec.querySelectorAll('.employee').forEach(card=>{const hit=!q||card.dataset.search.includes(q);card.style.display=hit?'flex':'none';if(hit)matches=true});sec.style.display=matches?'block':'none'})}
window.addEventListener('click',e=>{document.querySelectorAll('.modal').forEach(m=>{if(e.target===m)m.classList.remove('show')})});
</script>
</body>
</html>
