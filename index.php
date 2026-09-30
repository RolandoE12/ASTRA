<?php
date_default_timezone_set("Asia/Manila");

require_once "database/database.php";
require_once "database/personnel_org.php";

$hour = date("G"); // 0-23

if ($hour >= 0 && $hour < 12) {
    // 12:00 AM - 11:59 AM
    $greeting = "GOOD MORNING";
}
elseif ($hour >= 12 && $hour < 18) {
    // 12:00 PM - 5:59 PM
    $greeting = "GOOD AFTERNOON";
}
else {
    // 6:00 PM - 11:59 PM
    $greeting = "GOOD EVENING";
}
?>



<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">

<title>ASTRA</title>

<link rel="stylesheet" href="css_js/style.css">
<link rel="stylesheet" href="css_js/cards.css">
<link rel="stylesheet" href="css_js/scroll.css">
<link rel="stylesheet" href="css_js/admission.css">
<link rel="stylesheet" href="css_js/courses.css">
<link rel="stylesheet" href="css_js/osas.css">
<link rel="stylesheet" href="css_js/osas_details.css">
<link rel="stylesheet" href="css_js/events.css">
<link rel="stylesheet" href="css_js/student_act.css">
<link rel="stylesheet" href="css_js/library.css">
<link rel="stylesheet" href="css_js/computer.css">
<link rel="stylesheet" href="css_js/guidance.css">
<link rel="stylesheet" href="css_js/student_publication.css">
<link rel="stylesheet" href="css_js/supplies.css">
<link rel="stylesheet" href="css_js/food.css">
<link rel="stylesheet" href="css_js/bscs.css">
<link rel="stylesheet" href="css_js/bsba.css">
<link rel="stylesheet" href="css_js/bsed.css">
<link rel="stylesheet" href="css_js/emergency.css">
<link rel="stylesheet" href="css_js/personnel.css">



<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&display=swap" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

</head>

<body>

<div class="background-overlay"></div>

<div id="particles"></div>

<div class="container">

<!-- ================= TOP STATUS ================= -->

<div class="status-panel">


<div class="status-text">

<div class="status-title">

    <img src="assets/core.png"  class="status-logo">

    <span>CORE GATEWAY COLLEGE INC.</span>

</div>



</div>


</div>

<!-- ================= MAIN SCREEN ================= -->

<div class="main-screen">

<div class="scanline"></div>

<div class="screen-glow"></div>

<div class="hud-grid"></div>

<!-- HUD Decorations -->

<div class="hud-top-left"></div>
<div class="hud-top-right"></div>

<div class="hud-bottom-left"></div>
<div class="hud-bottom-right"></div>

<div class="hud-left-line"></div>
<div class="hud-right-line"></div>

<div class="hud-top-line"></div>
<div class="hud-bottom-line"></div>

<div class="hex-grid"></div>

<div class="radar-circle"></div>

<div class="mouse-glow"></div>

<div class="corner tl"></div>
<div class="corner tr"></div>
<div class="corner bl"></div>
<div class="corner br"></div>

<div class="left-decoration"></div>
<div class="right-decoration"></div>

<div class="screen-content">

<div class="greeting">

<?php echo $greeting; ?>

</div>

<h1 class="title">

ASTRA

</h1>

<div id="clock"></div>

<div id="date"></div>

<!-- CHAT -->

<!-- ==========================================
TOP FEATURE CARDS
========================================== -->

<div class="top-cards">

    <div class="feature-card" id="coreMapCard">

        <img src="assets/MAP.png">

        <div class="card-title">
            CORE MAP
        </div>

    </div>

    <div class="feature-card" id="informationCard">

        <img src="assets/INFO.jpg">

        <div class="card-title">
            INFORMATION
        </div>

    </div>

    <div class="feature-card" id="featuresCard">

        <img src="assets/FEATURES.jpg">

        <div class="card-title">
            FEATURES
        </div>

    </div>

</div>

<!-- =====================================
 ASTRA FEATURES PAGE
====================================== -->

