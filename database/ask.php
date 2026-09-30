<?php

include "database.php";
include "openrouter.php";
include "ml_common.php";

header("Content-Type: application/json");


session_start();

/*
|--------------------------------------------------------------------------
| Persistent Guest User ID
|--------------------------------------------------------------------------
| The session ID alone can be lost when the browser/session ends.
| A long-lived cookie lets ASTRA remember the same guest on later visits.
| This does NOT store the person's name in the browser; only a random ID.
*/

if (isset($_COOKIE["astra_user_id"]) && preg_match('/^student_[a-f0-9]{32}$/', $_COOKIE["astra_user_id"])) {

    $userId = $_COOKIE["astra_user_id"];

} else {

    $userId = "student_" . bin2hex(random_bytes(16));

    setcookie(
        "astra_user_id",
        $userId,
        [
            "expires" => time() + (60 * 60 * 24 * 365),
            "path" => "/",
            "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
            "httponly" => true,
            "samesite" => "Lax"
        ]
    );

}

$_SESSION["user_id"] = $userId;

$originalMessage = trim($_POST["message"] ?? "");

if ($originalMessage == "") {

    echo json_encode([
        "reply"=>"Please enter a question."
    ]);

    exit;
}

$message = strtolower($originalMessage);
$message = preg_replace('/[^a-z0-9\s]/', '', $message);
$message = preg_replace('/\s+/', ' ', $message);





/*
|--------------------------------------------------------------------------
| Learn User Name
|--------------------------------------------------------------------------
*/

$name = false;

/*
 * Understand natural ways a user can introduce their name:
 * "My name is Rolando"
 * "I am Rolando"
 * "I'm Rolando"
 * "Call me Rolando"
 * "This is Rolando"
 */
$namePatterns = [
    '/^\s*(?:my\s+name\s+is|my\s+name\'s|i\s+am|i\'m|im|call\s+me|this\s+is)\s+([a-z][a-z .\'-]{0,49})\s*[.!?]*\s*$/i',
    '/^\s*(?:hello|hi),?\s+i\s*\'?m\s+([a-z][a-z .\'-]{0,49})\s*[.!?]*\s*$/i'
];

foreach ($namePatterns as $pattern) {

    if (preg_match($pattern, $originalMessage, $match)) {

        $candidate = trim($match[1]);
        $candidate = preg_replace('/\s+/', ' ', $candidate);
        $candidate = trim($candidate, " .,!?\t\n\r");

        /*
         * Avoid treating a whole sentence as a name.
         * A normal name can contain spaces, apostrophes, and hyphens.
         */
        $nameWords = preg_split('/\s+/', $candidate);

        if (count($nameWords) <= 5 && strlen($candidate) >= 2) {
            $name = $candidate;
            break;
        }
    }
}

