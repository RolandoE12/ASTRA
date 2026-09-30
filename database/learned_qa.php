<?php
session_start();
if (!isset($_SESSION['admin'])) { header('location:login.php'); exit; }
include 'database.php';

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'status' && $id > 0) {
        $status = $_POST['status'] ?? 'active';
        if (in_array($status, ['active','pending','disabled'], true)) {
            $stmt = mysqli_prepare($conn, "UPDATE learned_qa SET status=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'si', $status, $id);
            mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        }
    } elseif ($action === 'delete' && $id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM learned_qa WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
    } elseif ($action === 'edit' && $id > 0) {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $intent = trim($_POST['intent'] ?? 'unknown');
        if ($question !== '' && $answer !== '') {
            $stmt = mysqli_prepare($conn, "UPDATE learned_qa SET question=?, answer=?, intent=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'sssi', $question, $answer, $intent, $id);
            mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        }
    }
    header('Location: learned_qa.php'); exit;
}

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? 'all';
$where = [];
$params = []; $types = '';
if ($search !== '') { $where[]='(question LIKE ? OR answer LIKE ? OR intent LIKE ?)'; $like="%$search%"; $params=[$like,$like,$like]; $types='sss'; }
if (in_array($status,['active','pending','disabled'],true)) { $where[]='status=?'; $params[]=$status; $types.='s'; }
$sql='SELECT * FROM learned_qa'.($where?' WHERE '.implode(' AND ',$where):'').' ORDER BY updated_at DESC, id DESC LIMIT 500';
$stmt=mysqli_prepare($conn,$sql);
if($params) mysqli_stmt_bind_param($stmt,$types,...$params);
mysqli_stmt_execute($stmt); $result=mysqli_stmt_get_result($stmt);

$counts=['active'=>0,'pending'=>0,'disabled'=>0];
$cres=mysqli_query($conn,"SELECT status, COUNT(*) c FROM learned_qa GROUP BY status");
while($r=mysqli_fetch_assoc($cres)) $counts[$r['status']]=(int)$r['c'];
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ASTRA Learned Q&A</title>
<style>
*{box-sizing:border-box;font-family:'Segoe UI',sans-serif}body{margin:0;background:radial-gradient(circle at top,#004d5e,#07131f 42%,#03070d);color:#fff;min-height:100vh}body:before{content:"";position:fixed;inset:0;background:linear-gradient(90deg,transparent 95%,rgba(0,255,255,.12) 96%),linear-gradient(transparent 95%,rgba(0,255,255,.12) 96%);background-size:50px 50px;opacity:.35;pointer-events:none}.header{padding:22px 35px;border-bottom:1px solid rgba(0,255,255,.25);background:rgba(255,255,255,.05);backdrop-filter:blur(18px);display:flex;justify-content:space-between;align-items:center;gap:15px}.header h1{margin:0;color:#00ffff;text-shadow:0 0 15px cyan;font-size:28px}.back{color:#fff;text-decoration:none;background:rgba(0,255,255,.12);border:1px solid rgba(0,255,255,.4);padding:10px 16px;border-radius:9px}.wrap{width:94%;margin:28px auto}.stats{display:flex;gap:15px;flex-wrap:wrap;margin-bottom:20px}.stat{padding:15px 22px;background:rgba(255,255,255,.06);border:1px solid rgba(0,255,255,.2);border-radius:12px}.stat b{color:#00ffff;font-size:22px}.toolbar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}.toolbar input,.toolbar select{background:#081923;color:#fff;border:1px solid #176b76;border-radius:8px;padding:11px}.toolbar input{min-width:280px}.toolbar button{padding:11px 18px;border:0;border-radius:8px;background:#00bfc7;color:#001114;font-weight:bold}.card{background:rgba(255,255,255,.06);border:1px solid rgba(0,255,255,.18);border-radius:15px;padding:20px;margin-bottom:16px;box-shadow:0 0 18px rgba(0,255,255,.07)}.top{display:flex;justify-content:space-between;gap:12px;align-items:center}.id{color:#7feff5}.badge{padding:5px 9px;border-radius:20px;font-size:12px;background:#17454b}.q{font-size:19px;color:#00ffff;margin:15px 0 8px;font-weight:600}.a{color:#dce7ea;line-height:1.55;white-space:pre-wrap}.meta{color:#91a7ad;font-size:12px;margin-top:12px}.actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}.actions button{border:1px solid #28727a;background:#09232a;color:#fff;padding:8px 12px;border-radius:7px;cursor:pointer}.actions .active{border-color:#00ffff}.actions .danger{border-color:#ff5964;color:#ff9ba2}.edit{display:none;margin-top:15px}.edit textarea,.edit input{width:100%;background:#06151c;color:#fff;border:1px solid #26727b;border-radius:7px;padding:10px;margin:5px 0 10px}.edit textarea{min-height:110px}.empty{text-align:center;padding:50px;color:#9fb0b5}
</style></head><body>
<div class="header"><h1>🧠 ASTRA | LEARNED Q&A</h1><a class="back" href="dashboard.php">← Dashboard</a></div>
<div class="wrap"><div class="stats"><div class="stat">Active <b><?=$counts['active']?></b></div><div class="stat">Pending <b><?=$counts['pending']?></b></div><div class="stat">Disabled <b><?=$counts['disabled']?></b></div></div>
<form class="toolbar" method="get"><input name="search" value="<?=e($search)?>" placeholder="Search learned questions, answers, intent..."><select name="status"><option value="all">All statuses</option><?php foreach(['active','pending','disabled'] as $s): ?><option value="<?=$s?>" <?=$status===$s?'selected':''?>><?=$s?></option><?php endforeach; ?></select><button>Search</button></form>
<?php if(mysqli_num_rows($result)===0): ?><div class="card empty">No learned Q&A records found.</div><?php endif; ?>
<?php while($row=mysqli_fetch_assoc($result)): ?><div class="card"><div class="top"><span class="id">#<?=$row['id']?> · Intent: <?=e($row['intent'])?></span><span class="badge"><?=e($row['status'])?></span></div><div class="q"><?=e($row['question'])?></div><div class="a"><?=e($row['answer'])?></div><div class="meta">Confidence: <?=number_format((float)$row['confidence']*100,1)?>% · Used: <?=$row['use_count']?> times · Updated: <?=e($row['updated_at'])?></div>
<div class="actions"><form method="post"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$row['id']?>"><input type="hidden" name="status" value="active"><button class="active">✓ Approve / Activate</button></form><form method="post"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$row['id']?>"><input type="hidden" name="status" value="pending"><button>⏳ Pending</button></form><form method="post"><input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?=$row['id']?>"><input type="hidden" name="status" value="disabled"><button>Disable</button></form><button type="button" onclick="toggleEdit(<?=$row['id']?>)">✎ Edit</button><form method="post" onsubmit="return confirm('Delete this learned Q&A permanently?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=$row['id']?>"><button class="danger">Delete</button></form></div>
<div class="edit" id="edit<?=$row['id']?>"><form method="post"><input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?=$row['id']?>"><label>Question</label><input name="question" value="<?=e($row['question'])?>"><label>Intent</label><input name="intent" value="<?=e($row['intent'])?>"><label>Answer</label><textarea name="answer"><?=e($row['answer'])?></textarea><button type="submit">Save Changes</button></form></div></div><?php endwhile; mysqli_stmt_close($stmt); ?></div>
<script>function toggleEdit(id){const x=document.getElementById('edit'+id);x.style.display=x.style.display==='block'?'none':'block'}</script></body></html>