<section id="featuresPage" class="features-page" aria-labelledby="featuresHeading">
    <div class="features-orbit features-orbit-one"></div>
    <div class="features-orbit features-orbit-two"></div>

    <header class="features-hero">
        <button id="backFeatures" class="features-back-btn" type="button">
            <i class="fa-solid fa-arrow-left"></i> BACK TO ASTRA
        </button>
        <div class="features-kicker"><i class="fa-solid fa-sparkles"></i> YOUR CAMPUS COMPANION</div>
        <h2 id="featuresHeading">Everything you need,<br><span>one helpful assistant.</span></h2>
        <p>ASTRA makes it easier to explore campus services, get quick answers, and stay connected to what matters at CORE.</p>
    </header>

    <div class="features-grid">
        <article class="astra-feature-item feature-highlight">
            <div class="feature-icon"><i class="fa-solid fa-comments"></i></div>
            <div>
                <span class="feature-number">01</span>
                <h3>Ask ASTRA</h3>
                <p>Get clear, instant guidance about school services, enrollment, courses, and everyday campus questions.</p>
            </div>
        </article>

        <article class="astra-feature-item">
            <div class="feature-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <div>
                <span class="feature-number">02</span>
                <h3>Smart Suggestions</h3>
                <p>Discover useful follow-up questions that help you find the right information faster.</p>
            </div>
        </article>

        <article class="astra-feature-item">
            <div class="feature-icon"><i class="fa-solid fa-map-location-dot"></i></div>
            <div>
                <span class="feature-number">03</span>
                <h3>Campus Navigator</h3>
                <p>Use the CORE map to locate key offices, student spaces, and campus destinations.</p>
            </div>
        </article>

        <article class="astra-feature-item">
            <div class="feature-icon"><i class="fa-solid fa-bell"></i></div>
            <div>
                <span class="feature-number">04</span>
                <h3>Campus Updates</h3>
                <p>Browse upcoming events, admissions information, services, and important announcements.</p>
            </div>
        </article>
    </div>

    <div class="features-footer-note">
        <span class="features-status-dot"></span>
        READY WHEN YOU ARE — TYPE A QUESTION BELOW TO START.
    </div>
</section>


<!-- ==========================
 INFORMATION PAGE
========================== -->

<div id="informationPage" class="information-page">

    <h2>INFORMATION</h2>

    <div class="info-cards">

<div class="info-card" id="coursesCard">
    <img src="assets/info1.jpeg">
    <span>COURSES</span>
</div>

        <div class="info-card" id="eventsCard">
            <img src="assets/info2.jpeg">
            <span>UP-COMING EVENTS</span>
        </div>

        <div class="info-card" id="osasCard">
            <img src="assets/info3.jpg">
            <span>OSAS</span>
        </div>

        <div class="info-card" id="admissionCard">
            <img src="assets/info4.jpeg">
            <span>ADMISSION</span>
        </div>

        <div class="info-card" id="emergencyHotlineBtn" onclick="openEmergencyPanel()">
            <img src="assets/info5.jpeg">
            <span>EMERGENCY HOTLINE</span>
        </div>

<div class="info-card" id="personnelCard">

<img src="assets/info66.jpeg">

<span>
CGCI PERSONNEL
</span>

</div>

    </div>

    <button id="backMain">
        <i class="fa-solid fa-arrow-left"></i> BACK
    </button>

</div>

<!-- =====================================
CORE MAP PAGE
====================================== -->