if ($name !== false) {

    saveMemory($conn, $userId, "name", $name);

    echo json_encode([
        "reply" => "Nice to meet you, " . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . ". I'll remember your name."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| ASTRA AI Greetings
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Detect User Intent
|--------------------------------------------------------------------------
*/

function hasWords($message, $words){

    foreach($words as $word){

        if(preg_match('/\b'.preg_quote($word,'/').'\b/i',$message)){
            return true;
        }

    }

    return false;

}


/*
|--------------------------------------------------------------------------
| Smart Text Processing
|--------------------------------------------------------------------------
*/

function normalizeText($text){

    $text = strtolower($text);

    $text = html_entity_decode($text);

    $text = preg_replace('/[^a-z0-9\s]/',' ',$text);

    $text = preg_replace('/\s+/',' ',trim($text));

    return $text;

}

/*
|--------------------------------------------------------------------------
| Save Memory
|--------------------------------------------------------------------------
*/

function saveMemory($conn,$userId,$key,$value){

    $stmt=mysqli_prepare($conn,"
        INSERT INTO user_memory
        (user_id,memory_key,memory_value)
        VALUES(?,?,?)
        ON DUPLICATE KEY UPDATE
        memory_value=VALUES(memory_value)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $userId,
        $key,
        $value
    );

    mysqli_stmt_execute($stmt);

}

/*
|--------------------------------------------------------------------------
| Get Memory
|--------------------------------------------------------------------------
*/

function getMemory($conn,$userId,$key){

    $stmt=mysqli_prepare($conn,"
        SELECT memory_value
        FROM user_memory
        WHERE user_id=?
        AND memory_key=?
        LIMIT 1
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $userId,
        $key
    );

    mysqli_stmt_execute($stmt);

    $result=mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result)>0){

        $row=mysqli_fetch_assoc($result);

        return $row["memory_value"];

    }

    return false;

}

/*
|--------------------------------------------------------------------------
| Stop Words
|--------------------------------------------------------------------------
*/

function removeStopWords($text){

    $stopWords = [

        "who","what","where","when","why","how",

        "is","are","was","were",

        "the","a","an",

        "can","could","would","will",

        "please",

        "tell","show","give","know",

        "about","me","to","for","of",

        "do","does","did",

        "ako","ba","nga","po","naman","lang",

        "si","ang","yung","iyan"

    ];

    $words = explode(" ",normalizeText($text));

    $filtered=[];

    foreach($words as $word){

        if(!in_array($word,$stopWords)){

            $filtered[]=$word;

        }

    }

    return implode(" ",$filtered);

}

/*
|--------------------------------------------------------------------------
| Similar Text
|--------------------------------------------------------------------------
*/

function similarityPercent($a,$b){

    similar_text($a,$b,$percent);

    return $percent;

}

/*
|--------------------------------------------------------------------------
| Levenshtein Score
|--------------------------------------------------------------------------
*/

function levenshteinPercent($a,$b){

    $distance = levenshtein($a,$b);

    $max = max(strlen($a),strlen($b));

    if($max==0){

        return 100;

    }

    return (1-($distance/$max))*100;

}

/*
|--------------------------------------------------------------------------
| Keyword Overlap
|--------------------------------------------------------------------------
*/

function keywordScore($user,$database){

    $userWords=array_unique(explode(" ",removeStopWords($user)));

    $dbWords=array_unique(explode(" ",removeStopWords($database)));

    $common=array_intersect($userWords,$dbWords);

    if(count($dbWords)==0){

        return 0;

    }

    return (count($common)/count($dbWords))*100;

}

/*
|--------------------------------------------------------------------------
| Final Smart Score
|--------------------------------------------------------------------------
*/

function smartScore($user,$database){

    $user = removeStopWords($user);

    $database = removeStopWords($database);

    $similar = similarityPercent($user,$database);

    $lev = levenshteinPercent($user,$database);

    $keyword = keywordScore($user,$database);

    return

        ($similar*0.35)+
        ($lev*0.35)+
        ($keyword*0.30);

}

/*
|--------------------------------------------------------------------------
| Friendly Conversation
|--------------------------------------------------------------------------
*/

$intents = [

"greeting"=>[
    "keywords"=>[
        "hello","hi","hey",
    ],

    "responses"=>[
        "Hello! I'm ASTRA. How may I help you today?",
        "Hi! Welcome to Core Gateway College.",
        "Hello! What can I do for you today?",
        "Hi there! Feel free to ask me anything about Core Gateway College."
    ]
],

"thanks"=>[
    "keywords"=>[
        "thanks",
        "thank you",
        "thankyou",
        "salamat"
    ],

    "responses"=>[
        "You're welcome!",
        "Happy to help!",
        "It's my pleasure!",
        "Anytime! Feel free to ask another question."
    ]
],

"ok"=>[
    "keywords"=>[
        "ok",
        "okay",
        "oke",
        "alright",
        "sige",
        "cge"
    ],

    "responses"=>[
        "Alright!",
        "Okay! What would you like to know next?",
        "Got it!",
        "Sure!",
        "No problem!"
    ]
],

"yes"=>[
    "keywords"=>[
        "yes",
        "yeah",
        "yep",
        "opo",
        "oo"
    ],

    "responses"=>[
        "Great!",
        "Awesome!",
        "Perfect!",
        "Let's continue!"
    ]
],

"no"=>[
    "keywords"=>[
        "no",
        "nope",
        "hindi"
    ],

    "responses"=>[
        "No problem.",
        "That's okay!",
        "Alright!"
    ]
],

"bye"=>[
    "keywords"=>[
        "bye",
        "goodbye",
        "see you",
        "see you later",
        "paalam",
        "babye"
    ],

    "responses"=>[
        "Goodbye!  Have a wonderful day!",
        "See you again! Stay safe.",
        "Take care!",
        "See you soon!"
    ]
],

"compliment"=>[
    "keywords"=>[
        "nice",
        "good",
        "great",
        "awesome",
        "amazing",
        "cool"
    ],

    "responses"=>[
        "Thank you!",
        "I'm glad you think so!",
        "That means a lot. Thank you!",
        "I'm happy I could help!"
    ]
],

"love"=>[
    "keywords"=>[
        "love you",
        "i love you"
    ],

    "responses"=>[
        "Thank you! I'm always here to help you.",
        "Aww! Thank you very much. ",
        "That made my day!"
    ]
],

"handsome"=>[
"keywords"=>[
"ang pogi ko",
"gwapo ba ako",
"gwapo ako",
"pogi ako",
"handsome ba ako",
"am i handsome"
],

"responses"=>[
"Yes! You're very handsome!",
"Of course! Confidence looks good on you.",
"I think you're awesome!",
"Definitely! Keep smiling!"
]
],

"beautiful"=>[
"keywords"=>[
"maganda ba ako",
"beautiful ba ako",
"pretty ba ako",
"cute ba ako"
],

"responses"=>[
"Yes! You're beautiful!",
"Of course! Keep being yourself.",
"You look great!",
"Absolutely!"
]
],

"joke"=>[
"keywords"=>[
"tell me a joke",
"joke",
"patawa",
"make me laugh"
],

"responses"=>[
"Why don't programmers like nature? Because it has too many bugs!",
"I would tell you a UDP joke, but you might not get it.",
"What do computers eat? Chips!"
]
],

"sad"=>[
"keywords"=>[
"malungkot ako",
"i am sad",
"sad",
"naiiyak ako"
],

"responses"=>[
"I'm sorry you're feeling that way. I hope things get better soon.",
"I'm here if you want someone to talk to.",
"Take a short break and remember that every day is a new opportunity."
]
],

"howareyou"=>[
"keywords"=>[
"how are you",
"kamusta",
"kumusta",
"how r u",
"are you okay",
],

"responses"=>[
"I'm doing great! Thank you for asking.",
"I'm good! How about you?",
"I'm always ready to help!"
]
],

"name"=>[
"keywords"=>[
"what is your name",
"who are you",
"your name",
"ano pangalan mo",
"sino ka"
],

"responses"=>[
"I'm ASTRA, your Smart Campus Assistant of Core Gateway College Inc.",
"I'm ASTRA. I'm here to answer your questions and help you around the campus.",
"My name is ASTRA. Nice to meet you!"
]
],

"creator"=>[
"keywords"=>[
"who made you",
"who created you",
"sino gumawa sayo",
"creator mo",
"developer mo"
],

"responses"=>[
"I was developed to assist students of Core Gateway College Inc.",
"I was created as the Smart Campus Assistant to help students and visitors.",
"My developers designed me to answer questions and provide campus information."
]
],

"age"=>[
"keywords"=>[
"how old are you",
"ilang taon ka",
"age mo"
],

"responses"=>[
"I don't have an age like humans, but I'm always learning.",
"I'm an AI, so I don't celebrate birthdays."
]
],


"morning"=>[
"keywords"=>[
"good morning",
"magandang umaga"
],

"responses"=>[
"Good morning! I hope you have a wonderful day!",
"Good morning! How can I help you today?"
]
],


"afternoon"=>[
"keywords"=>[
"good afternoon",
"magandang hapon"
],

"responses"=>[
"Good afternoon! How may I help you?",
"Good afternoon! Hope you're having a great day."
]
],


"evening"=>[
"keywords"=>[
"good evening",
"magandang gabi"
],

"responses"=>[
"Good evening! I'm here whenever you need me.",
"Good evening! What can I do for you?"
]
],


"night"=>[
"keywords"=>[
"good night",
"matutulog na ako",
"goodnight"
],

"responses"=>[
"Good night! Sleep well and take care.",
"Sweet dreams! See you tomorrow."
]
],


"hungry"=>[
"keywords"=>[
"kumain ka na",
"have you eaten",
"gutom ka ba"
],

"responses"=>[
"I don't eat, but thank you for asking!",
"If I could eat, I'd probably choose pizza!"
]
],

"crush"=>[
"keywords"=>[
"crush mo ako",
"love mo ba ako",
"gusto mo ba ako"
],

"responses"=>[
"Haha! I'm here to be your friendly campus assistant.",
"I appreciate you, but my job is helping students.",
"Let's focus on helping you today!"
]
],

"bored"=>[
"keywords"=>[
"bored ako",
"wala akong magawa",
"boring"
],

"responses"=>[
"Why not ask me something about Core Gateway College?",
"We can chat! Ask me anything.",
"Let's make your day productive!"
]
],

"stress"=>[
"keywords"=>[
"stress ako",
"pagod ako",
"tired",
"kapagod"
],

"responses"=>[
"I'm sorry you're feeling that way. I hope you can get some rest.",
"Take a short break. You've got this!",
"Remember to take care of yourself."
]
],

"funny"=>[
"keywords"=>[
"haha",
"hahaha",
"lol",
"lmao"
],

"responses"=>[
"😂 Glad you're having fun!",
"Haha! 😄",
"I'm happy that made you smile!"
]
],

"welcome"=>[
"keywords"=>[
"thank you so much",
"maraming salamat",
"thanks a lot"
],

"responses"=>[
"You're very welcome!",
"Always happy to help!",
"My pleasure!"
]
]
];

/*
|--------------------------------------------------------------------------
| Detect Greeting (Don't Reply Yet)
|--------------------------------------------------------------------------
*/

$isGreetingOnly = false;
$greeting = "";

foreach($intents["greeting"]["keywords"] as $keyword){

    if($message == strtolower($keyword)){

        $isGreetingOnly = true;

        $greeting = $intents["greeting"]["responses"][
            array_rand($intents["greeting"]["responses"])
        ];

        break;
    }

}
/*
|--------------------------------------------------------------------------
| ASTRA AI - Time and Date
|--------------------------------------------------------------------------
*/

date_default_timezone_set("Asia/Manila");

$currentTime  = date("g:i A");
$currentDate  = date("l, F j, Y");
$currentDay   = date("l");
$currentMonth = date("F");
$currentYear  = date("Y");

// ================= TIME =================

$timeWords = [
    "time",
    "oras",
    "clock"
];

foreach($timeWords as $word){

    if(preg_match('/\b' . preg_quote($word,'/') . '\b/i',$message)){

$reply = "";

if($greeting != ""){
    $reply .= $greeting . " ";
}

$reply .= "The current time is ".$currentTime.".";

echo json_encode([
    "reply"=>$reply
]);

exit;

    }

}

// ================= DATE =================

$dateWords = [
    "date",
    "today",
    "petsa"
];

foreach($dateWords as $word){

    if(preg_match('/\b' . preg_quote($word,'/') . '\b/i',$message)){

$reply="";

if($greeting!=""){
    $reply.=$greeting." ";
}

$reply.="Today is ".$currentDate.".";

echo json_encode([
"reply"=>$reply
]);

exit;

    }

}

// ================= DAY =================

$dayWords = [
    "day",
    "araw"
];

foreach($dayWords as $word){

    if(preg_match('/\b' . preg_quote($word,'/') . '\b/i',$message)){

        echo json_encode([
            "reply"=>"Today is ".$currentDay."."
        ]);

        exit;

    }

}

// ================= MONTH =================

$monthWords = [
    "month",
    "buwan"
];

foreach($monthWords as $word){

    if(preg_match('/\b' . preg_quote($word,'/') . '\b/i',$message)){

        echo json_encode([
            "reply"=>"The current month is ".$currentMonth."."
        ]);

        exit;

    }

}

// ================= YEAR =================

$yearWords = [
    "year",
    "taon"
];

foreach($yearWords as $word){

    if(preg_match('/\b' . preg_quote($word,'/') . '\b/i',$message)){

        echo json_encode([
            "reply"=>"The current year is ".$currentYear."."
        ]);

        exit;

    }

}


/*
|--------------------------------------------------------------------------
| Greeting Only
|--------------------------------------------------------------------------
*/

if($isGreetingOnly){

    echo json_encode([
        "reply" => $greeting
    ]);

    exit;

}



/*
|--------------------------------------------------------------------------
| Learn Student Information
|--------------------------------------------------------------------------
*/

$patterns=[

[
"key"=>"name",
"patterns"=>[
"/my name is (.+)/i",
"/i am (.+)/i",
"/i'm (.+)/i",
"/call me (.+)/i",
"/you can call me (.+)/i",
"/ako si (.+)/i"
]
],

[
"key"=>"course",
"patterns"=>[
"/i am a (.+) student/i",
"/i am taking (.+)/i",
"/my course is (.+)/i",
"/i study (.+)/i"
]
],

[
"key"=>"favorite color",
"patterns"=>[
"/my favorite color is (.+)/i"
]
],

[
"key"=>"birthday",
"patterns"=>[
"/my birthday is (.+)/i"
]
],

[
"key"=>"address",
"patterns"=>[
"/i live in (.+)/i"
]
],

[
"key"=>"age",
"patterns"=>[
"/i am ([0-9]+) years old/i"
]
]

];

foreach($patterns as $memory){

    foreach($memory["patterns"] as $pattern){

        if(preg_match($pattern,$originalMessage,$match)){

            $value=trim($match[1]);

            saveMemory(
                $conn,
                $userId,
                $memory["key"],
                $value
            );

            echo json_encode([

                "reply"=>"Okay! I'll remember that your ".$memory["key"]." is ".$value."."

            ]);

            exit;

        }

    }

}


/*
|--------------------------------------------------------------------------
| ASTRA Machine Learning Intent Prediction
|--------------------------------------------------------------------------
| A trained Multinomial Naive Bayes model predicts the student's intent.
| The prediction is then used to improve knowledge-base matching.
*/
$mlPrediction = mlPredict($originalMessage);
$mlIntent = $mlPrediction['intent'];
$mlConfidence = (float)$mlPrediction['confidence'];

// Keep an evaluation record so the admin can measure model behavior and
// later expand the training dataset with real student questions.
if ($mlIntent !== 'unknown') {
    $mlLog = mysqli_prepare($conn, "INSERT INTO ml_interactions (user_id, question, predicted_intent, confidence) VALUES (?, ?, ?, ?)");
    if ($mlLog) {
        mysqli_stmt_bind_param($mlLog, "sssd", $userId, $originalMessage, $mlIntent, $mlConfidence);
        mysqli_stmt_execute($mlLog);
        mysqli_stmt_close($mlLog);
    }
}


/*
|--------------------------------------------------------------------------
| Learned Question/Answer Memory
|--------------------------------------------------------------------------
| If a question is not found in the official knowledge table, ASTRA checks
| previous AI-generated answers. This is retrieval-based learning: the ML
| classifier identifies the topic, while similarity matching retrieves the
| previous answer. The answer is NOT generated by Naive Bayes.
*/
function learnedQuestionScore($user, $candidate) {

    $userClean = removeStopWords(normalizeText($user));
    $candidateClean = removeStopWords(normalizeText($candidate));

    if ($userClean === '' || $candidateClean === '') {
        return 0;
    }

    $similar = similarityPercent($userClean, $candidateClean);
    $lev = levenshteinPercent($userClean, $candidateClean);

    $userWords = array_unique(array_filter(
        explode(' ', $userClean),
        function($w) { return strlen($w) >= 3; }
    ));

    $candidateWords = array_unique(array_filter(
        explode(' ', $candidateClean),
        function($w) { return strlen($w) >= 3; }
    ));

    $common = count(array_intersect($userWords, $candidateWords));
    $overlap = count($userWords) > 0
        ? ($common / count($userWords)) * 100
        : 0;

    return ($similar * 0.45) + ($lev * 0.20) + ($overlap * 0.35);
}

function findLearnedAnswer($conn, $question, $mlIntent, $mlConfidence) {

    $result = mysqli_query(
        $conn,
        "SELECT id, question, answer, intent
         FROM learned_qa
         WHERE status='active'
         ORDER BY id DESC
         LIMIT 500"
    );

    if (!$result) {
        return false;
    }

    $best = false;
    $bestScore = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        $score = learnedQuestionScore($question, $row['question']);

        // The classifier helps retrieve questions from the same topic.
        if (
            $mlIntent !== 'unknown' &&
            $mlConfidence >= 0.30 &&
            !empty($row['intent']) &&
            $row['intent'] === $mlIntent
        ) {
            $score += 12 * $mlConfidence;
        }

        if ($score > $bestScore) {
            $bestScore = $score;
            $best = $row;
        }
    }

    // High threshold prevents unrelated previous answers from being reused.
    if ($best && $bestScore >= 78) {

        $id = (int)$best['id'];

        mysqli_query(
            $conn,
            "UPDATE learned_qa
             SET use_count = use_count + 1
             WHERE id = $id"
        );

        return [
            'answer' => $best['answer'],
            'question' => $best['question'],
            'score' => round($bestScore, 2)
        ];
    }

    return false;
}

