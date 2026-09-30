<?php

include "database.php";
include "ml_common.php";

header("Content-Type: application/json");

$originalMessage = trim($_POST["message"] ?? "");

if ($originalMessage === "") {
    echo json_encode(["suggestions" => []]);
    exit;
}

function normalizeText($text) {
    $text = strtolower($text);
    $text = html_entity_decode($text);
    $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
    $text = preg_replace('/\s+/', ' ', trim($text));
    return $text;
}

function removeStopWords($text) {
    $stopWords = [
        "who","what","where","when","why","how",
        "is","are","was","were","the","a","an",
        "can","could","would","will","please",
        "tell","show","give","know","about","me",
        "to","for","of","do","does","did",
        "and","or","my","your",
        "ako","ba","nga","po","naman","lang",
        "si","ang","yung","iyan","ito","na"
    ];

    $words = explode(" ", normalizeText($text));
    $filtered = [];

    foreach ($words as $word) {
        if ($word !== "" && !in_array($word, $stopWords, true)) {
            $filtered[] = $word;
        }
    }

    return implode(" ", $filtered);
}

function similarityPercent($a, $b) {
    similar_text($a, $b, $percent);
    return $percent;
}

function levenshteinPercent($a, $b) {
    $distance = levenshtein($a, $b);
    $max = max(strlen($a), strlen($b));

    if ($max === 0) {
        return 100;
    }

    return (1 - ($distance / $max)) * 100;
}

function keywordOverlap($user, $candidate) {
    $userWords = array_unique(explode(" ", removeStopWords($user)));
    $candidateWords = array_unique(explode(" ", removeStopWords($candidate)));

    $userWords = array_filter($userWords, function($w) {
        return strlen($w) >= 3;
    });

    $candidateWords = array_filter($candidateWords, function($w) {
        return strlen($w) >= 3;
    });

    if (count($candidateWords) === 0) {
        return 0;
    }

    $common = count(array_intersect($userWords, $candidateWords));

    // Give stronger weight when the user's important words occur
    // in the candidate question.
    $userCount = max(1, count($userWords));
    $candidateCount = max(1, count($candidateWords));

    $byUser = ($common / $userCount) * 100;
    $byCandidate = ($common / $candidateCount) * 100;

    return ($byUser * 0.65) + ($byCandidate * 0.35);
}

function suggestionScore($user, $question, $keywords) {
    $user = normalizeText($user);
    $question = normalizeText($question);

    $similar = similarityPercent(
        removeStopWords($user),
        removeStopWords($question)
    );

    $lev = levenshteinPercent(
        removeStopWords($user),
        removeStopWords($question)
    );

    $overlap = keywordOverlap($user, $question);

    // Also inspect the admin-defined keywords.
    $keywordBonus = 0;
    if (trim($keywords) !== "") {
        $keywordList = array_filter(array_map("trim", explode(",", strtolower($keywords))));
        $userClean = removeStopWords($user);

        foreach ($keywordList as $keyword) {
            $keyword = removeStopWords($keyword);
            if ($keyword !== "" && stripos($userClean, $keyword) !== false) {
                $keywordBonus += 12;
            }
        }

        $keywordBonus = min(30, $keywordBonus);
    }

    return
        ($similar * 0.30) +
        ($lev * 0.20) +
        ($overlap * 0.40) +
        ($keywordBonus * 0.10);
}

$user = normalizeText($originalMessage);
$userClean = removeStopWords($user);

// ML classification of the student's topic.
$mlPrediction = mlPredict($originalMessage);
$mlIntent = $mlPrediction['intent'];
$mlConfidence = (float)$mlPrediction['confidence'];

$result = mysqli_query(
    $conn,
    "SELECT id, question, keywords FROM knowledge ORDER BY id DESC"
);

$ranked = [];

while ($row = mysqli_fetch_assoc($result)) {
    $question = trim($row["question"]);

    if ($question === "") {
        continue;
    }

    $candidateClean = removeStopWords($question);

    // Do not suggest the exact question the student just asked.
    if ($candidateClean !== "" && $candidateClean === $userClean) {
        continue;
    }

    $score = suggestionScore(
        $originalMessage,
        $question,
        $row["keywords"]
    );

    // ML-based recommendation boost: questions predicted to belong to the
    // same intent as the user's message are ranked higher.
    if ($mlIntent !== 'unknown' && $mlConfidence >= 0.30) {
        $candidatePrediction = mlPredict($question);
        if ($candidatePrediction['intent'] === $mlIntent) {
            $score += 25 * $mlConfidence;
        }
    }

    // Suggestions must have a meaningful connection to the topic.
    if ($score >= 35) {
        $ranked[] = [
            "question" => $question,
            "score" => $score,
            "id" => (int)$row["id"]
        ];
    }
}

usort($ranked, function($a, $b) {
    if ($a["score"] == $b["score"]) {
        return $b["id"] <=> $a["id"];
    }
    return $b["score"] <=> $a["score"];
});

// Remove near-duplicate suggestions.
$selected = [];

foreach ($ranked as $item) {
    $duplicate = false;

    foreach ($selected as $existing) {
        similar_text(
            normalizeText($item["question"]),
            normalizeText($existing),
            $percent
        );

        if ($percent >= 82) {
            $duplicate = true;
            break;
        }
    }

    if (!$duplicate) {
        $selected[] = $item["question"];
    }

    if (count($selected) >= 3) {
        break;
    }
}

// If the topic has no close database questions, show useful
// campus starters rather than leaving the suggestion area empty.
if (count($selected) === 0) {
    $fallback = [
        "How much is the tuition fee?",
        "Where can I pay my tuition fee?",
        "What are the admission requirements?"
    ];

    $selected = array_slice($fallback, 0, 3);
}

echo json_encode([
    "suggestions" => $selected
]);

exit;
?>