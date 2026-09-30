<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit('Admin access required.');
}

require_once __DIR__ . '/ml_common.php';

$csvPath = __DIR__ . '/ml_dataset.csv';
$modelPath = __DIR__ . '/ml/ml_model.json';
$reportPath = __DIR__ . '/ml/ml_evaluation.json';

if (!is_dir(__DIR__ . '/ml')) mkdir(__DIR__ . '/ml', 0775, true);

function astra_train_rows_from_csv($csvPath) {
    if (!is_file($csvPath)) return [];
    $handle = fopen($csvPath, 'r');
    if (!$handle) return [];
    fgetcsv($handle);
    $rows = [];
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) >= 2 && trim($row[0]) !== '' && trim($row[1]) !== '') {
            $rows[] = ['intent' => trim($row[0]), 'text' => trim($row[1])];
        }
    }
    fclose($handle);
    return $rows;
}

function astra_build_model($rows) {
    $classes = [];
    $classDocs = [];
    $classTotalWords = [];
    $wordCounts = [];
    $vocabulary = [];

    foreach ($rows as $row) {
        $class = $row['intent'];
        $classes[$class] = true;
        $classDocs[$class] = ($classDocs[$class] ?? 0) + 1;
        $wordCounts[$class] = $wordCounts[$class] ?? [];
        $tokens = mlTokens($row['text']);
        foreach ($tokens as $token) {
            $vocabulary[$token] = true;
            $wordCounts[$class][$token] = ($wordCounts[$class][$token] ?? 0) + 1;
            $classTotalWords[$class] = ($classTotalWords[$class] ?? 0) + 1;
        }
    }

    return [
        'algorithm' => 'Multinomial Naive Bayes',
        'version' => 2,
        'trained_at' => date('c'),
        'alpha' => 1.0,
        'total_docs' => count($rows),
        'classes' => array_keys($classes),
        'class_docs' => $classDocs,
        'class_total_words' => $classTotalWords,
        'vocabulary' => array_keys($vocabulary),
        'word_counts' => $wordCounts
    ];
}

function astra_predict_with_model($model, $text) {
    if (!$model || empty($model['classes'])) return ['intent'=>'unknown','confidence'=>0,'scores'=>[]];
    $tokens = mlTokens($text);
    $scores = [];
    $totalDocs = max(1, (int)$model['total_docs']);
    $vocabSize = max(1, count($model['vocabulary']));
    $alpha = (float)($model['alpha'] ?? 1.0);

    foreach ($model['classes'] as $class) {
        $classDocs = (int)($model['class_docs'][$class] ?? 0);
        $logProb = log(max($classDocs, 1) / $totalDocs);
        $totalWords = (int)($model['class_total_words'][$class] ?? 0);
        $denom = $totalWords + ($alpha * $vocabSize);
        foreach ($tokens as $token) {
            $count = (int)($model['word_counts'][$class][$token] ?? 0);
            $logProb += log(($count + $alpha) / max($denom, 1));
        }
        $scores[$class] = $logProb;
    }

    arsort($scores, SORT_NUMERIC);
    $top = array_key_first($scores);
    $values = array_values($scores);
    $maxLog = $values[0] ?? 0;
    $sumExp = 0.0;
    foreach ($values as $value) $sumExp += exp($value - $maxLog);
    $confidence = $sumExp > 0 ? 1.0 / $sumExp : 0.0;

    return ['intent'=>$top ?: 'unknown', 'confidence'=>$confidence, 'scores'=>$scores];
}

