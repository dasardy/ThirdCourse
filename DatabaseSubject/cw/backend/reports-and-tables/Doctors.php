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
                        <label class = 'error'>Врач с таким табельным номером уже имеется в таблице!<label>
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
                        <label class = 'error'>Врач не может быть удален, потому что у него имеются посещения!<label>
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
            function getDoctorData($connect)
            {
                $query = "SELECT * FROM Doctor ORDER BY id_doc";
                $result = pg_query($connect, $query);
                if ($result && pg_num_rows($result) > 0) {
                    return pg_fetch_all($result);
                } else {
                    echo error_msg(2 , $connect);
                    return array();
                }
            }
            function insert_in_table($connect, $serv_num, $firstname_doc, $name_doc, $father_name, $spec, $categor, $sal, $num_area =null)
            {
               
                if ($serv_num==''||$firstname_doc==''||$name_doc==''||$father_name==''||$spec==''||$categor ==''||$sal=='')
                    {
                        echo error_msg(1, $connect);
                    }
                else
                {
                    $serv_num = htmlspecialchars($serv_num);
                    $firstname_doc = htmlspecialchars($firstname_doc);
                    $name_doc = htmlspecialchars($name_doc);
                    $father_name = htmlspecialchars($father_name);
                    $spec = htmlspecialchars($spec);
                    $categor = htmlspecialchars($categor);
                    $sal = htmlspecialchars($num_area);
                    if ($num_area !=null)
                    {
                        $num_area = htmlspecialchars($num_area);
                    }
                    $check_query = 'SELECT * FROM Doctor WHERE serv_num ='.$serv_num;
                    $res = pg_query($connect, $check_query);
                    if (!$res)
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
                            $insert_query = "CALL add_doctor($1, $2, $3, $4, $5, $6, $7, $8)";
                            $res = pg_query_params($connect, $insert_query, 
                                array($serv_num, $firstname_doc, $name_doc, $father_name, 
                                $spec, $categor, $sal, $num_area));
                            if ($res) {
                                echo fine_msg(1);
                            } else {
                                echo error_msg(4, $connect);
                            }
                        }
                    }
                }
            }

            function upd_table($connect,$id_doc, $serv_num, $firstname_doc, $name_doc, $father_name, $spec, $categor, $sal, $num_area =null)
            {
                if ($serv_num==''||$firstname_doc==''||$name_doc==''||$father_name==''||$spec==''||$categor ==''||$sal=='')
                {
                    echo error_msg(1, $connect);
                }
                else
                {
                    $serv_num = htmlspecialchars($serv_num);
                    $firstname_doc = htmlspecialchars($firstname_doc);
                    $name_doc = htmlspecialchars($name_doc);
                    $father_name = htmlspecialchars($father_name);
                    $spec = htmlspecialchars($spec);
                    $categor = htmlspecialchars($categor);
                    $sal = htmlspecialchars($sal);
                    if ($num_area !=null)
                    {
                        $num_area = htmlspecialchars($num_area);
                    }
                    if ($serv_num!=$_SESSION['old_numdoc'])
                    {
                        $check_query = 'SELECT * FROM Doctor WHERE serv_num ='.$serv_num;
                        $res = pg_query($connect, $check_query);
                        if (!$res)
                        {
                            echo error_msg(5, $connect);
                        }
                        else{
                            $result = pg_fetch_assoc($res);
                            if ($result)
                            {
                                echo error_msg(3, $connect);
                            }
                            else
                            {
                                $upd_query = "CALL upd_doc($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                                $res = pg_query_params($connect, $upd_query , array($id_doc, $serv_num, $firstname_doc, $name_doc, $father_name, $spec, $categor, $sal, $num_area));
                                if ($res) {
                                    echo fine_msg(2);
                                } else {
                                    echo error_msg(5, $connect);
                                }
                            }
                        }
                    }
                    else {
                        $upd_query = "CALL upd_doc($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                        $res = pg_query_params($connect, $upd_query , array($id_doc, $serv_num, $firstname_doc, $name_doc, $father_name, $spec, $categor, $sal, $num_area));
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
                $check_query1 = 'SELECT * FROM Visit WHERE id_doc = '.$id_item;
                $res1 = pg_query($connect, $check_query1);
                $result1 = pg_fetch_assoc($res1);
                if ($result1)
                {
                    echo error_msg(9, $connect);
                }
                else{
                    $del_query = 'DELETE FROM Doctor WHERE id_doc = '.$id_item;
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
                    echo '<input class = "include_input" type="input" placeholder = "serv_num" name="serv_num" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "firstname_doc" name="firstname_doc" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "name_doc" name="name_doc" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "father_name" name="father_name" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "specialization" name="specialization" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "category" name="category" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "salary" name="salary" value="">';
                    echo '<input class = "include_input" type="input" placeholder = "num_area" name="num_area" value="">';
                    echo '<input type="submit" class = "log_btn" name="applyadd" value="Подтвердить">';
                echo '</form>';
            }
            if (isset($_POST['edit'])) {
                if (!isset($_POST['selected_id'])) {
                    echo error_msg(7, $connect);
                } else {
                    $edit_query = "SELECT * FROM doctor WHERE id_doc =".$_POST['selected_id'];
                    $res = pg_query($connect, $edit_query);
                    $result = pg_fetch_assoc($res);
                    $_SESSION['old_numdoc'] =  $result['serv_num'];
                    echo '<form action= "admin.php" method = "POST">';
                        echo '<input type="hidden" class = "include_input" placeholder = "village_area" name="id_doc" value="'.$_POST['selected_id'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "serv_num"name=" serv_num" value="'.$result['serv_num'].'">';
                        echo '<input type="input" class = "include_input"  placeholder = "firstname_doc" name="firstname_doc" value="'.$result['firstname_doc'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "name_doc" name="name_doc" value="'.$result['name_doc'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "father_name" name="father_name" value="'.$result['father_name'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "specialization" name="specialization" value="'.$result['specialization'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "category" name="category" value="'.$result['category'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "salary" name="salary" value="'.$result['salary'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "num_area" name="num_area" value="'.$result['num_area'].'">';
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
                    $del_query = "SELECT * FROM Doctor WHERE id_doc =".$_POST['selected_id'];
                    $res = pg_query($connect, $del_query);
                    $result = pg_fetch_assoc($res);
                  
                    echo '<form action= "admin.php" method = "POST">';
                        echo '<label class = "main_text">Вы уверены, что хотите удалить врача с табельным номером '.$result['serv_num'].'</label>?';
                        echo '<input type="hidden" class = "log_btn"  name="id_doc" value="' . $_POST['selected_id'] . '">';
                        echo "<div class = 'l_button'>";
                            echo '<input type="submit" class = "log_btn" name="yesdel" value="Да">';
                            echo '<input type="submit" class = "log_btn" name="nodel" value="Нет">';
                        echo "<div>";
                    echo '</form>';
                }
            }
            if (isset($_POST['applyadd']))
            {
                @insert_in_table($connect, $_POST['serv_num'], $_POST['firstname_doc'], $_POST['name_doc'], 
                $_POST['father_name'], $_POST['specialization'], $_POST['category'], $_POST['salary'], $_POST['num_area']);
            }
            if (isset($_POST['applyupd']))
            {
                @upd_table($connect, $_POST['id_doc'], $_POST['serv_num'], $_POST['firstname_doc'], $_POST['name_doc'], 
                $_POST['father_name'], $_POST['specialization'], $_POST['category'], $_POST['salary'], $_POST['num_area']);
            }
            if (isset($_POST['yesdel']))
            {
                @delete_item($connect, $_POST['id_doc']);
            }
            $result = getDoctorData($connect);
            echo "<form action='admin.php' method='POST'>";
                echo '<table>';
                echo '<tr>';
                echo '<th></th>';
                echo '<th>id_doc</th>';
                echo '<th>serv_num</th>';
                echo '<th>firstname_doc</th>';
                echo '<th>name_doc</th>';
                echo '<th>father_name</th>';
                echo '<th>specialization</th>';
                echo '<th>category</th>';
                echo '<th>salary</th>';
                echo '<th>num_area</th>';
                echo '</tr>';

                foreach ($result as $row) {
                    echo "
                        <tr>
                            <td><input type='radio' name='selected_id' value='{$row['id_doc']}'></td>
                            <td>{$row['id_doc']}</td>
                            <td>{$row['serv_num']}</td>
                            <td>{$row['firstname_doc']}</td>
                            <td>{$row['name_doc']}</td>
                            <td>{$row['father_name']}</td>
                            <td>{$row['specialization']}</td>
                            <td>{$row['category']}</td>
                            <td>{$row['salary']}</td>
                            <td>{$row['num_area']}</td>
                        </tr>
                    ";
                }
                echo '</table>';
                echo "<div class = 'l_button'>";
                    echo '<input type="submit" class = "log_btn" name="append" value="Добавить">';
                    echo '<input type="submit" class = "log_btn" name="edit" value="Изменить">';
                    echo '<input type="submit" class = "log_btn" name="delete" value="Удалить">';
                echo "<div>";
            echo "</form>";
            
        ?>
    </body>
</html>