<div id="coreMapPage" class="core-map-page">

    <div class="core-map-header">
        <button id="backCoreMap" class="core-back-btn">
            <i class="fa-solid fa-arrow-left"></i> BACK
        </button>

        <h2>CORE MAP</h2>
        <p>SELECT AN OFFICE OR LOCATION TO VIEW ITS MAP</p>
    </div>

    <div class="core-location-grid">

        <button class="core-location-card full-map-card" data-title="FULL MAP OF CORE" data-full-map="true">
            <i class="fa-solid fa-map-location-dot"></i>
            <span>FULL MAP OF CORE</span>
        </button>

        <button class="core-location-card" data-title="OFFICE OF REGISTRAR">
            <i class="fa-solid fa-file-signature"></i>
            <span>OFFICE OF REGISTRAR</span>
        </button>

        <button class="core-location-card" data-title="OFFICE OF UNIFAST">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>OFFICE OF UNIFAST</span>
        </button>

        <button class="core-location-card" data-title="OFFICE OF THE VPAA">
            <i class="fa-solid fa-user-tie"></i>
            <span>OFFICE OF THE VPAA</span>
        </button>

        <button class="core-location-card" data-title="OFFICE OF PRESIDENT">
            <i class="fa-solid fa-building-columns"></i>
            <span>OFFICE OF PRESIDENT</span>
        </button>

        <button class="core-location-card" data-title="OFFICE OF STUDENT AFFAIRS">
            <i class="fa-solid fa-users"></i>
            <span>OFFICE OF STUDENT AFFAIRS</span>
        </button>

        <button class="core-location-card" data-title="GUIDANCE OFFICE">
            <i class="fa-solid fa-comments"></i>
            <span>GUIDANCE OFFICE</span>
        </button>

        <button class="core-location-card" data-title="LIBRARY">
            <i class="fa-solid fa-book-open"></i>
            <span>LIBRARY</span>
        </button>

        <button class="core-location-card" data-title="ACCOUNTING OFFICE">
            <i class="fa-solid fa-calculator"></i>
            <span>ACCOUNTING OFFICE</span>
        </button>

        <button class="core-location-card" data-title="CASHIER">
            <i class="fa-solid fa-cash-register"></i>
            <span>CASHIER</span>
        </button>

        <button class="core-location-card" data-title="ADMISSIONS OFFICE">
            <i class="fa-solid fa-door-open"></i>
            <span>ADMISSIONS OFFICE</span>
        </button>

        <button class="core-location-card" data-title="HUMAN RESOURCE OFFICE">
            <i class="fa-solid fa-user-group"></i>
            <span>HUMAN RESOURCE OFFICE</span>
        </button>

        <button class="core-location-card" data-title="SCHOOL CLINIC">
            <i class="fa-solid fa-kit-medical"></i>
            <span>SCHOOL CLINIC</span>
        </button>

        <button class="core-location-card" data-title="SECURITY OFFICE">
            <i class="fa-solid fa-shield-halved"></i>
            <span>SECURITY OFFICE</span>
        </button>

        <button class="core-location-card" data-title="IT OFFICE">
            <i class="fa-solid fa-computer"></i>
            <span>IT OFFICE</span>
        </button>

        <button class="core-location-card" data-title="PROCUREMENT OFFICE">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>PROCUREMENT OFFICE</span>
        </button>

        <button class="core-location-card" data-title="FACILITIES &amp; MAINTENANCE">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <span>FACILITIES &amp; MAINTENANCE</span>
        </button>

        <button class="core-location-card" data-title="PUBLIC INFORMATION OFFICE">
            <i class="fa-solid fa-bullhorn"></i>
            <span>PUBLIC INFORMATION OFFICE</span>
        </button>

        <button class="core-location-card" data-title="RESEARCH &amp; EXTENSION OFFICE">
            <i class="fa-solid fa-flask"></i>
            <span>RESEARCH &amp; EXTENSION OFFICE</span>
        </button>

        <button class="core-location-card" data-title="SCHOLARSHIP OFFICE">
            <i class="fa-solid fa-award"></i>
            <span>SCHOLARSHIP OFFICE</span>
        </button>

    </div>

</div>

<!-- CORE MAP VIEWER -->
<div id="coreMapViewer" class="core-map-viewer">
    <button id="closeCoreMapViewer" class="core-viewer-close">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <h2 id="coreMapViewerTitle">FULL MAP OF CORE</h2>

    <div class="core-map-image-wrap">
        <img src="assets/MAP.png" alt="CORE Campus Map">
    </div>

    <p id="coreMapViewerText">Campus map of CORE Gateway College Inc.</p>
</div>

<!-- =====================================
CGCI PERSONNEL PAGE
====================================== -->

<?php
/* Build the personnel hierarchy once so every department can be displayed as
   an organizational chart. parent_id is managed from the admin personnel page. */
$personnelDepartments = [];
$deptResult = mysqli_query($conn, "SELECT id,name,description,image FROM departments ORDER BY name ASC");
while ($d = mysqli_fetch_assoc($deptResult)) {
    $personnelDepartments[(int)$d['id']] = $d;
    $personnelDepartments[(int)$d['id']]['people'] = [];
}

$personResult = mysqli_query(
    $conn,
    "SELECT id,department_id,parent_id,name,position,image,sort_order
     FROM personnel
     ORDER BY department_id ASC, sort_order ASC, name ASC"
);
while ($person = mysqli_fetch_assoc($personResult)) {
    $deptId = (int)$person['department_id'];
    if (isset($personnelDepartments[$deptId])) {
        $personnelDepartments[$deptId]['people'][] = $person;
    }
}

function personnelPositionRank(string $position): int {
    $p = strtolower(trim($position));

    // Higher number = higher position in the public organizational chart.
    // This is only a display fallback when an explicit Reports To relationship
    // has not been configured in the admin personnel manager.
    $rankRules = [
        100 => ['dean'],
        95  => ['president', 'vice president', 'vp '],
        90  => ['executive director', 'school director', 'campus director'],
        85  => ['director', 'department head', 'office head', 'unit head', 'chairperson', 'chair'],
        80  => ['assistant dean', 'associate dean', 'program head', 'program chair', 'coordinator', 'registrar', 'principal'],
        70  => ['supervisor', 'manager', 'lead'],
        60  => ['professor', 'associate professor', 'assistant professor'],
        50  => ['instructor', 'faculty', 'teacher', 'guidance facilitator'],
        40  => ['staff', 'nurse', 'librarian', 'secretary', 'clerk', 'assistant', 'aide', 'technician', 'utility'],
    ];

    foreach ($rankRules as $rank => $keywords) {
        foreach ($keywords as $keyword) {
            if (str_contains($p, $keyword)) {
                return $rank;
            }
        }
    }

    return 30;
}

