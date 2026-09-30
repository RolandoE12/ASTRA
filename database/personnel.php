<?php
require_once 'database.php';
require_once 'personnel_org.php';

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function positionRank(string $position): int {
    $p = strtolower(trim($position));

    // Highest positions first. More specific titles are checked before
    // general keywords so that "Assistant Dean" does not become a Dean.
    $rules = [
        1000 => ['dean'],
        950  => ['president'],
        940  => ['vice president', 'vice-president'],
        930  => ['executive director'],
        920  => ['director'],
        910  => ['department head', 'office head', 'head of'],
        900  => ['chairperson', 'chairman', 'chairwoman', 'department chair', 'program chair'],
        880  => ['associate dean'],
        870  => ['assistant dean'],
        850  => ['program head', 'program coordinator', 'coordinator'],
        820  => ['supervisor'],
        800  => ['principal'],
        780  => ['professor'],
        760  => ['associate professor'],
        740  => ['assistant professor'],
        720  => ['instructor', 'faculty', 'lecturer'],
        600  => ['registrar'],
        580  => ['guidance counselor', 'counselor'],
        560  => ['librarian'],
        540  => ['school nurse', 'nurse'],
        500  => ['secretary'],
        450  => ['clerk'],
        400  => ['technician', 'technologist'],
        300  => ['staff', 'assistant', 'aide'],
    ];

    // Avoid classifying assistant/associate dean as plain dean.
    if (str_contains($p, 'associate dean')) return 880;
    if (str_contains($p, 'assistant dean')) return 870;

    foreach ($rules as $rank => $keywords) {
        foreach ($keywords as $keyword) {
            if (str_contains($p, $keyword)) return $rank;
        }
    }

    return 100;
}

function buildLevels(array $members): array {
    foreach ($members as &$member) {
        $member['_rank'] = positionRank($member['position']);
    }
    unset($member);

    usort($members, function ($a, $b) {
        if ($a['_rank'] !== $b['_rank']) return $b['_rank'] <=> $a['_rank'];
        $sortA = (int)($a['sort_order'] ?? 0);
        $sortB = (int)($b['sort_order'] ?? 0);
        if ($sortA !== $sortB) return $sortA <=> $sortB;
        return strcasecmp($a['name'], $b['name']);
    });

    // If a Dean exists, keep the highest level as the first row.
    // Otherwise the highest-ranked personnel become the first row.
    $levels = [];
    foreach ($members as $member) {
        $rank = $member['_rank'];
        if (!$levels || $levels[count($levels) - 1][0]['_rank'] !== $rank) {
            $levels[] = [$member];
        } else {
            $levels[count($levels) - 1][] = $member;
        }
    }

    return $levels;
}

$departments = [];
$res = mysqli_query($conn, 'SELECT id,name,description,image FROM departments ORDER BY name');
while ($row = mysqli_fetch_assoc($res)) {
    $departments[] = $row;
}

$selectedDepartment = isset($_GET['department']) ? (int)$_GET['department'] : 0;
$selected = null;

if ($selectedDepartment > 0) {
    $stmt = mysqli_prepare($conn, 'SELECT id,name,description,image FROM departments WHERE id=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $selectedDepartment);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $selected = mysqli_fetch_assoc($result) ?: null;
    mysqli_stmt_close($stmt);
}

$members = [];
if ($selected) {
    $stmt = mysqli_prepare($conn, 'SELECT id,name,position,image,sort_order FROM personnel WHERE department_id=? ORDER BY sort_order ASC, name ASC');
    mysqli_stmt_bind_param($stmt, 'i', $selectedDepartment);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) $members[] = $row;
    mysqli_stmt_close($stmt);
}