function saveLearnedAnswer($conn, $question, $answer, $intent, $confidence) {

    if (trim($question) === '' || trim($answer) === '') {
        return;
    }

    // Avoid storing the same question repeatedly.
    $check = mysqli_prepare(
        $conn,
        "SELECT id FROM learned_qa WHERE question=? LIMIT 1"
    );

    if ($check) {
        mysqli_stmt_bind_param($check, "s", $question);
        mysqli_stmt_execute($check);
        $result = mysqli_stmt_get_result($check);

        if ($result && mysqli_num_rows($result) > 0) {
            mysqli_stmt_close($check);
            return;
        }

        mysqli_stmt_close($check);
    }

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO learned_qa
         (question, answer, intent, confidence, status)
         VALUES (?, ?, ?, ?, 'active')"
    );

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssd",
            $question,
            $answer,
            $intent,
            $confidence
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

/*
|--------------------------------------------------------------------------
| CGCI Personnel Lookup
|--------------------------------------------------------------------------
| If a student asks about an instructor, dean, staff member, or a specific
| employee, return the matching personnel record so the UI can show the
| person's name, position, department, and photo.
*/
function personnelSearchTerms($text) {
    $text = normalizeText($text);
    $text = preg_replace('/\\b(what|who|where|which|tell|show|give|me|the|a|an|is|are|was|were|of|about|for|please|can|could|would|you|do|does|did|name|picture|photo|image|person|people|employee|personnel|staff|member|members|instructor|instructors|teacher|teachers|faculty|professor|professors|dean|deans|head|heads|director|directors|coordinator|coordinators|registrar|guidance|nurse|nurses|librarian|librarians|secretary|secretaries|clerk|clerks|assistant|assistants|who|is|from|department|office)\\b/i', ' ', $text);
    $text = preg_replace('/\\s+/', ' ', trim($text));
    return $text;
}

