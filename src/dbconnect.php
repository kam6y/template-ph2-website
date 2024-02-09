<?php
$dsn = 'mysql:host=db;dbname=posse;charset=utf8';
$user = 'root';
$password = 'root';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

// SQL ステートメント
$sql = "SELECT content FROM questions";


$questions = $dbh->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
$choices = $dbh->query("SELECT * FROM choices")->fetchAll(PDO::FETCH_ASSOC);

foreach ($questions as $qKey => $question) {
    $question["choices"] = [];
    foreach ($choices as $cKey => $choice) {
        if ($choice["question_id"] == $question["id"]) {
            $question["choices"][] = $choice;
        }
    }
    $questions[$qKey] = $question;
}