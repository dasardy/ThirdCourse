<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Page</title>
    <link rel="stylesheet" type="text/css" href="../2MAIN/style.css">
</head>
<body>
    <?php
        session_start();
        if(isset($_SESSION["selected_answers"]))
        {$_SESSION["selected_answers"] = array();}
    ?>
    <div class = "name_app" >QUIZ APP</div>
    <img class = "index_image" src="../2MAIN/images/indexicon.svg">
    <form action="quiz.php" method="GET">
        <input class = "name_form" type="text" name="name_user" placeholder="Введите ваше имя сюда">
        <div>
            <button class="begin_btn">Начать тест</button>
        </div>
    </form>
</body>
</html>



