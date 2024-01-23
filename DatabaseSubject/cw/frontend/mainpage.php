<!DOCTYPE html>
<?php
    session_start();
    
    if (!isset($_SESSION['user_info']))
    {
        die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
    }
    $user_name = $_SESSION['user_info']['user_name'];
    $text = 'Здравствуйте, '.$user_name.'.<br>
    С левой стороны страницы вы можете заметить несколько разделов. Их предназначение будет изложено ниже.<br>
    1. В разделе "Задание" объясняются поставленные задачи, согласно выбранному варианту.<br>
    2. В разделе "Администрирование" выполняется управление базой данных посредством кнопок и различных форм,<br> с учётом того, 
    какой пользователь выполнил авторизацию в приложении.<br>
    3. В разделе "Обо мне" указана информация об авторе данного приложения.<br>
    Также, с помощью кнопки "Выйти из учётной записи" вы можете вернуться на стартовую страницу.';
    if (isset ($_POST['exit']))
    {
        session_destroy();
        header("Location: ../index.php");
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
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
                <td class = "mainarea" rowspan ="5">
                    <div class = "main_text">
                        <?php echo $text;?>
                    </div>
                    <form action="admin.php" method="POST">
                        <input type='submit' class = "log_btn" name = 'exit' value='Выйти из учётной записи'><br>
                    </form>
                </td>
            </tr>
            <tr>
                <td class = "menu">
                    <span >
                        <a class = "menu_btn" href="../frontend/task.php">Задание</a>
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