function findPersonnelMatches($conn, $message) {
    $original = normalizeText($message);
    $search = personnelSearchTerms($message);

    $positionWords = [
        'dean','instructor','teacher','faculty','professor','staff','employee',
        'director','coordinator','registrar','guidance','nurse','librarian',
        'secretary','clerk','assistant','head','program chair','chair'
    ];

    $isPersonnelQuestion = false;
    foreach ($positionWords as $word) {
        if (preg_match('/\\b'.preg_quote($word,'/').'s?\\b/i', $original)) {
            $isPersonnelQuestion = true;
            break;
        }
    }

    // A specific employee name can also make this a personnel question.
    $result = mysqli_query($conn, "SELECT p.id,p.name,p.position,p.image,d.name AS department FROM personnel p LEFT JOIN departments d ON d.id=p.department_id ORDER BY p.sort_order ASC,p.name ASC");
    if (!$result) return [];

    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $nameClean = normalizeText($row['name']);
        $nameParts = array_values(array_filter(explode(' ', $nameClean)));

        $nameMatched = false;
        if ($search !== '') {
            $nameMatched = (strpos($nameClean, $search) !== false);
            if (!$nameMatched && count($nameParts) >= 2) {
                $matchedParts = 0;
                foreach ($nameParts as $part) {
                    if (strlen($part) >= 2 && preg_match('/\\b'.preg_quote($part,'/').'\\b/i', $search)) {
                        $matchedParts++;
                    }
                }
                $nameMatched = $matchedParts >= 2;
            }
        }

        $positionClean = normalizeText($row['position']);
        $positionMatched = false;
        foreach ($positionWords as $word) {
            if (preg_match('/\\b'.preg_quote($word,'/').'s?\\b/i', $original) && strpos($positionClean, normalizeText($word)) !== false) {
                $positionMatched = true;
                break;
            }
        }

        if ($nameMatched || $positionMatched) {
            $row['_nameMatched'] = $nameMatched;
            $row['_positionMatched'] = $positionMatched;
            $rows[] = $row;
            $isPersonnelQuestion = true;
        }
    }

    if (!$isPersonnelQuestion) return [];

    // For broad position questions, return all matching records. For a
    // specific name, put the strongest name match first.
    usort($rows, function($a, $b) {
        $sa = (($a['_nameMatched'] ?? false) ? 100 : 0) + (($a['_positionMatched'] ?? false) ? 20 : 0);
        $sb = (($b['_nameMatched'] ?? false) ? 100 : 0) + (($b['_positionMatched'] ?? false) ? 20 : 0);
        if ($sa !== $sb) return $sb <=> $sa;
        return strcasecmp($a['name'], $b['name']);
    });

    return $rows;
}