$levels = buildLevels($members);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CGCI Personnel</title>
<style>
*{box-sizing:border-box}
:root{--green:#075b24;--darkgreen:#003d18;--lightgreen:#118b3a;--text:#101010;--line:#075b24}
body{margin:0;color:var(--text);font-family:Arial,Helvetica,sans-serif;background:#fff;min-height:100vh}
body:before{content:"";position:fixed;inset:0;pointer-events:none;opacity:.12;background-image:repeating-linear-gradient(45deg,#8f8f8f 0,#8f8f8f 1px,transparent 1px,transparent 14px),repeating-linear-gradient(-45deg,#8f8f8f 0,#8f8f8f 1px,transparent 1px,transparent 14px);background-size:28px 28px}
.page{position:relative;z-index:1;max-width:1500px;margin:auto;padding:34px 28px 60px}
.header{text-align:center;margin-bottom:28px}
.logo{width:100px;height:100px;object-fit:contain;margin:auto;display:block}
.school{font-family:Georgia,'Times New Roman',serif;font-size:clamp(24px,3vw,44px);font-weight:800;margin:8px 0 0;text-transform:uppercase}
.subtitle{font-family:Georgia,'Times New Roman',serif;font-size:18px;margin:2px 0 22px}
h1{color:var(--green);font-size:clamp(30px,4vw,58px);line-height:1;margin:0 0 12px;font-weight:900;letter-spacing:1px;text-transform:uppercase}
.header-note{font-size:18px;font-weight:700}
.department-list{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:22px;max-width:1150px;margin:35px auto}
.department-card{display:block;text-decoration:none;color:#fff;background:linear-gradient(145deg,var(--darkgreen),var(--green));border-radius:20px;padding:24px;text-align:center;box-shadow:0 12px 30px rgba(0,60,20,.18);transition:.2s}
.department-card:hover{transform:translateY(-5px);box-shadow:0 18px 35px rgba(0,60,20,.28)}
.department-card img{width:130px;height:100px;object-fit:contain;background:#fff;border-radius:14px;margin-bottom:12px}
.department-card h2{margin:0 0 7px;font-size:21px}.department-card p{margin:0;opacity:.9;font-size:14px}
.chart-wrap{background:rgba(255,255,255,.88);border:1px solid #d4d4d4;border-radius:24px;padding:28px 12px 42px;box-shadow:0 10px 35px rgba(0,0,0,.08);overflow-x:hidden}
.chart-header{text-align:center;margin-bottom:18px}.chart-header h2{margin:0;color:var(--green);font-size:32px;text-transform:uppercase}.chart-header p{margin:7px auto 0;max-width:850px;color:#444;font-size:15px}
.back{display:inline-flex;align-items:center;gap:7px;text-decoration:none;background:var(--green);color:#fff;padding:11px 18px;border-radius:10px;font-weight:700;margin-bottom:22px}
.orgchart{width:100%;max-width:1280px;margin:0 auto;padding:12px 5px 35px}
.level{position:relative;display:grid;grid-template-columns:repeat(5,minmax(0,1fr));justify-content:center;gap:18px;margin:0 auto;max-width:1160px}.level.top-level{grid-template-columns:1fr;max-width:300px}.level.top-level .person{width:250px;margin:auto}
.level:not(:first-child){padding-top:70px}.level.same-rank-row{margin-top:42px;padding-top:0}.level.same-rank-row:before{display:none}.level.same-rank-row .person:before{display:none}
.level:not(:first-child):before{content:"";position:absolute;top:0;left:0;right:0;height:35px;border-top:3px solid var(--line);width:72%;margin:auto}
.level:not(:first-child) .person:before{content:"";position:absolute;top:-70px;left:50%;height:70px;border-left:3px solid var(--line)}
.person{position:relative;width:220px;text-align:center}
.person-photo{width:150px;height:150px;border-radius:50%;object-fit:cover;border:7px solid var(--green);background:#075b24;display:block;margin:0 auto 0;box-shadow:0 5px 15px rgba(0,0,0,.18)}
.person-label{margin:-1px auto 0;background:var(--green);color:#fff;padding:10px 12px 12px;border-radius:0 0 4px 4px;min-height:62px;display:flex;flex-direction:column;justify-content:center;box-shadow:0 3px 8px rgba(0,0,0,.15)}
.person-name{font-weight:900;font-size:15px;text-transform:uppercase;line-height:1.15}.person-position{font-size:13px;line-height:1.15;margin-top:4px}
.level:first-child .person-photo{width:190px;height:190px;border-width:8px}.level:first-child .person{width:250px}.level:first-child .person-label{font-size:1.03em}
.level-rank{font-size:11px;font-weight:700;color:#52735c;text-align:center;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px}
.empty{text-align:center;padding:55px 20px;color:#555}.empty strong{display:block;color:var(--green);font-size:22px;margin-bottom:7px}
.footer{text-align:center;color:#555;margin-top:30px;font-size:13px}
@media(max-width:1100px){.level{grid-template-columns:repeat(5,minmax(0,1fr));gap:10px}.person{width:100%}.orgchart{width:100%}}
@media(max-width:700px){.page{padding:20px 12px 40px}.school{font-size:22px}.subtitle{font-size:14px}h1{font-size:31px}.header-note{font-size:15px}.chart-wrap{padding:18px 4px 30px;border-radius:15px}.chart-header h2{font-size:25px}.department-list{grid-template-columns:1fr}.orgchart{width:100%;padding-left:0;padding-right:0}.level{grid-template-columns:repeat(5,minmax(0,1fr));gap:4px}.person{width:100%;min-width:0}.person-photo{width:72px;height:72px;border-width:4px}.person-label{padding:7px 3px}.person-name{font-size:8px}.person-position{font-size:7px}.level:first-child .person{width:220px}.level:first-child .person-photo{width:140px;height:140px}}
</style>
</head>
<body>
<div class="page">
<header class="header">
    <img class="logo" src="../assets/core.png" alt="Core Gateway College Inc. logo" onerror="this.style.display='none'">
    <div class="school">Core Gateway College, Inc.</div>
    <div class="subtitle">formerly the Colleges of the Republic</div>
    <h1>CGCI Personnel</h1>
    <div class="header-note">Personnel Organizational Chart</div>
</header>

<?php if (!$selected): ?>
    <div class="department-list">
    <?php foreach ($departments as $d): ?>
        <a class="department-card" href="personnel.php?department=<?= (int)$d['id'] ?>">
            <img src="../assets/personnel/<?= e($d['image']) ?>" alt="<?= e($d['name']) ?>">
            <h2><?= e($d['name']) ?></h2>
            <?php if ($d['description'] !== ''): ?><p><?= e($d['description']) ?></p><?php endif; ?>
        </a>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <a class="back" href="personnel.php">← Back to Departments</a>
    <section class="chart-wrap">
        <div class="chart-header">
            <h2><?= e($selected['name']) ?></h2>
            <?php if ($selected['description'] !== ''): ?><p><?= e($selected['description']) ?></p><?php endif; ?>
        </div>

        <?php if (!$members): ?>
            <div class="empty"><strong>No personnel listed yet.</strong>This department does not have employee or staff records available.</div>
        <?php else: ?>
            <div class="orgchart">
            <?php foreach ($levels as $levelIndex => $level): ?>
                <?php
                    // Keep the highest position(s) at the top.
                    // Every lower level is split into rows of no more than 5 people.
                    $levelRows = ($levelIndex === 0) ? [$level] : array_chunk($level, 5);
                ?>
                <?php foreach ($levelRows as $rowIndex => $levelRow): ?>
                    <div class="level<?= $levelIndex === 0 ? ' top-level' : ($rowIndex > 0 ? ' same-rank-row' : '') ?>">
                        <?php foreach ($levelRow as $person): ?>
                            <article class="person" title="<?= e($person['name'].' - '.$person['position']) ?>">
                                <div class="level-rank">Level <?= $levelIndex + 1 ?></div>
                                <img class="person-photo" src="../assets/personnel/<?= e($person['image']) ?>" alt="<?= e($person['name']) ?>">
                                <div class="person-label">
                                    <div class="person-name"><?= e($person['name']) ?></div>
                                    <div class="person-position"><?= e($person['position']) ?></div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<div class="footer">Core Gateway College, Inc. • CGCI Personnel</div>
</div>
</body>
</html>
