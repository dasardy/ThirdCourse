<?php
ini_set('session.gc_maxlifetime', 128 * 1024 * 1024);
session_start();

$host = 'localhost';
$db = 'DB2023_cucumber';
$user = 'DB2023_cucumber';
$pass = 'DB2023_cucumber';
$conn = new mysqli($host, $user, $pass, $db);
$name = $_GET["name_user"];
//формирование массива с id всех 6 вопросов
$sql1 = "SELECT q.question_id
        FROM questions q
        JOIN answers a ON q.question_id = a.question_id
        GROUP BY q.question_id
        HAVING COUNT(*) < 5
        ORDER BY RAND()
        LIMIT 5";
$result = $conn->query($sql1);

$sql2 = "SELECT q.question_id
        FROM questions q
        JOIN answers a ON q.question_id = a.question_id
        GROUP BY q.question_id
        HAVING COUNT(*) >= 5
        ORDER BY RAND()
        LIMIT 1";
$result2 = $conn->query($sql2);

$questionsArray = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questionsArray[] = $row['question_id'];
    }
    //добавление вопроса с несколькими ответами в конец массива
    $row = $result2->fetch_assoc();
    array_push($questionsArray, $row["question_id"]);
    print_r($questionsArray);
}

$all_answerArray = array();
//формирование массива с ответами на каждый вопрос
foreach ($questionsArray as $question)
{
    $one_answerArray= array();
    $ans_query = "SELECT answer_id FROM answers WHERE question_id = $question ORDER BY RAND()";
    $ans_res = $conn->query($ans_query);
    while ($row = $ans_res->fetch_assoc()) 
    {
        $one_answerArray[] = $row['answer_id'];
    }
    array_push($all_answerArray, $one_answerArray);
}
$_SESSION["conn_arr"] = array($host, $user, $pass, $db);
$_SESSION["questions"] = $questionsArray;
$_SESSION["answers"] = $all_answerArray;
$_SESSION["name"] = $name;
header("Location: ../2MAIN/mainApp.php");
?>
