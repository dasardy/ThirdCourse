<!DOCTYPE html>
<?php
    if (isset ($_POST['exit']))
    {
        session_destroy();
        header("Location: ../index.php");
    }
?>
<html class="no_body">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Успешно!</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body class="no_body">
        <div class = "l_page">
            <form  action="mainpage.php" method="POST">
                <div class = "c_text"> <label >Успешная авторизация!</label><br></div>
                <div class = "l_button">
                    <input class = "log_btn" type="submit" name = "continue" value="Продолжить">
                    <input class = "log_btn" type="submit" name = "exit" value="Вернуться назад">
                </div>
                
            </form>
        </div>
    </body>
</html>