function personnelOrgTree(array $people): array {
    $byId = [];
    foreach ($people as $person) {
        $person['children'] = [];
        $person['_rank'] = personnelPositionRank((string)$person['position']);
        $byId[(int)$person['id']] = $person;
    }

    if (!$byId) return [];

    // Explicit Reports To relationships always take priority.
    $hasExplicitParent = false;
    foreach ($byId as $person) {
        if ($person['parent_id'] !== null && (int)$person['parent_id'] > 0 && isset($byId[(int)$person['parent_id']])) {
            $hasExplicitParent = true;
            break;
        }
    }

    if ($hasExplicitParent) {
        $roots = [];
        foreach ($byId as $id => &$person) {
            $parentId = $person['parent_id'] !== null ? (int)$person['parent_id'] : 0;
            if ($parentId > 0 && isset($byId[$parentId]) && $parentId !== $id) {
                $byId[$parentId]['children'][] =& $person;
            } else {
                $roots[] =& $person;
            }
        }
        unset($person);
    } else {
        // No hierarchy has been configured yet. Build a sensible display
        // hierarchy from job titles so the highest-ranked person is alone at
        // the top, with lower-ranked personnel progressively underneath.
        $nodes = array_values($byId);
        usort($nodes, function($a, $b) {
            return ($b['_rank'] <=> $a['_rank']) ?: strcasecmp($a['name'], $b['name']);
        });

        $root = array_shift($nodes);
        $levels = [$root['_rank'] => [$root]];

        foreach ($nodes as $node) {
            // Attach each person to the nearest higher-ranked person.
            $parentIndex = null;
            $bestRank = -1;
            foreach ($levels as $levelRank => $levelNodes) {
                if ((int)$levelRank > (int)$node['_rank'] && (int)$levelRank > $bestRank) {
                    $parentIndex = $levelNodes[0]['id'] ?? null;
                    $bestRank = (int)$levelRank;
                }
            }

            // If no strictly higher level exists, keep the person under the
            // highest node. This guarantees a single top position.
            if ($parentIndex === null) {
                $parentIndex = $root['id'];
            }

            // Find the actual parent node recursively and append the child.
            $appendChild = function (&$tree, $targetId, $child) use (&$appendChild): bool {
                if ((int)$tree['id'] === (int)$targetId) {
                    $tree['children'][] = $child;
                    return true;
                }
                foreach ($tree['children'] as &$childNode) {
                    if ($appendChild($childNode, $targetId, $child)) return true;
                }
                return false;
            };
            $appendChild($root, $parentIndex, $node);

            $levels[$node['_rank']][] = $node;
        }

        $roots = [$root];
    }

    $sortTree = function (&$node) use (&$sortTree): void {
        usort($node['children'], function($a, $b) {
            return ($b['_rank'] <=> $a['_rank']) ?: strcasecmp($a['name'], $b['name']);
        });
        foreach ($node['children'] as &$child) {
            $sortTree($child);
        }
    };

    usort($roots, function($a, $b) {
        return ($b['_rank'] <=> $a['_rank']) ?: strcasecmp($a['name'], $b['name']);
    });
    foreach ($roots as &$rootNode) $sortTree($rootNode);
    unset($rootNode);

    return $roots;
}

function flattenPersonnelOrg(array $roots): array {
    $all = [];
    $walk = function(array $node) use (&$walk, &$all): void {
        $children = $node['children'] ?? [];
        unset($node['children']);
        $all[] = $node;
        foreach ($children as $child) {
            $walk($child);
        }
    };
    foreach ($roots as $root) {
        $walk($root);
    }
    return $all;
}