function astra_evaluate_model($model) {
    // These are deliberately separate evaluation phrases, not copies of the training CSV.
    $test = [
        'admission'=>['What papers should a new applicant prepare?','How do I start applying to the college?','Where do freshmen submit their requirements?','What do I need before I become a student?','Where can I ask about getting admitted?'],
        'tuition'=>['How much money should I prepare for school fees?','Where do students settle their fees?','What payment choices are available for tuition?','How much will I pay for the semester?','Where is the place for tuition payment?'],
        'courses'=>['Which degree can I study at the college?','What academic programs can I choose from?','Does the school offer a computing degree?','Tell me the programs available to students.','What can I take as my college course?'],
        'enrollment'=>['How do I register for the coming term?','When does student registration begin?','What should I do to enroll in classes?','What are the steps to register as a student?','How can I complete my enrollment?'],
        'registrar'=>['Where can I request an official school record?','Who handles student documents and records?','Where do I get my transcript?','How can I request a certificate from school?','Where should I go for registrar concerns?'],
        'guidance'=>['Where can I talk to the guidance counselor?','Who can help me with a student concern?','Where is the counseling office?','Can students ask the counselor for help?','How do I reach the guidance office?'],
        'library'=>['Where can I read or borrow books on campus?','Can students study inside the library?','Where should I go to borrow a book?','How do I find the campus library?','Is there a place for students to read books?'],
        'student_activities'=>['What organizations or activities can students join?','Where can I learn about student clubs?','Are there activities for students outside class?','How can I participate in campus activities?','What student organizations are available?'],
        'events'=>['What school activities are coming up?','Is there an upcoming campus event?','What is scheduled for this month at school?','When is the next college activity?','Where can I see the school event schedule?'],
        'map'=>['How can I find a building on campus?','Where is a specific office located?','Can ASTRA show me the way around school?','How do I navigate to another campus building?','Where can I find the building I need?'],
        'personnel'=>['Who are the faculty members in the college?','Which staff member works in this office?','Can I find information about school personnel?','Who is assigned to a department?','Where can I view the faculty directory?'],
        'emergency'=>['What should I do during a campus emergency?','Who should I contact if there is an emergency?','Where can I get emergency assistance at school?','What number should I use for urgent help?','How do I report an emergency on campus?'],
        'food'=>['Where can students buy food on campus?','Is there a place to eat inside the school?','Where is the campus food area?','Can I buy snacks at school?','What food options are available for students?'],
        'computer_lab'=>['Where can I use a school computer?','Is there a computer laboratory on campus?','Where are the computers for student use?','Can students use the computer lab?','How do I find the campus computer laboratory?'],
        'scholarship'=>['What financial aid can students apply for?','Are there scholarships available at the college?','How can I apply for student assistance?','Where can I ask about scholarships?','What scholarship opportunities are offered?']
    ];

    $total=0; $correct=0; $confusion=[]; $classStats=[];
    foreach ($test as $expected=>$questions) {
        $classStats[$expected]=['expected'=>count($questions),'correct'=>0];
        foreach ($questions as $q) {
            $p=astra_predict_with_model($model,$q); $total++;
            if ($p['intent']===$expected) { $correct++; $classStats[$expected]['correct']++; }
            if ($p['intent']!==$expected) {
                $confusion[$expected][$p['intent']] = ($confusion[$expected][$p['intent']] ?? 0)+1;
            }
        }
    }
    $accuracy = $total ? $correct/$total : 0;
    $precisions=[]; $recalls=[]; $f1s=[];
    $classes=array_keys($test);
    foreach ($classes as $c) {
        $tp=$classStats[$c]['correct']; $actual=$classStats[$c]['expected']; $fp=0;
        foreach ($confusion as $expected=>$preds) foreach ($preds as $pred=>$n) if ($pred===$c) $fp += $n;
        $precision=($tp+$fp)>0?$tp/($tp+$fp):0; $recall=$actual>0?$tp/$actual:0;
        $f1=($precision+$recall)>0?2*$precision*$recall/($precision+$recall):0;
        $precisions[$c]=$precision; $recalls[$c]=$recall; $f1s[$c]=$f1;
    }
    return [
        'evaluated_at'=>date('c'),'test_examples'=>$total,'correct'=>$correct,'accuracy'=>$accuracy,
        'macro_precision'=>count($classes)?array_sum($precisions)/count($classes):0,
        'macro_recall'=>count($classes)?array_sum($recalls)/count($classes):0,
        'macro_f1'=>count($classes)?array_sum($f1s)/count($classes):0,
        'per_class'=>$classStats,'confusion'=>$confusion
    ];
}

$action = $_POST['action'] ?? 'view';
$message=''; $error=''; $model=null; $report=null;

