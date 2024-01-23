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
                        <label class = 'error'>Посещение с таким номером талона уже имеется в таблице!<label>
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
                };
                return $text_msg;
            }
            function fine_msg($num_msg)
            {
                $text_msg = match($num_msg)
                {
                    1=>"<span class = 'main_text'>
                            label class = 'fine'>Запись успешно добавлена!<label>
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
            $userinfo = $_SESSION['user_info'];
            $doc_num = $userinfo['serv_num'];
            if (isset($_SESSION["db_param"])) {
                $connect = pg_connect($_SESSION["db_param"]);
            } else {
                die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
            }

            function getVisitData($connect, $serv_num)
            {
                $for_id_query = 'SELECT id_doc from Doctor WHERE serv_num = '.$serv_num; 
                $res = pg_query($connect, $for_id_query);
                $result = pg_fetch_all($res);
                $id_doc = $result[0]['id_doc'];
                $query = "SELECT * FROM Visit WHERE id_doc = ".$id_doc." ORDER BY id_visit";
                $result = pg_query($connect, $query);
                if ($result && pg_num_rows($result) > 0) {
                    return pg_fetch_all($result);
                } else {
                    echo error_msg(2, $connect);
                    return array();
                }
            }
            function insert_in_table($connect, $doc_num, $id_pat, $id_diag, $id_goal, $date_visit, $status_visit, $num_ticket)
            {
               
                if ($id_pat==''||$id_diag==''||$id_goal==''||$date_visit==''||$status_visit==''||$num_ticket=='')
                {
                    echo error_msg(1, $connect);
                }
                else{
                    $for_id_query = 'SELECT id_doc from Doctor WHERE serv_num = '.$doc_num; 
                    $res = pg_query($connect, $for_id_query);
                    $result = pg_fetch_assoc($res);
                    if ($result)
                    {
                        $id_doc = $result['id_doc'];
                        $id_pat = htmlspecialchars($id_pat);
                        $id_diag = htmlspecialchars($id_diag);
                        $id_goal = htmlspecialchars($id_goal);
                        $date_visit = htmlspecialchars($date_visit);
                        $status_visit = htmlspecialchars($status_visit);
                        $num_ticket = htmlspecialchars($num_ticket);
                        $check_query = 'SELECT * FROM Visit WHERE num_ticket ='.$num_ticket;
                        $res = pg_query($connect, $check_query);
                        if ($res) {
                            $result = pg_fetch_assoc($res);
                            if ($result)
                            {
                                echo error_msg(3, $connect);
                            }
                            else{
                                $insert_query = "CALL add_visit($1, $2, $3, $4, $5, $6, $7)";
                                $res = pg_query_params($connect, $insert_query, array($id_pat, $id_doc, $id_diag, $id_goal, $date_visit, $status_visit, $num_ticket));
                                if ($res) {
                                    echo fine_msg(1);
                                } else {
                                    echo error_msg(4, $connect);
                                }
                            }
                        } else 
                        {
                            echo error_msg(4, $connect);
                        }
                    }
                    else{
                        echo "
                        <span class = 'main_text'>
                            <label class = 'error'>Вас нет в списке врачей поликлиники! Добавление записи не будет произведено<label>
                        </span>
                        ";
                    }
                }
            }
            function upd_table($connect, $doc_num, $id_visit, $id_pat, $id_diag, $id_goal, $date_visit, $status_visit, $num_ticket)
            {
                if ($id_pat == '' || $id_diag == '' || $id_goal == '' || $date_visit == '' || $status_visit == '' || $num_ticket == '') {
                    echo error_msg(1, $connect);
                } else 
                {
                    $for_id_query = 'SELECT id_doc from Doctor WHERE serv_num = ' . $doc_num;
                    $res = pg_query($connect, $for_id_query);
                    $result = pg_fetch_assoc($res);
                    if ($result) {
                        $id_doc = $result['id_doc'];
                        if ($num_ticket != $_SESSION['old_numticket']) 
                        {
                            $check_query = 'SELECT * FROM Visit WHERE num_ticket = ' . $num_ticket;
                            $res = pg_query($connect, $check_query);
                            if (!$res) {
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
                                    $upd_query = "CALL upd_visit($1, $2, $3, $4, $5, $6, $7, $8)";
                                    $res = pg_query_params($connect, $upd_query,
                                        array($id_visit, $id_pat, $id_doc, $id_diag, $id_goal,
                                            $date_visit, $status_visit, $num_ticket));
                                    if ($res) {
                                        echo fine_msg(2);
                                    } else {
                                        echo error_msg(5, $connect);
                                    }
                                }
                            }
                        } else 
                        {
                            $upd_query = "CALL upd_visit($1, $2, $3, $4, $5, $6, $7, $8)";
                            $res = pg_query_params($connect, $upd_query,
                                array($id_visit, $id_pat, $id_doc, $id_diag, $id_goal,
                                    $date_visit, $status_visit, $num_ticket));
                            if ($res) {
                                echo fine_msg(2);
                            } else {
                                echo error_msg(5, $connect);
                            }
                        }
                    } 
                    else 
                    {
                        echo "
                        <span class = 'main_text'>
                            <label class = 'error'>Вас нет в списке врачей поликлиники! Обновление записи не будет произведено<label>
                        </span>
                        ";
                    }
                }
            }
            function delete_item($connect, $id_item)
            {
                $del_query = 'DELETE FROM Visit WHERE id_visit = '.$id_item;
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

            if (isset($_POST['append']))
            {
                echo '<form action= "admin.php" method = "POST">';
                    echo '<input type="input" class = "include_input" placeholder = "id_pat" name="id_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "id_diag" name="id_diag" value="">';
                    echo '<input type="input"  class = "include_input" placeholder = "id_goal" name="id_goal" value="">';
                    echo '<input type="input"  class = "include_input" placeholder = "date_visit" name="date_visit" value="">';
                    echo '<input type="input"  class = "include_input" placeholder = "status_visit" name="status_visit" value="">';
                    echo '<input type="input" class = "include_input"  placeholder = "num_ticket" name="num_ticket" value="">';
                    echo '<input type="submit"  class = "include_input" placeholder = "id_diag" name="applyadd" value="Подтвердить">';
                echo '</form>';
            }
            if (isset($_POST['edit'])) {
                if (!isset($_POST['selected_id'])) {
                    echo error_msg(7, $connect);
                } else {
                    $edit_query = "SELECT * FROM Visit WHERE id_visit =".$_POST['selected_id'];
                    $res = pg_query($connect, $edit_query);
                    $result = pg_fetch_assoc($res);
                    $_SESSION['old_numticket'] =  $result['num_ticket'];
                    echo '<form action= "admin.php" method = "POST">';
                        echo '<input type="hidden" name="id_visit" value="'.$_POST['selected_id'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "id_pat" name="id_pat" value="'.$result['id_pat'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "id_diag" name="id_diag" value="'.$result['id_diag'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "id_goal" name="id_goal" value="'.$result['id_goal'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "date_visit" name="date_visit" value="'.$result['date_visit'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "status_visit" name="status_visit" value="'.$result['status_visit'].'">';
                        echo '<input type="input" class = "include_input" placeholder = "num_ticket" name="num_ticket" value="'.$result['num_ticket'].'">';
                        echo '<input type="submit" class="log_btn"  name="applyupd" value="Подтвердить">';
                    echo '</form>';
                }
            }
            if (isset($_POST["delete"]))
            {
                if (!isset($_POST['selected_id'])) 
                {
                    echo error_msg(8, $connect);
                }
                else
                {   
                    $del_query = "SELECT * FROM Visit WHERE id_visit =".$_POST['selected_id'];
                    $res = pg_query($connect, $del_query);
                    $result = pg_fetch_assoc($res);

                    echo '<form action= "admin.php" method = "POST">';
                        echo "<label class = 'main_text'>Вы уверены, что хотите удалить посещение с номером: ".$result['num_ticket']."?</label>";
                        echo '<input type="hidden" name="id_visit" value="' . $_POST['selected_id'] . '">';
                        echo "<br>";
                        echo "<div class = 'l_button'>";
                            echo '<input type="submit" class="log_btn" name="yesdel" value="Да">';
                            echo '<input type="submit" class="log_btn"  name="nodel" value="Нет">';
                        echo "</div>";
                    echo '</form>';
                }
            }
            if (isset($_POST['applyadd']))
            {
                @insert_in_table($connect, $doc_num, $_POST['id_pat'], $_POST['id_diag'], $_POST['id_goal'], 
                $_POST['date_visit'], $_POST['status_visit'], $_POST['num_ticket']);
            }
            if (isset($_POST['applyupd']))
            {
                @upd_table($connect, $doc_num, $_POST['id_visit'], $_POST['id_pat'], $_POST['id_diag'], $_POST['id_goal'], 
                $_POST['date_visit'], $_POST['status_visit'], $_POST['num_ticket']);
            }
            if (isset($_POST['yesdel']))
            {
                @delete_item($connect, $_POST['id_visit']);
            }
            $result = getVisitData($connect, $doc_num);
            echo "<form action='admin.php' method='POST'>";
                echo '<table>';
                echo '<tr>';
                echo '<th></th>';
                echo '<th>id_visit</th>';
                echo '<th>id_pat</th>';
                echo '<th>id_doc</th>';
                echo '<th>id_diag</th>';
                echo '<th>id_goal</th>';
                echo '<th>date_visit</th>';
                echo '<th>status_visit</th>';
                echo '<th>num_ticket</th>';
            echo '</tr>';

                foreach ($result as $row) {
                    echo "
                        <tr>
                            <td><input type='radio' name='selected_id' value='{$row['id_visit']}'></td>
                            <td>{$row['id_visit']}</td>
                            <td>{$row['id_pat']}</td>
                            <td>{$row['id_doc']}</td>
                            <td>{$row['id_diag']}</td>
                            <td>{$row['id_goal']}</td>
                            <td>{$row['date_visit']}</td>
                            <td>{$row['status_visit']}</td>
                            <td>{$row['num_ticket']}</td>
                        </tr>
                    ";
                }
                echo '</table>';
                echo '<div class = "l_button">';
                    echo '<input class="log_btn" type="submit" name="append" value="Добавить">';
                    echo '<input class="log_btn" type="submit" name="edit" value="Изменить">';
                    echo '<input class="log_btn" type="submit" name="delete" value="Удалить">';
                echo '<div>';
            echo "</form>";
        ?>
    </body>
    
</html>