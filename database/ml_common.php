<?php
/**
 * ASTRA Machine Learning Module
 * Multinomial Naive Bayes text classifier implemented in PHP.
 * The model is trained from ml/ml_dataset.csv and stored in ml/ml_model.json.
 */

function mlNormalizeText($text) {
    $text = strtolower(html_entity_decode((string)$text));
    $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
    $text = preg_replace('/\s+/', ' ', trim($text));
    return $text;
}

function mlTokens($text) {
    $stop = [
        'the','a','an','is','are','was','were','what','where','when','why','how',
        'can','could','would','will','please','tell','show','give','me','about',
        'to','for','of','do','does','did','my','your','i','am','im','and','or',
        'in','on','at','from','with','this','that','it','there','any','have','has',
        'ako','ba','nga','po','naman','lang','si','ang','yung','ito','na'
    ];
    $words = preg_split('/\s+/', mlNormalizeText($text));
    $tokens = [];
    foreach ($words as $word) {
        if ($word !== '' && strlen($word) >= 2 && !in_array($word, $stop, true)) {
            $tokens[] = $word;
        }
    }
    return $tokens;
}

function mlLoadModel() {
    static $model = null;
    if ($model !== null) return $model;
    $path = __DIR__ . '/ml/ml_model.json';
    if (!is_file($path)) return null;
    $json = file_get_contents($path);
    $model = json_decode($json, true);
    return is_array($model) ? $model : null;
}

function mlPredict($text) {
    $model = mlLoadModel();
    if (!$model || empty($model['classes'])) {
        return ['intent' => 'unknown', 'confidence' => 0.0, 'scores' => []];
    }

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
    foreach ($values as $value) {
        $sumExp += exp($value - $maxLog);
    }
    $confidence = $sumExp > 0 ? 1.0 / $sumExp : 0.0;

    return [
        'intent' => $top ?: 'unknown',
        'confidence' => round($confidence, 4),
        'scores' => array_map(function($v) use ($maxLog, $sumExp) {
            return round(exp($v - $maxLog) / max($sumExp, 1e-12), 4);
        }, $scores)
    ];
}
?>