/*
 * If the personnel name is also present in the official knowledge table,
 * use the knowledge-base answer as the text response and attach the
 * personnel record so the UI can display the person's photo. This keeps
 * the written answer synchronized with the school's official knowledge.
 */
function findPersonnelKnowledgeAnswer($conn, $message, $personnelMatches) {

    if (empty($personnelMatches)) {
        return false;
    }

    $result = mysqli_query($conn, "SELECT id,question,keywords,answer FROM knowledge");

    if (!$result) {
        return false;
    }

    $bestAnswer = false;
    $bestScore = 0;

    while ($row = mysqli_fetch_assoc($result)) {

        $candidateText = normalizeText(
            ($row['question'] ?? '') . ' ' . ($row['keywords'] ?? '')
        );

        $score = smartScore($message, $row['question'] ?? '');

        foreach ($personnelMatches as $person) {

            $name = normalizeText($person['name'] ?? '');
            $position = normalizeText($person['position'] ?? '');
            $department = normalizeText($person['department'] ?? '');

            // A knowledge entry containing the exact personnel name gets
            // a strong boost. This is the important link between the two
            // data sources.
            if ($name !== '' && strpos($candidateText, $name) !== false) {
                $score += 70;
            }

            if ($position !== '' && strpos($candidateText, $position) !== false) {
                $score += 10;
            }

            if ($department !== '' && strpos($candidateText, $department) !== false) {
                $score += 8;
            }
        }

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestAnswer = $row;
        }
    }

    // Require a meaningful personnel-name match. The 70-point name boost
    // prevents a generic knowledge answer from being returned accidentally.
    if ($bestAnswer && $bestScore >= 70) {
        return [
            'answer' => $bestAnswer['answer'],
            'question' => $bestAnswer['question'],
            'score' => round($bestScore, 2)
        ];
    }

    return false;
}

