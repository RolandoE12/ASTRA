<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit('Admin access required.');
}
$config = require __DIR__ . '/camera_config.php';
$name = $config['name'] ?? 'ASTRA Camera';
$stream = $config['stream_url'] ?? '';
$snapshot = $config['snapshot_url'] ?? '';
$location = $config['location'] ?? 'Raspberry Pi 4';
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ASTRA Surveillance Camera</title>
<style>
*{box-sizing:border-box;font-family:'Segoe UI',sans-serif}body{margin:0;min-height:100vh;color:#fff;background:radial-gradient(circle at top,#004d5e,#07131f 42%,#03070d)}.wrap{width:94%;max-width:1250px;margin:auto;padding:28px 0 45px}.top{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:22px}.top h1{margin:0;color:#00ffff;text-shadow:0 0 15px rgba(0,255,255,.7)}.muted{color:#aebdca}.btn{display:inline-block;text-decoration:none;border:1px solid rgba(0,255,255,.25);background:#102333;color:#d9ffff;padding:12px 18px;border-radius:12px;font-weight:800}.panel{background:rgba(255,255,255,.055);border:1px solid rgba(0,255,255,.16);border-radius:22px;padding:20px;box-shadow:0 0 30px rgba(0,255,255,.10);backdrop-filter:blur(14px)}.camera{position:relative;background:#000;border-radius:17px;overflow:hidden;min-height:420px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(0,255,255,.18)}#feed{display:block;width:100%;height:auto;max-height:72vh;object-fit:contain}.offline{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;text-align:center;padding:25px;background:rgba(0,0,0,.82);font-size:20px}.hidden{display:none}.meta{display:flex;justify-content:space-between;gap:15px;flex-wrap:wrap;margin:16px 0}.badge{padding:8px 12px;border-radius:999px;background:rgba(255,50,50,.14);border:1px solid rgba(255,80,80,.35);color:#ffb0b0;font-weight:800}.badge.online{background:rgba(0,255,150,.12);border-color:rgba(0,255,150,.35);color:#aaffd8}.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:15px}.action{cursor:pointer;border:0;border-radius:11px;padding:12px 17px;font-weight:800;background:linear-gradient(45deg,#00a8b5,#00ffff);color:#00151b}.action.secondary{background:#102333;color:#d9ffff;border:1px solid rgba(0,255,255,.25)}.note{margin-top:18px;line-height:1.6}.warning{border-left:4px solid #00ffff;padding:12px 15px;background:rgba(0,255,255,.05);border-radius:8px}@media(max-width:700px){.top{flex-direction:column;align-items:flex-start}.camera{min-height:260px}}
</style>
</head>
<body>
<div class="wrap">
  <div class="top">
    <div><h1>📹 ASTRA SURVEILLANCE</h1><div class="muted">Administrator-only Raspberry Pi camera</div></div>
    <a class="btn" href="dashboard.php">← Dashboard</a>
  </div>
  <div class="panel">
    <div class="meta">
      <div><strong><?=htmlspecialchars($name)?></strong><div class="muted"><?=htmlspecialchars($location)?></div></div>
      <div id="status" class="badge">● CONNECTING</div>
    </div>
    <div class="camera">
      <img id="feed" src="<?=htmlspecialchars($stream, ENT_QUOTES)?>" alt="Raspberry Pi camera live feed">
      <div id="offline" class="offline hidden"><div><strong>Camera feed unavailable</strong><br><span class="muted">Check the Raspberry Pi, USB webcam, network, and stream URL.</span></div></div>
    </div>
    <div class="actions">
      <button class="action" onclick="reloadFeed()">↻ Refresh Feed</button>
      <?php if($snapshot): ?><button class="action secondary" onclick="openSnapshot()">📸 Open Snapshot</button><?php endif; ?>
      <button class="action secondary" onclick="toggleFullscreen()">⛶ Full Screen</button>
    </div>
    <div class="note warning">The camera stream is displayed only after this admin page passes the ASTRA admin-session check. Keep the Raspberry Pi stream on your trusted local network and do not expose it directly to the public internet.</div>
  </div>
</div>
<script>
const feed=document.getElementById('feed'), status=document.getElementById('status'), offline=document.getElementById('offline');
function setOnline(){status.textContent='● LIVE';status.classList.add('online');offline.classList.add('hidden');}
function setOffline(){status.textContent='● OFFLINE';status.classList.remove('online');offline.classList.remove('hidden');}
feed.addEventListener('load',setOnline);feed.addEventListener('error',setOffline);
function reloadFeed(){const base=<?=json_encode($stream)?>;feed.src=base+(base.includes('?')?'&':'?')+'_='+Date.now();}
function openSnapshot(){window.open(<?=json_encode($snapshot)?>,'_blank','noopener');}
function toggleFullscreen(){const box=document.querySelector('.camera');if(document.fullscreenElement)document.exitFullscreen();else box.requestFullscreen?.();}
setTimeout(()=>{if(!feed.complete){}},5000);
</script>
</body>
</html>