function renderPersonnelCard(array $person, bool $isRoot = false): void {
    $name = htmlspecialchars($person['name'], ENT_QUOTES, 'UTF-8');
    $position = htmlspecialchars($person['position'], ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars($person['image'], ENT_QUOTES, 'UTF-8');
?>
<article class="org-person <?= $isRoot ? 'org-person-head' : '' ?>">
    <div class="org-photo-ring">
        <img src="assets/personnel/<?=$image?>" alt="<?=$name?>">
    </div>
    <div class="org-person-label">
        <strong><?=$name?></strong>
        <span><?=$position?></span>
    </div>
</article>
<?php
}

function renderPersonnelOrgChart(array $roots): void {
    if (empty($roots)) return;

    /*
       The public chart is intentionally flat by organizational level:
       the highest-ranked person is alone at the top, while everyone below
       is sorted high-to-low and displayed in rows of exactly five columns.
       This avoids nested flex/grid containers that can squeeze cards into
       narrow columns and cause horizontal scrolling.
    */
    $people = flattenPersonnelOrg($roots);
    if (!$people) return;

    usort($people, function($a, $b) {
        return ($b['_rank'] <=> $a['_rank']) ?: strcasecmp($a['name'], $b['name']);
    });

    $highestRank = $people[0]['_rank'];
    $top = $people[0];
    $remaining = array_slice($people, 1);

    echo '<div class="org-level org-top-level">';
    renderPersonnelCard($top, true);
    echo '</div>';

    if (!empty($remaining)) {
        echo '<div class="org-main-connector"></div>';
        foreach (array_chunk($remaining, 5) as $rowIndex => $row) {
            echo '<div class="org-level-row-wrap">';
            echo '<div class="org-row-connector"></div>';
            echo '<div class="org-level-row">';
            foreach ($row as $person) {
                echo '<div class="org-level-cell">';
                renderPersonnelCard($person);
                echo '</div>';
            }
            for ($i = count($row); $i < 5; $i++) {
                echo '<div class="org-level-cell org-level-cell-empty" aria-hidden="true"></div>';
            }
            echo '</div>';
            echo '</div>';
        }
    }
}

?>

<div id="personnelPage" class="personnel-page">
    <button id="backPersonnel"><i class="fa-solid fa-arrow-left"></i> BACK</button>
    <h2>CGCI PERSONNEL</h2>
    <p class="personnel-page-subtitle">Select a department or office to view its organizational chart.</p>

    <div class="personnel-cards">
        <?php foreach ($personnelDepartments as $department): ?>
            <div class="personnel-department-card" data-id="<?= (int)$department['id'] ?>">
                <img src="assets/personnel/<?=htmlspecialchars($department['image'], ENT_QUOTES, 'UTF-8')?>" alt="">
                <div class="department-name"><?=htmlspecialchars($department['name'], ENT_QUOTES, 'UTF-8')?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- =====================================
PERSONNEL ORGANIZATIONAL CHART PAGE
====================================== -->

<div id="personnelDetailsPage" class="personnel-details-page">
    <button id="backDepartment"><i class="fa-solid fa-arrow-left"></i> BACK</button>

    <div class="org-chart-header">
        <div class="org-chart-brand">CORE GATEWAY COLLEGE, INC.</div>
        <h2 id="departmentTitle">CGCI PERSONNEL</h2>
        <p id="departmentDescription">Organizational chart</p>
    </div>

    <div class="org-chart-toolbar">
        <span><i class="fa-solid fa-sitemap"></i> ORGANIZATIONAL CHART</span>
        <button id="orgZoomReset" type="button">RESET VIEW</button>
    </div>

    <div class="org-chart-scroll">
        <?php foreach ($personnelDepartments as $dept):
            $roots = personnelOrgTree($dept['people']);
        ?>
        <section class="department-personnel" id="department<?=$dept['id']?>" data-title="<?=htmlspecialchars($dept['name'], ENT_QUOTES, 'UTF-8')?>" data-description="<?=htmlspecialchars($dept['description'], ENT_QUOTES, 'UTF-8')?>" style="display:none;">
            <?php if (!empty($roots)): ?>
                <div class="org-chart-canvas">
                    <?php renderPersonnelOrgChart($roots); ?>
                </div>
            <?php else: ?>
                <div class="empty-personnel">
                    <i class="fa-solid fa-users-slash"></i>
                    <strong>No personnel found.</strong>
                    <span>This department does not have personnel assigned yet.</span>
                </div>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>
    </div>
</div>

<!-- ================= COURSES PAGE ================= -->

<div id="coursesPage" class="courses-page">

    <button id="backInfo">
        <i class="fa-solid fa-arrow-left"></i> BACK
    </button>

    <h2>COURSES OFFERED</h2>

    <div class="course-cards">
        <div class="course-card" id="bscsCard" onclick="openBscsPanel()">
            <img src="assets/bscs.jpg" class="bscs-logo" alt="BSCS Logo">
            <span>BSCS</span>
        </div>
        <div class="course-card" id="bsbaCard" onclick="openBsbaPanel()">
            <img src="assets/bsba.jpg">
            <span>BSBA</span>
        </div>
        <div class="course-card" id="bsedCard" onclick="openBsedPanel()">
            <img src="assets/bsis.jpg">
            <span>BSED</span>
        </div>
        <div class="course-card" id="beedCard" onclick="openBeedPanel()">
            <img src="assets/bsba.jpg">
            <span>BEED</span>
        </div>
        <div class="course-card" id="bapsCard" onclick="openBapsPanel()">
            <img src="assets/bscrim.jpg">
            <span>BAPS</span>
        </div>
        <div class="course-card" id="k12Card" onclick="openK12Panel()">
            <img src="assets/bshm.jpg">
            <span>K-12</span>
        </div>
    </div>

</div>

<!-- ================= UP-COMING EVENTS PAGE ================= -->

<div id="eventsPage" class="events-page" style="display: none;">

    <button id="backEvents">
        <i class="fa-solid fa-arrow-left"></i> BACK
    </button>

    <h2 class="events-main-title">UP-COMING EVENTS</h2>

    <div class="events-layout-container">
        <!-- Cyber Events Box (Left) -->
        <div class="events-panel">
            <div class="events-header">
                <h3>UP-COMING EVENTS</h3>
            </div>
            <div class="events-content" id="eventsList">
                <p class="no-events-placeholder">Select a date on the calendar to view events</p>
            </div>
        </div>

        <!-- White Calendar (Right) -->
        <div class="calendar-card">
            <div class="calendar-header">
                <button class="cal-nav-btn" id="prevMonthBtn">&#9664;</button>
                <div class="calendar-title" id="calendarMonthYear">JULY 2026</div>
                <button class="cal-nav-btn" id="nextMonthBtn">&#9654;</button>
            </div>
            <div class="calendar-days-header">
                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
            </div>
            <div class="calendar-grid" id="calendarGrid">
                <!-- Dynamically populated by JS -->
            </div>
        </div>
    </div>

</div>

<!-- ================= OSAS PAGE ================= -->

<div id="osasPage" class="osas-page">

    <button id="backOsas">
        <i class="fa-solid fa-arrow-left"></i> BACK
    </button>

    <h2>OFFICE OF STUDENT AFFAIRS & SERVICES</h2>

    <div class="osas-cards">
        <div class="osas-card" id="studentActivitiesCard">
            <img src="assets/os1.jpeg">
            <span>STUDENT ACTIVITIES</span>
        </div>
        <div class="osas-card" id="libraryCard">
            <img src="assets/os2.jpeg">
            <span>LIBRARY</span>
        </div>
        <div class="osas-card" id="computerCard">
            <img src="assets/os3.jpeg">
            <span>COMPUTER</span>
        </div>
        <div class="osas-card" id="guidanceCard">
            <img src="assets/os4.jpeg">
            <span>GUIDANCE</span>
        </div>
        <div class="osas-card" id="publicationCard">
            <img src="assets/os5.jpeg">
            <span>STUDENT PUBLICATION</span>
        </div>
        <div class="osas-card" id="healthSafetyCard">
            <img src="assets/os6.jpeg">
            <span>HEALTH AND SAFETY</span>
        </div>
        <div class="osas-card" id="suppliesCard">
            <img src="assets/os7.jpeg">
            <span>SUPPLIES</span>
        </div>
        <div class="osas-card" id="foodCard">
            <img src="assets/os8.jpeg">
            <span>FOOD AND DRINKS</span>
        </div>
    </div>

</div>


<!-- EMERGENCY HOTLINE CONTAINER (HIDDEN BY DEFAULT) -->
<div id="emergencyHotlinePage" class="hud-content-view" style="display: none;">
    <div class="hud-top-nav">
        <button class="hud-btn-back" onclick="closeEmergencyPanel()">
            ← BACK
        </button>
    </div>

    <h2 class="hud-main-title">EMERGENCY HOTLINE</h2>

    <!-- 6 HORIZONTAL CARDS GRID -->
    <div class="emergency-cards-grid">
        <div class="emergency-horizontal-card">
            <div class="card-left-img"><img src="assets/security-icon.png"></div>
            <div class="card-right-content">
                <span class="emergency-card-title">CAMPUS SECURITY / OSAS</span>
                <a href="tel:0441234567" class="emergency-card-number">(044) 123-4567</a>
                <span class="emergency-card-subtext">24/7 On-Campus Response</span>
            </div>
        </div>

        <div class="emergency-horizontal-card">
            <div class="card-left-img"><img src="assets/clinic-icon.png"></div>
            <div class="card-right-content">
                <span class="emergency-card-title">MEDICAL CLINIC / ER</span>
                <a href="tel:0449876543" class="emergency-card-number">(044) 987-6543</a>
                <span class="emergency-card-subtext">First Aid & Medical Services</span>
            </div>
        </div>

        <div class="emergency-horizontal-card">
            <div class="card-left-img"><img src="assets/fire-icon.png"></div>
            <div class="card-right-content">
                <span class="emergency-card-title">BUREAU OF FIRE PROTECTION</span>
                <a href="tel:160" class="emergency-card-number">160 / (044) 456-7890</a>
                <span class="emergency-card-subtext">Local Fire Station</span>
            </div>
        </div>

        <div class="emergency-horizontal-card" id="pnpCard" onclick="openPnpPanel()">
            <div class="card-left-img"><img src="assets/police-icon.png"></div>
            <div class="card-right-content">
                <span class="emergency-card-title">PNP STATION</span>
                <a class="emergency-card-number">117 / (044) 321-7654</a>
                <span class="emergency-card-subtext">Local Police Department</span>
            </div>
        </div>

        <div class="emergency-horizontal-card">
            <div class="card-left-img"><img src="assets/disaster-icon.png"></div>
            <div class="card-right-content">
                <span class="emergency-card-title">MDRRMO / DISASTER RISK</span>
                <a href="tel:911" class="emergency-card-number">911 / (044) 555-0199</a>
                <span class="emergency-card-subtext">Disaster Response Operations</span>
            </div>
        </div>

        <div class="emergency-horizontal-card">
            <div class="card-left-img"><img src="assets/mental-icon.png"></div>
            <div class="card-right-content">
                <span class="emergency-card-title">MENTAL HEALTH CRISIS</span>
                <a href="tel:1553" class="emergency-card-number">1553 / 0917-899-8727</a>
                <span class="emergency-card-subtext">NCMH Hopeline</span>
            </div>
        </div>
    </div>
</div>

<!-- ================= ADMISSION, ACADEMIC POLICIES AND UNIFAST PAGE ================= -->

<div id="admissionPage" class="admission-page">

    <button id="backAdmission">
        <i class="fa-solid fa-arrow-left"></i> BACK
    </button>

    <h2>ADMISSION, ACADEMIC AND UNIFAST</h2>

    <div class="admission-cards">

<div class="admission-card" id="freshmenCard">
    <img src="assets/apr.jpeg">
    <span>ADMISSION POLICY </br>AND REQUIREMENTS</span>
</div>

<div class="admission-card" id="transfereesCard">
    <img src="assets/ad2.jpeg">
    <span>ACADEMIC POLICIES</span>
</div>

<div class="admission-card" id="requirementsCard">
    <img src="assets/ad3.jpeg">
    <span>AWARDS, RECOGNITION</br> AND GRADUATION</span>
</div>

        <div class="admission-card">
            <img src="assets/ad4.jpeg">
            <span>ACADEMIC SCHOLARSHIPS </br> AND GRANTS </span>
        </div>

<div class="admission-card" id="scholarshipsCard">
    <img src="assets/ad5.jpeg">
    <span>DISCIPLINARY POLICIES </br> AND ACTIONS</span>
</div>

    </div>

</div>


<!-- ================= ADMISSION POLICY AND REQUIREMENTS PAGE ================= -->

<div id="freshmenPage" class="freshmen-page">

    <button id="backFreshmen">
        <i class="fa-solid fa-arrow-left"></i>
        BACK
    </button>

    <h2>ADMISSION POLICY AND REQUIREMENTS</h2>

    <div class="freshmen-cards">

        <div class="freshmen-card">
            <img src="assets/PR1.jpeg">
            <span>ADMISSION REQUIREMENTS</span>
        </div>

        <div class="freshmen-card">
            <img src="assets/PR2.jpeg">
            <span>ADMISSION PROCEDURE</span>
        </div>

        <div class="freshmen-card">
            <img src="assets/PR3.jpeg">
            <span>LOADING/OVERLOADING </br> OF SUBJECTS</span>
        </div>

        <div class="freshmen-card">
            <img src="assets/PR4.jpeg">
            <span>ADDING, CHANGING, AND </br>DROPPING OF SUBJECTS</span>
        </div>

        <div class="freshmen-card">
            <img src="assets/PR5.jpeg">
            <span>FEES, PAYMENT, AND</br> WITHDRAWALS</span>
        </div>

    </div>

</div>


<!-- ================= ACADEMIC POLICIES PAGE ================= -->

<div id="transfereesPage" class="transferees-page">

    <button id="backTransferees">
        <i class="fa-solid fa-arrow-left"></i>
        BACK
    </button>

    <h2>ACADEMIC POLICIES</h2>

    <div class="transferees-cards">

        <div class="transferees-card">
            <img src="assets/trans1.jpg">
            <span>ATTENDANCE</span>
        </div>

        <div class="transferees-card">
            <img src="assets/trans2.jpg">
            <span>EXAMINATIONS</span>
        </div>

        <div class="transferees-card">
            <img src="assets/trans3.jpg">
            <span>GRADING SYSTEM</span>
        </div>

        <div class="transferees-card">
            <img src="assets/trans4.jpg">
            <span>RETENTION POLICIES</span>
        </div>

        <div class="transferees-card">
            <img src="assets/trans5.jpg">
            <span>REMOVAL OF INCOMPLETE</br> GRADES</span>
        </div>

        <div class="transferees-card">
            <img src="assets/trans6.jpg">
            <span>SHIFTING TO ANOTHER</br> PROGRAM</span>
        </div>

        <div class="transferees-card">
            <img src="assets/trans7.jpg">
            <span>LEAVE OF ABSENCE</span>
        </div>

    </div>

</div>


<!-- ================= AWARDS, RECOGNITION, AND GRADUATION PAGE ================= -->

<div id="requirementsPage" class="requirements-page">

    <button id="backRequirements">
        <i class="fa-solid fa-arrow-left"></i>
        BACK
    </button>

    <h2>AWARDS, RECOGNITION, AND GRADUATION</h2>

    <div class="requirements-cards">

        <div class="requirements-card">
            <img src="assets/requirement1.jpg">
            <span>DEAN'S LISTER</span>
        </div>

        <div class="requirements-card">
            <img src="assets/requirement2.jpg">
            <span>LATIN AWARDS</span>
        </div>

        <div class="requirements-card">
            <img src="assets/requirement3.jpg">
            <span>STUDENT GRADUATION</span>
        </div>

        <div class="requirements-card">
            <img src="assets/requirement4.jpg">
            <span>RELEASING OF ACADEMIC </br> RECORDS</span>
        </div>

    </div>

</div>


<!-- ================= DISCIPLINARY POLICIES AND ACTIONS PAGE ================= -->

<div id="scholarshipsPage" class="scholarships-page">

    <button id="backScholarships">
        <i class="fa-solid fa-arrow-left"></i>
        BACK
    </button>

    <h2>DISCIPLINARY POLICIES AND ACTIONS</h2>

    <div class="scholarships-cards">

        <div class="scholarships-card">
            <img src="assets/scholarship1.jpg">
            <span>INVESTIGATION PROCEDURE </br>FOR STUDENTS' CASES</span>
        </div>

        <div class="scholarships-card">
            <img src="assets/scholarship2.jpg">
            <span>SANCTION</span>
        </div>

        <div class="scholarships-card">
            <img src="assets/scholarship3.jpg">
            <span>DISCIPLINE BOARD</span>
        </div>

        <div class="scholarships-card">
            <img src="assets/scholarship4.jpg">
            <span>SILENT PROVISION</span>
        </div>

    </div>

</div>






<div class="chat-window">

<div id="chatBox">

<div class="ai-message">

Hello.

I'm ASTRA.

How may I assist you today?

</div>

</div>

</div>

<!-- SMART QUESTION SUGGESTIONS -->
<div id="suggestionsBox" class="suggestions-box" aria-label="Suggested questions">
    <div class="suggestions-label">
        <i class="fa-solid fa-wand-magic-sparkles"></i>
        YOU MAY ALSO ASK
    </div>

    <div id="suggestionsList" class="suggestions-list"></div>
</div>

<!-- INPUT -->

<div class="input-area">

<input
type="text"
id="userInput"
placeholder="Ask something..."
autocomplete="off">

<button id="sendBtn">

<i class="fa-solid fa-paper-plane"></i>

</button>

<button id="micBtn">

<i class="fa-solid fa-microphone"></i>

</button>

</div>

</div>


<!-- ROBOT -->


</div>

<div class="robot">

<img src="assets/ROBOTS.png" alt="Robot">

</div>

<!-- LOADER -->

<div class="loader" id="loader">

<div class="loader-circle"></div>

<div class="loader-text">

INITIALIZING ASTRA...

</div>

</div>

<script src="css_js/no-zoom.js"></script>
<script src="css_js/script.js"></script>
<script src="css_js/cards.js"></script>
<script src="css_js/osas_details.js"></script>
<script src="css_js/events.js"></script>
<script src="css_js/bscs.js"></script>
<script src="css_js/bsba.js"></script>
<script src="css_js/bsed.js"></script>
<script src="css_js/beed.js"></script>
<script src="css_js/baps.js"></script>
<script src="css_js/k-12.js"></script>
<script src="css_js/emergency.js"></script>
<script src="css_js/personnel.js"></script>
<script src="css_js/pnp.js"></script>

</body>
</html>

