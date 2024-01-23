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
    $text = "Мною был выбран 6 вариант под названием «Районная поликлиника».<br>
    Целью данной ИС является хранение данных о посещениях пациентами<br>
    поликлиники с целью дальнейшей статистической обработки, или иными<br>
    словами, регистрация статистических талонов. Врач характеризуется<br>
    следующими свойствами: табельный номер, ФИО, специальность, категория,<br>
    ставка. За каждым врачом закреплен определенный участок. Пациент имеет<br>
    номер медицинской карты, номер медицинского страхового полиса, ФИО,<br>
    пол, дату рождения, адрес (улица-дом (корпус) определяют принадлежность<br>
    к определенному участку).<br>
    Следует хранить информацию о посещениях пациентами врачей (номер<br>
    талона, дата визита, цель посещения, статус – первичный, повторный,<br>
    диагноз). Следует отдельно хранить диагнозы (наименование), цели<br>
    посещения (профосмотр, медосмотр, консультация, лечение, больничный<br>
    лист и т.д.).<br>
    Необходимо выводить следующие документы:<br>
    1. Статистические талоны.<br>
    2. Список пациентов, побывавших на приеме у определенного врача за<br>
    определенный период.<br>
    3. Список пациентов поликлиники с определенным диагнозом.<br>
    4. Список участков и участковых врачей.<br>"
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
                    <form action="admin.php" method="POST">
                        <input type='submit' class = "log_btn" name = 'exit' value='Выйти из учётной записи'><br>
                    </form>
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