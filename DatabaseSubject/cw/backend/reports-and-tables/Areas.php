<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../frontend/style.css">
    </head>
    <body>
        <?php
            function error_msg($num_msg, $connect)
            {
                $text_msg = match($num_msg)
                {
                1=>"<span class = 'main_text'>
                        <label class = 'error'>Пустые поля в формах, заполните все поля и повторите попытку!<label>
                    </span>",
                2=>"<span class = 'main_text'>
                        <label class = 'error'>Запрос вернул пустой результат или произошла ошибка:". pg_last_error($connect)."<label>
                    </span>",
                3=> "<span class = 'main_text'>
                        <label class = 'error'>Участок с таким номером уже имеется в таблице!<label>
                    </span>",
                4=>"<span class = 'main_text'>
                        <label class = 'error'>Произошла ошибка при добавлении записи:<br> " . pg_last_error($connect)."<label>
                    </span>",
                5=>"<span class = 'main_text'>
                        <label class = 'error'>Произошла ошибка при обновлении записи:<br> " . pg_last_error($connect)."<label>
                    </span>",
                6=>"<span class = 'main_text'>
                        <label class = 'error'>Произошла ошибка при удалении записи:<br> " . pg_last_error($connect)."<label>
                    </span>",
                7=>"<span class = 'main_text'>
                        <label class = 'error'>Пожалуйста, выберите запись для изменения! <label>
                    </span>",
                8=>"<span class = 'main_text'>
                        <label class = 'error'>Пожалуйста, выберите запись для удаления! <label>
                    </span>", 
                9=>"<span class = 'main_text'>
                        <label class = 'error'>Участок не может быть удален, потому что в нём есть врачи или пациенты!<label>
                    </span>",            
                };
                return $text_msg;
            }
            function fine_msg($num_msg)
            {
                $text_msg = match($num_msg)
                {
                    1=>"<span class = 'main_text'>
                            <label class = 'fine'>Запись успешно добавлена!<label>
                        </span>",
                    2=>"<span class = 'main_text'>
                            <label class = 'fine'>Запись успешно обновлена!<label>
                        </span>",
                    3=>"<span class = 'main_text'>
                            <label class = 'fine'>Запись успешно удалена!<label>
                        </span>",
                };
                return $text_msg;
            }
            if (isset($_SESSION["db_param"])) {
                $connect = pg_connect($_SESSION["db_param"]);
            } else {
                die("Ошибка: Соединение отсутствует или вы не вошли в учётную запись!");
            }
            function getAreaData($connect)
            {
                $query = "SELECT * FROM med_area ORDER BY id_area";
                $result = pg_query($connect, $query);

                if ($result && pg_num_rows($result) > 0) {
                    return pg_fetch_all($result);
                } else {
                    echo error_msg(2, $connect);
                    return array();
                }
            }
            function insert_in_table($connect, $num_area, $village_area, $street_area, $house_area)
            {
               
                if ($num_area==''||$village_area==''||$street_area==''||$house_area=='')
                    {
                        echo error_msg(1, $connect);
                    }
                else
                {
                    $village_area = htmlspecialchars($village_area);
                    $street_area = htmlspecialchars($street_area);
                    $house_area = htmlspecialchars($house_area);
                    $check_query = 'SELECT * FROM Med_area WHERE num_area ='.$num_area;
                    $res = pg_query($connect, $check_query);
                    if(!$res)
                    {
                        echo error_msg(4, $connect);
                    }
                    else{
                        $result = pg_fetch_assoc($res);
                        if ($result)
                        {
                            echo error_msg(3, $connect);
                        }
                        else
                        {
                            $insert_query = "CALL add_med_area($1, $2, $3, $4)";
                            $res = pg_query_params($connect, $insert_query, array($num_area, $village_area, $street_area, $house_area));
                            if ($res) {
                                echo fine_msg(1); 
                            } else {
                                echo error_msg(4, $connect);
                            }
                        }
                    }
                }
            }

            function upd_table($connect, $id_area, $num_area, $village_area, $street_area, $house_area)
            {
                if ($num_area==''||$village_area==''||$street_area==''||$house_area=='')
                {
                    echo error_msg(1, $connect);
                }
                else
                {
                    $village_area = htmlspecialchars($village_area);
                    $street_area = htmlspecialchars($street_area);
                    $house_area = htmlspecialchars($house_area);
                    if ($num_area!=$_SESSION['old_numarea'])
                    {
                        $check_query = 'SELECT * FROM med_area WHERE num_area ='.$num_area;
                        $res = pg_query($connect, $check_query);
                        if (!$res)
                        {
                            echo error_msg(5, $connect);
                        }
                        else
                        {
                            $result = pg_fetch_assoc($res);
                            if ($result)
                            {
                                echo error_msg(3, $connect);
                                return array();
                            }
                            else{
                                $upd_query = "CALL upd_med_area($1, $2, $3, $4, $5)";
                                $res = pg_query_params($connect, $upd_query , array($id_area, $num_area, $village_area, $street_area, $house_area));
                                if ($res) {
                                    echo fine_msg(2); 
                                } else {
                                    echo error_msg(5, $connect);
                                }
                            }
                        }
                    }
                    else {
                        $upd_query = "CALL upd_med_area($1, $2, $3, $4, $5)";
                        $res = pg_query_params($connect, $upd_query , array($id_area, $num_area, $village_area, $street_area, $house_area));
                        if ($res) {
                            echo fine_msg(2); 
                        } else {
                            echo error_msg(5, $connect);
                        }
                    }
                }
            }
            function delete_item($connect, $id_item)
            {
                $check_query1 = 'SELECT * FROM Patient p WHERE p.id_area = '.$id_item;
                $check_query2 = 'SELECT * FROM Doctor d, Med_area ma
                    WHERE ma.num_area = d.num_area AND ma.id_area = '.$id_item;
                $res1 = pg_query($connect, $check_query1);
                $res2 = pg_query($connect, $check_query2);
                $result1 = pg_fetch_assoc($res1);
                $result2 = pg_fetch_assoc($res2);
                if ($result1 || $result2)
                {
                    echo error_msg(9, $connect);
                }
                else{
                    $del_query = 'DELETE FROM Med_area WHERE id_area = '.$id_item;
                    $res = pg_query($connect, $del_query);
                    if ($res)
                    {
                        echo fine_msg(3); 
                    }
                    else
                    {
                        echo error_msg(6, $connect);
                    }
                }
            }

            if (isset($_POST['append']))
            {
                echo '<form action= "admin.php" method = "POST">';
                    echo '<input type="input" class = "include_input" placeholder = "num_area" name="num_area" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "village_area" name="village_area" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "street_area" name="street_area" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "house_area" name="house_area" value="">';
                    echo '<input type="submit" class = "log_btn" name="applyadd" value="Подтвердить">';
                echo '</form>';
            }
            if (isset($_POST['edit'])) {
                if (!isset($_POST['selected_id'])) {
                    echo error_msg(7, $connect);
                } else {
                    $edit_query = "SELECT * FROM med_area WHERE id_area =".$_POST['selected_id'];
                    $res = pg_query($connect, $edit_query);
                    $result = pg_fetch_assoc($res);
                    $_SESSION['old_numarea'] =  $result['num_area'];
                    echo '<form action= "admin.php" method = "POST">';
                        echo '<input type="hidden" name="id_area" value="'.$_POST['selected_id'].'">';
                        echo '<input type="input" class = "include_input"  placeholder = "num_area" name="num_area" value="'.$result['num_area'].'">';
                        echo '<input type="input" class = "include_input"  placeholder = "village_area" name = "village_area" value="'.$result['village_area'].'">';
                        echo '<input type="input" class = "include_input"  placeholder = "street_area" name = "street_area"  value="'.$result['street_area'].'">';
                        echo '<input type="input" class = "include_input"  placeholder = "house_area" name = "house_area" value="'.$result['house_area'].'">';
                        echo '<input type="submit" class = "log_btn" name="applyupd" value="Подтвердить">';
                    echo '</form>';
                    
                }
            }
            if (isset($_POST["delete"]))
            {
                if (!isset($_POST['selected_id'])) {
                    echo error_msg(8, $connect);
                }
                else
                {   
                    $del_query = "SELECT * FROM med_area WHERE id_area =".$_POST['selected_id'];
                    $res = pg_query($connect, $del_query);
                    $result = pg_fetch_assoc($res);
                  
                    echo '<form action= "admin.php" method = "POST">';
                        echo '<label class = "main_text">Вы уверены, что хотите удалить участок с номером '.$result['num_area'].'?<label><br>';
                        echo '<input type="hidden" name="id_area" value="' . $_POST['selected_id'] . '">';
                        echo '<div class = l_button>';
                            echo '<input type="submit"  class = "log_btn" name="yesdel" value="Да">';
                            echo '<input type="submit" class = "log_btn" name="nodel" value="Нет">';
                        echo '<div>';
                    echo '</form>';
                }
            }
            if (isset($_POST['applyadd']))
            {
                @insert_in_table($connect, $_POST['num_area'], $_POST['village_area'], $_POST['street_area'], $_POST['house_area']);
            }
            if (isset($_POST['applyupd']))
            {
                @upd_table($connect, $_POST['id_area'],$_POST['num_area'], $_POST['village_area'], $_POST['street_area'], $_POST['house_area']);
            }
            if (isset($_POST['yesdel']))
            {
                @delete_item($connect, $_POST['id_area']);
            }
            $result = getAreaData($connect);
            echo "<form action='admin.php' method='POST'>";
                echo '<table>';
                echo '<tr>';
                echo '<th></th>';
                echo '<th>id_area</th>';
                echo '<th>num_area</th>';
                echo '<th>village_area</th>';
                echo '<th>street_area</th>';
                echo '<th>house_area</th>';
            echo '</tr>';

                foreach ($result as $row) {
                    echo "
                        <tr>
                            <td><input type='radio' name='selected_id' value='{$row['id_area']}'></td>
                            <td>{$row['id_area']}</td>
                            <td>{$row['num_area']}</td>
                            <td>{$row['village_area']}</td>
                            <td>{$row['street_area']}</td>
                            <td>{$row['house_area']}</td>
                        </tr>
                    ";
                }
                echo '</table>';
                echo '<div class = l_button>';
                    echo '<input class = "log_btn" type="submit" name="append" value="Добавить">';
                    echo '<input class = "log_btn" type="submit" name="edit" value="Изменить">';
                    echo '<input class = "log_btn" type="submit" name="delete" value="Удалить">';
                echo '<div>';
            echo "</form>";
            
        ?>
        
        
    </body>
    
</html>