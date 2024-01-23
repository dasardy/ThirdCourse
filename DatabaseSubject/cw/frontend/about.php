<!DOCTYPE html>
<?php
    session_start();
    if (!isset($_SESSION['user_info']))
    {
        die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
    }
    if (isset ($_POST['exit']))
    {
        session_destroy();
        header("Location: ../index.php");
    }
    $text = "Работу выполнил: Студент группы АСУб-21-1, Ревякин Олег Андреевич."
?>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="../frontend/style.css">
    </head>
    <body>
        <table>
            <tr class = "topping">
                <th  colspan="2">
                   <div class="top_label">
                        Районная поликлиника
                   </div>
                </th>
            </tr>
            <tr>
                <td class = "menu">
                    <a class = "menu_btn" href="../frontend/mainpage.php">Главная</a>
                </td>
                <td rowspan="5">
                    <div class = "main_text">
                        <?php echo $text;?>
                    </div>
                    <div class = "me">
                        <img src="./imges/me.png" alt="My image" width= "220px">
                    </div>
                    <form action="admin.php" method="POST">
                        <input type='submit' class = "log_btn" name = 'exit' value='Выйти из учётной записи'><br>
                    </form>
                </td>
            </tr>
            <tr>
                <td class = "menu">
                    <span>
                        <a  class = "menu_btn" href="../frontend/task.php">Задание</a>
                    </span>
                </td>
            </tr>
            <tr>
                <td class = "menu">
                    <span>
                        <a class = "menu_btn" href="../frontend/admin.php">Администрирование</a>
                    </span>
                </td>
            </tr>
            <tr>
                <td class = "menu">
                    <span>
                        <a class = "menu_btn" href="../frontend/about.php">Обо мне</a>
                    </span>  
                </td>
            </tr>
            <tr>
                <td class = "button_area">
                </td>
            </tr>
        </table>
    </body>
</html>