if ($action === 'train_evaluate') {
    define('ASTRA_GENERATE_SILENT', true);
    require __DIR__ . '/ml_generate_dataset.php';
    $rows=astra_train_rows_from_csv($csvPath);
    if (!$rows) $error='Training dataset is empty.';
    else {
        $model=astra_build_model($rows);
        if (file_put_contents($modelPath,json_encode($model,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES))===false) $error='Could not write the model file.';
        else {
            $report=astra_evaluate_model($model);
            file_put_contents($reportPath,json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
            $message='Model trained and evaluated successfully.';
        }
    }
}

if (!$model && is_file($modelPath)) $model=json_decode(file_get_contents($modelPath),true);
if (!$report && is_file($reportPath)) $report=json_decode(file_get_contents($reportPath),true);
$datasetRows=astra_train_rows_from_csv($csvPath);
$classes=$model['classes']??[];

function pct($v){return number_format(((float)$v)*100,2).'%';}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>ASTRA ML Training & Evaluation</title>
<style>
*{box-sizing:border-box;font-family:'Segoe UI',sans-serif}body{margin:0;background:radial-gradient(circle at top,#004d5e,#07131f 42%,#03070d);color:#fff;min-height:100vh}.wrap{width:94%;max-width:1250px;margin:0 auto;padding:30px 0 50px}.top{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:25px}.top h1{color:#00ffff;text-shadow:0 0 15px cyan;margin:0}.btn{border:0;border-radius:12px;padding:14px 20px;font-weight:800;cursor:pointer;text-decoration:none;display:inline-block}.primary{background:linear-gradient(45deg,#00a8b5,#00ffff);color:#00151b;box-shadow:0 0 22px rgba(0,255,255,.35)}.secondary{background:#102333;color:#bffcff;border:1px solid rgba(0,255,255,.25)}.card{background:rgba(255,255,255,.055);border:1px solid rgba(0,255,255,.16);border-radius:20px;padding:25px;margin:18px 0;backdrop-filter:blur(16px);box-shadow:0 0 25px rgba(0,255,255,.10)}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px}.stat{padding:20px;border-radius:16px;background:rgba(0,255,255,.06);border:1px solid rgba(0,255,255,.12)}.stat b{font-size:30px;color:#00ffff;display:block}.muted{color:#aab8c4}.ok{padding:14px;border-radius:12px;background:rgba(0,255,150,.10);border:1px solid rgba(0,255,150,.35);color:#aaffd9}.err{padding:14px;border-radius:12px;background:rgba(255,50,50,.10);border:1px solid rgba(255,80,80,.35);color:#ffd0d0}table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,.1);text-align:left}th{color:#00ffff}.bar{height:9px;background:#102333;border-radius:20px;overflow:hidden}.bar span{display:block;height:100%;background:#00ffff}.actions{display:flex;gap:12px;flex-wrap:wrap}@media(max-width:700px){.top{flex-direction:column;align-items:flex-start}}
</style></head><body><div class="wrap">
<div class="top"><div><h1>🤖 ASTRA ML</h1><div class="muted">Training, evaluation, and model performance</div></div><a class="btn secondary" href="dashboard.php">← Dashboard</a></div>
<?php if($message):?><div class="ok">✓ <?=htmlspecialchars($message)?></div><?php endif;?><?php if($error):?><div class="err">⚠ <?=htmlspecialchars($error)?></div><?php endif;?>
<div class="card"><h2>Train + Evaluate Model</h2><p class="muted">This rebuilds the Multinomial Naive Bayes model from <code>ml_dataset.csv</code>, then tests it against a separate built-in evaluation set of 75 questions.</p><form method="post"><input type="hidden" name="action" value="train_evaluate"><button class="btn primary" type="submit">🧠 Train + Evaluate Model</button></form></div>
<div class="grid"><div class="stat"><span class="muted">Training examples</span><b><?=count($datasetRows)?></b></div><div class="stat"><span class="muted">Intent classes</span><b><?=count($classes)?></b></div><div class="stat"><span class="muted">Vocabulary</span><b><?=count($model['vocabulary']??[]) ?></b></div><div class="stat"><span class="muted">Model version</span><b><?=htmlspecialchars((string)($model['version']??'—'))?></b></div></div>
<?php if($report):?><div class="card"><h2>ML Evaluation Results</h2><div class="grid"><div class="stat"><span class="muted">Accuracy</span><b><?=pct($report['accuracy'])?></b></div><div class="stat"><span class="muted">Macro Precision</span><b><?=pct($report['macro_precision'])?></b></div><div class="stat"><span class="muted">Macro Recall</span><b><?=pct($report['macro_recall'])?></b></div><div class="stat"><span class="muted">Macro F1</span><b><?=pct($report['macro_f1'])?></b></div></div><p class="muted"><?=intval($report['correct'])?> correct out of <?=intval($report['test_examples'])?> evaluation questions. Last evaluated: <?=htmlspecialchars($report['evaluated_at'])?></p></div>
<div class="card"><h2>Per-Intent Performance</h2><table><tr><th>Intent</th><th>Correct</th><th>Tested</th><th>Recall</th></tr><?php foreach($report['per_class'] as $intent=>$s):$rec=$s['expected']?$s['correct']/$s['expected']:0;?><tr><td><?=htmlspecialchars($intent)?></td><td><?=intval($s['correct'])?></td><td><?=intval($s['expected'])?></td><td><div><?=pct($rec)?></div><div class="bar"><span style="width:<?=max(0,min(100,$rec*100))?>%"></span></div></td></tr><?php endforeach;?></table></div>
<div class="card"><h2>Misclassifications</h2><?php if(empty($report['confusion'])):?><p class="ok">No misclassifications were recorded in the evaluation set.</p><?php else:?><table><tr><th>Expected</th><th>Predicted</th><th>Count</th></tr><?php foreach($report['confusion'] as $expected=>$preds) foreach($preds as $pred=>$n):?><tr><td><?=htmlspecialchars($expected)?></td><td><?=htmlspecialchars($pred)?></td><td><?=intval($n)?></td></tr><?php endforeach;?></table><?php endif;?></div><?php else:?><div class="card"><h2>No evaluation report yet</h2><p class="muted">Click <b>Train + Evaluate Model</b> to create the first performance report.</p></div><?php endif;?>
<div class="card"><h2>How to improve ASTRA</h2><ol class="muted"><li>Add more labeled examples to <code>ml_dataset.csv</code>.</li><li>Use real student wording, including Taglish variations.</li><li>Retrain and evaluate again.</li><li>Review misclassifications and add corrected examples.</li></ol></div>
</div></body></html>