$personnelMatches = findPersonnelMatches($conn, $originalMessage);

if (!empty($personnelMatches)) {
    $cards = [];
    foreach ($personnelMatches as $person) {
        $file = basename((string)$person['image']);
        $cards[] = [
            'id' => (int)$person['id'],
            'name' => $person['name'],
            'position' => $person['position'],
            'department' => $person['department'] ?? '',
            'image' => 'assets/personnel/' . rawurlencode($file)
        ];
    }

    // If the same person's name exists in the official knowledge database,
    // return that knowledge answer together with the personnel photo.
    $personnelKnowledge = findPersonnelKnowledgeAnswer(
        $conn,
        $originalMessage,
        $personnelMatches
    );

    $count = count($cards);
    if ($personnelKnowledge !== false) {
        $reply = $personnelKnowledge['answer'];
        $source = 'knowledge_personnel';
    } else {
        $reply = $count === 1
            ? 'Here is the CGCI personnel record I found:'
            : 'Here are the CGCI personnel records that match your question:';
        $source = 'personnel_lookup';
    }

    echo json_encode([
        'reply' => $reply,
        'source' => $source,
        'personnel' => $cards,
        'knowledge_question' => $personnelKnowledge['question'] ?? null
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/*
|--------------------------------------------------------------------------
| Check previously learned AI answers
|--------------------------------------------------------------------------
| This runs before OpenRouter. It only returns a learned answer when the
| similarity is high enough to reduce accidental reuse of unrelated answers.
*/
$learnedMatch = findLearnedAnswer(
    $conn,
    $originalMessage,
    $mlIntent,
    $mlConfidence
);

if ($learnedMatch !== false) {

    echo json_encode([
        "reply" => $learnedMatch["answer"],
        "source" => "learned_memory",
        "matched_question" => $learnedMatch["question"]
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| ASTRA Smart Knowledge Search + ML ranking
|--------------------------------------------------------------------------
*/

$sql = "SELECT id,question,keywords,answer FROM knowledge";

$result = mysqli_query($conn,$sql);

$bestAnswer = "";
$bestQuestion = "";
$highestScore = 0;

while($row = mysqli_fetch_assoc($result)){

    $question = normalizeText($row["question"]);

    $answer = $row["answer"];

    $keywords = strtolower($row["keywords"]);

    /*
    ------------------------------------
    Exact Question
    ------------------------------------
    */

    if(removeStopWords($message) == removeStopWords($question)){

        echo json_encode([
            "reply"=>$answer
        ]);

        exit;

    }

    /*
    ------------------------------------
    Calculate Question Score
    ------------------------------------
    */

    $questionScore = smartScore(
        $message,
        $question
    );

    /*
    ------------------------------------
    Keyword Score
    ------------------------------------
    */

    $keywordScore = 0;

    if(trim($keywords)!=""){

        $keywordList = explode(",",$keywords);

        $match = 0;

        foreach($keywordList as $keyword){

            $keyword = removeStopWords($keyword);

            if($keyword==""){

                continue;

            }

            if(stripos(removeStopWords($message),$keyword)!==false){

                $match++;

            }

        }

        if(count($keywordList)>0){

            $keywordScore =

            ($match/count($keywordList))*100;

        }

    }

    /*
    ------------------------------------
    Final Score
    ------------------------------------
    */

    $finalScore =

        ($questionScore*0.75)+
        ($keywordScore*0.25);

    /*
    ------------------------------------
    Machine Learning Intent Boost
    ------------------------------------
    Classify the stored knowledge question too. If the model predicts the
    same intent as the student, give that answer a ranking boost.
    ------------------------------------
    */
    if ($mlIntent !== 'unknown' && $mlConfidence >= 0.30) {
        $candidatePrediction = mlPredict($row["question"]);
        if ($candidatePrediction['intent'] === $mlIntent) {
            $finalScore += 18 * $mlConfidence;
        }
    }

    /*
    ------------------------------------
    Save Best Match
    ------------------------------------
    */

    if($finalScore>$highestScore){

        $highestScore=$finalScore;

        $bestAnswer=$answer;

        $bestQuestion=$question;

    }

}

/*
|--------------------------------------------------------------------------
| Accept Smart Match
|--------------------------------------------------------------------------
*/

if($highestScore>=72){

    echo json_encode([

        "reply"=>$bestAnswer

    ]);

    exit;

}

   

   /*
|/*
|--------------------------------------------------------------------------
| Friendly Conversation
|--------------------------------------------------------------------------
*/

foreach($intents as $name=>$intent){

    foreach($intent["keywords"] as $keyword){

        $score = smartScore($message,$keyword);

        if($score >= 88){

            echo json_encode([

                "reply"=>$intent["responses"][
                    array_rand($intent["responses"])
                ]

            ]);

            exit;

        }

    }

}
    




/*
|--------------------------------------------------------------------------
| Recall User Name
|--------------------------------------------------------------------------
*/

$nameQuestions = [
    "what is my name",
    "what's my name",
    "whats my name",
    "tell me my name",
    "who am i",
    "who am i again",
    "do you remember my name",
    "do u remember my name",
    "remember my name",
    "my name again",
    "what did i tell you my name was",
    "what did i say my name was",
    "can you remember my name"
];

foreach ($nameQuestions as $q) {

    if (smartScore($message, $q) >= 85) {

        $savedName = getMemory($conn, $userId, "name");

        if ($savedName) {

            echo json_encode([
                "reply" => "Your name is " . htmlspecialchars($savedName, ENT_QUOTES, "UTF-8") . ". I remember!"
            ]);

        } else {

            echo json_encode([
                "reply" => "I don't know your name yet. Tell me by saying \"My name is Rolando.\""
            ]);

        }

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Recall Student Memory
|--------------------------------------------------------------------------
*/

$questions=[

[
"key"=>"name",
"ask"=>[
"what is my name",
"whats my name",
"tell me my name",
"who am i"
]
],

[
"key"=>"course",
"ask"=>[
"what is my course",
"what course am i taking",
"what do i study"
]
],

[
"key"=>"favorite color",
"ask"=>[
"what is my favorite color"
]
],

[
"key"=>"birthday",
"ask"=>[
"when is my birthday"
]
],

[
"key"=>"address",
"ask"=>[
"where do i live"
]
],

[
"key"=>"age",
"ask"=>[
"how old am i"
]
]

];

foreach($questions as $memory){

    foreach($memory["ask"] as $question){

        if(smartScore($message,$question)>=90){

            $value=getMemory(
                $conn,
                $userId,
                $memory["key"]
            );

            if($value){

                echo json_encode([

                    "reply"=>"Your ".$memory["key"]." is ".$value."."

                ]);

            }else{

                echo json_encode([

                    "reply"=>"You haven't told me your ".$memory["key"]." yet."

                ]);

            }

            exit;

        }

    }

}

   /*
|--------------------------------------------------------------------------
| Ask OpenRouter AI
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| OpenRouter AI
|--------------------------------------------------------------------------
*/

$aiReply = askOpenRouter($originalMessage);

if ($aiReply !== false && trim($aiReply) != "") {

    /*
     * Save the successful AI answer as learned Q&A.
     * A future similar question can retrieve this answer without calling
     * OpenRouter again.
     */
    saveLearnedAnswer(
        $conn,
        $originalMessage,
        trim($aiReply),
        $mlIntent,
        $mlConfidence
    );

    echo json_encode([
        "reply" => $aiReply,
        "source" => "openrouter_learned"
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Save unanswered question only if AI also fails
|--------------------------------------------------------------------------
*/

$safeMessage = mysqli_real_escape_string($conn, $originalMessage);

mysqli_query(
    $conn,
    "INSERT INTO unanswered_questions(question,status)
     VALUES('$safeMessage','pending')"
);

echo json_encode([
    "reply" => "I'm sorry, I'm having trouble answering right now. Your question has been sent to the administrator."
]);

exit;
