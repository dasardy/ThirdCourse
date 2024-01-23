<!DOCTYPE html>
<html>
<?php
session_set_cookie_params(0);
session_start();
// Получение значений из сессии
$id_questions = $_SESSION["questions"];
$id_answers = $_SESSION["answers"];
$dbparam = $_SESSION["conn_arr"];
$host = $dbparam[0];
$db = $dbparam[1];
$user = $dbparam[2];
$pass = $dbparam[3];

$conn = new mysqli($host, $user, $pass, $db); // Подключение к БД

$num_question = $_POST["num_question"] ?? 0; // Установка номера вопроса
if (!isset($_SESSION["selected_answers"])){
    $_SESSION["selected_answers"] = array();
}
//Обработка нажатых ответов
if (isset($_POST["selected_answer"])) {
    $inputType = isset($_POST["last_q"]) && $_POST["last_q"] ? 'checkbox' : 'radio';
    if (!isset($_SESSION["selected_answers"][$num_question])) {
        $_SESSION["selected_answers"][$num_question] = array();
    }
    $selectedAnswers = $_POST["selected_answer"];
    if ($inputType === 'radio') {
        $_SESSION["selected_answers"][$num_question] = array_map('htmlspecialchars', $selectedAnswers);
    } else {
        $_SESSION["selected_answers"][$num_question] = array_map('htmlspecialchars', $selectedAnswers);
    }
}
//Обработка нажатий кнопок для переключения
if (isset($_POST["next"])) {
    $num_question = min(count($id_questions) - 1, $num_question + 1);
} else if (isset($_POST["prev"]) || isset($_POST["finish"])) {
    $num_question = max(0, $num_question - 1);
}

// Получение вопроса под таким id из БД
$q_query = "SELECT question_text FROM questions WHERE question_id = " . $id_questions[$num_question];
$q_res = $conn->query($q_query);
$question = ($q_res->fetch_assoc())["question_text"];


$id_answers = $id_answers[$num_question];
$answer_array = array();
// Получение текстов ответов
foreach ($id_answers as $id) {
    $a_query = "SELECT answer_text FROM answers WHERE answer_id = $id";
    $a_res = $conn->query($a_query);
    $answer = ($a_res->fetch_assoc())["answer_text"];
    $answer_array[] = $answer;
}
// Обработка завершения теста
if (isset($_POST["finish"])) {
    header("Location: ../2MAIN/results.php");
    exit();
}
?>

<header>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <link rel="stylesheet" type="text/css" href="../2MAIN/style.css">
</header>
<body>
<form action="mainApp.php" method="POST">
        <?php
        echo "<div class='num_answer'>";
        echo "Номер вопроса: " . ($num_question + 1) . "<br>"; 
        echo "</div><br>";
        echo "<div class='text_question'>";
        echo $question."<br>";
        echo "</div>";
        echo "<div class = 'answers_div'>";
        $isLastQuestion = $num_question == count($id_questions) - 1;
        // Вывод чекбоксов в зависимости от их истории нажатий
        foreach ($answer_array as $answer) {
            $escapedAnswer = htmlspecialchars($answer);
            $inputType = $isLastQuestion ? 'checkbox' : 'radio';
            $isChecked = isset($_SESSION["selected_answers"][$num_question]) && in_array($escapedAnswer, $_SESSION["selected_answers"][$num_question]);
            $checkboxName = 'selected_answer[]';
            echo "<div>";
            echo '<input class="answers" type="' . $inputType . '" name="' . $checkboxName. '" value="' . $escapedAnswer . '" ' . ($isChecked ? 'checked' : '') . '>';
            echo '<label for="' . $escapedAnswer . '">' . $escapedAnswer . '</label>';
            echo "</div>";
            
        }
        echo "</div>"
        ?>
        <input type="hidden" name="num_question" value="<?php echo $num_question ?>">
        <input type="hidden" name="last_q" value="<?php echo $isLastQuestion?>">
        <div class = "a_btns">
            <input class="begin_btn" type="submit" name="prev" value="Вернуться назад">
            <input class="begin_btn" type="submit" name="next" value="Следующий вопрос">
        </div>
        <div class = "end_btn"> 
            <input class="begin_btn" type="submit" name="finish" value="Завершить тест">
        </div>
        
</form>
</body>

</html>


