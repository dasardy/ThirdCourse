<!DOCTYPE html>

<?php
    session_start();
    if (isset ($_POST['exit']))
    {
        session_destroy();
        header("Location: ../index.php");
    }
    // проверка информации пользователя в сессии
    if (isset($_SESSION['user_info']))
    {
        if (isset($_POST['back']))
        {
            $_SESSION["choose"] = 0;
        }
        $userinfo =  $_SESSION['user_info' ];
        $userame = $userinfo['user_name'];
        $user_job = $userinfo['jobtitle'];
        // создание списка таблиц
        $tablearr = array(
            'Areas' => 'Открыть список всех участков', 
            'Doctors' => 'Открыть список врачей',
            'Visits' => 'Открыть список посещений',
            'Patients' => 'Открыть список пациентов в участке');
        //список с названиями таблиц
        $tablearr_keys = array_keys($tablearr);
        // создание списка отчётов
        $reportarr = array(
            'AreaAndDoc'=> 'Показать отчёт "Список всех участков и врачей"',  
            'PatAtDoc' => 'Показать отчёт "Пациенты за опр. период у опр. врача"', 
            'PatAtDiag' => 'Показать отчёт "Пациенты с определённым диагнозом"', 
            'StaticTalon' => 'Выдать статистический талон');
        // названия отчётов
        $reportarr_keys = array_keys($reportarr);
        // указание полномочий для сотрудника поликлиники
        if ($user_job == 'Сотрудник поликлиники')
        {
            $item1 = $tablearr_keys['0'];
            $item2 = $reportarr_keys['0'];
            $available = array($item1 => $tablearr[$item1], $item2 => $reportarr[$item2]);
        }
        // указание полномочий для главного врачча поликлиники
        elseif ($user_job == 'Главный врач')
        {
            $item1 = $tablearr_keys['1'];
            $item2 = $reportarr_keys['1'];
            $available = array($item1 => $tablearr[$item1], $item2 => $reportarr[$item2]);
        }
        // указание полномочий для лечащего врача
        elseif ($user_job == 'Лечащий врач')
        {
            $item1 = $tablearr_keys['2'];
            $item2 = $reportarr_keys['1'];
            $item3 = $reportarr_keys['2'];
            $available = array($item1 => $tablearr[$item1], $item2 => $reportarr[$item2], 
            $item3 => $reportarr[$item3]);
        }
        // указание полномочий для работника регистратуры
        elseif ($user_job == 'Работник регистратуры')
        {
            $item1 = $tablearr_keys['3'];
            $item2 = $reportarr_keys['3'];
            $available = array($item1 => $tablearr[$item1], $item2 => $reportarr[$item2]);
        }
    }
    else
    {
        if(!isset($_POST["exit"]))
        {
            die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
        }
       
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

                <td rowspan="5">
                    <span>
                        <?php
                            foreach ($available as $op =>$value)
                            {
                                if (isset($_POST[$op]))
                                {   
                                    $_SESSION["choose"] = 1;
                                    $_SESSION["choose_op"] = $op;
                                }
                            }
                            if (!isset($_SESSION["choose"])||$_SESSION["choose"]!=1)
                            {
                            echo  '<form action="admin.php" method="POST">';
                                {
                                    echo '<div class = "main_text" >Ниже показаны все доступные для вашей учётной записи операции: </div>';
                                    foreach ($available as $op =>$value)
                                    {   
                                        echo "<input type='submit' class = 'log_btn' name='".$op."' value='".$value."'>";
                                        echo '<br>';
                                    }
                                }
                            echo '</form>';
                            }
                            else{
                                include("../backend/reports-and-tables/".$_SESSION['choose_op'].".php");
                            }
                            
                            if(isset($_SESSION['choose']))
                            {
                                if ($_SESSION['choose'] == 1)
                                {
                                    echo '<form  class="l_button"action="admin.php" method="POST">';
                                        echo "<input type='submit'  class='log_btn' name = 'back' value='Вернуться к выбору операции '><br>";
                                    echo '</form>';
                                }
                            }
                            
                        ?>
                        <form action="admin.php" class="l_button" method="POST">
                            <input type='submit'  class='log_btn' name = 'exit' value='Выйти из учётной записи'><br>
                        </form>
                    </span>
                </td>
            </tr>

            <tr>
                <td class = "menu">
                    <span>
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