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
                        <label class = 'error'>Пациент с таким номером мед. полиса или номером мед. книжки уже имеется в таблице!<label>
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
                        <label class = 'error'>Пациент не может быть удален, потому что у него имеются посещения!<label>
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
            $reg = $_SESSION["user_info"];
            $reg_area = $reg["num_area"];

            if (isset($_SESSION["db_param"])) {
                $connect = pg_connect($_SESSION["db_param"]);
            } else {
                die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
            }
            


            function getPatientData($connect, $reg_area)
            {
                $query = "SELECT * FROM Patient WHERE num_area = ".$reg_area." ORDER BY id_pat";
                $result = pg_query($connect, $query);

                if ($result && pg_num_rows($result) > 0) {
                    return pg_fetch_all($result);
                } else {
                    echo error_msg(2, $connect);
                    return array();
                }
            }

            
            function insert_in_table($connect, $reg_area, $med_pol, $num_medbook, $firstname_pat, $name_pat, $fathername_pat=null, $village_pat, $street_pat, $house_pat, $flat_pat=null, $datebirth_pat)
            {
                if ($med_pol==''||$num_medbook==''||$firstname_pat==''||$name_pat==''||$village_pat ==''||$street_pat==''||$house_pat ==''||$datebirth_pat=='')
                    {
                        echo error_msg(1, $connect);
                    }
                else
                {
                    $med_pol = htmlspecialchars($med_pol);
                    $num_medbook = htmlspecialchars($num_medbook);
                    $firstname_pat = htmlspecialchars($firstname_pat);
                    $name_pat = htmlspecialchars($name_pat);
                    $village_pat = htmlspecialchars($village_pat);
                    $street_pat = htmlspecialchars($street_pat);
                    $house_pat = htmlspecialchars($house_pat);
                    $datebirth_pat = htmlspecialchars($datebirth_pat);
                    $fathername_pat = htmlspecialchars($fathername_pat);
                    $flat_pat = ($flat_pat !== null && $flat_pat !== '') ? htmlspecialchars($flat_pat) : null;
                    $for_id_query = 'SELECT id_area FROM Med_area WHERE num_area = '.$reg_area;
                    $res = pg_query($connect, $for_id_query);
                    $result = pg_fetch_assoc($res);
                    if (!$result)
                    {
                        echo "Участка с таким же номером, как у вас, не существует!";
                    }
                    else
                    {
                        $reg_id_area = $result['id_area'];
                        $check_query = 'SELECT * FROM Patient WHERE med_pol ='.$med_pol.' OR num_medbook = '.$num_medbook;
                        $res = pg_query($connect, $check_query);
                        if (!$res)
                        {
                            echo error_msg(4, $connect);
                        }
                        else
                        {
                            $result = pg_fetch_assoc($res);
                            if ($result)
                            {
                                echo error_msg(3, $connect);
                            }
                            else {
                                $insert_query = "CALL add_patient($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";
                                $res = pg_query_params($connect, $insert_query, 
                                    array($med_pol, $num_medbook, $firstname_pat, $name_pat, 
                                    $fathername_pat, $village_pat, $street_pat, $house_pat, 
                                    $flat_pat, $datebirth_pat, $reg_area, $reg_id_area));
                                if ($res) {
                                    echo fine_msg(1);
                                } else {
                                    echo error_msg(4, $connect);
                                }
                            }
                        
                        }
                    }
                    
                }
            }

            function upd_table($connect, $id_pat, $reg_area, $med_pol, $num_medbook, $firstname_pat, $name_pat, $fathername_pat, $village_pat, $street_pat, $house_pat, $flat_pat, $datebirth_pat)
            {
                if ($med_pol==''||$num_medbook==''||$firstname_pat==''||$name_pat==''||$village_pat ==''||$street_pat==''||$house_pat ==''||$datebirth_pat=='')
                {
                    echo error_msg(1, $connect);
                }
                else
                {
                    $med_pol = htmlspecialchars($med_pol);
                    $num_medbook = htmlspecialchars($num_medbook);
                    $firstname_pat = htmlspecialchars($firstname_pat);
                    $name_pat = htmlspecialchars($name_pat);
                    $village_pat = htmlspecialchars($village_pat);
                    $street_pat = htmlspecialchars($street_pat);
                    $house_pat = htmlspecialchars($house_pat);
                    $datebirth_pat = htmlspecialchars($datebirth_pat);
                    $fathername_pat = htmlspecialchars($fathername_pat);
                    $flat_pat = ($flat_pat !== null && $flat_pat !== '') ? htmlspecialchars($flat_pat) : null;
                    $for_id_query = 'SELECT id_area FROM Med_area WHERE num_area = '.$reg_area;
                    $res = pg_query($connect, $for_id_query);
                    $result = pg_fetch_assoc($res);

                    if ($med_pol!=$_SESSION['old_medpol'] || $num_medbook != $_SESSION['old_medbook'])
                    {
                        if ($num_medbook==$_SESSION['old_medbook'])
                        {
                            $check_query = 'SELECT * FROM Patient WHERE med_pol ='.$med_pol;
                        }
                        if ($med_pol==$_SESSION['old_medpol'])
                        {
                            $check_query = 'SELECT * FROM Patient WHERE num_medbook ='.$num_medbook;
                        }
                        $res = pg_query($connect, $check_query);
                        if(!$res)
                        {
                            echo error_msg(5, $connect);
                        }
                        else
                        {
                            $result = pg_fetch_assoc($res);
                            if ($result)
                            {
                                echo error_msg(3, $connect);
                            }
                            else{
                                $for_id_query = 'SELECT id_area FROM Med_area WHERE num_area = '.$reg_area;
                                $res = pg_query($connect, $for_id_query);
                                $result = pg_fetch_assoc($res);
                                $reg_id_area = $result['id_area'];
                                $upd_query = "CALL upd_patient($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)";
                                $res = pg_query_params($connect, $upd_query, 
                                        array($id_pat, $med_pol, $num_medbook, $firstname_pat, $name_pat, 
                                        $fathername_pat, $village_pat, $street_pat, $house_pat, 
                                        $flat_pat, $datebirth_pat, $reg_area, $reg_id_area));
                                if ($res) {
                                    echo fine_msg(2);
                                } else { 
                                    echo error_msg(5, $connect);
                                }
                            }
                        }
                    }
                    else {
                        $for_id_query = 'SELECT id_area FROM Med_area WHERE num_area = '.$reg_area;
                        $res = pg_query($connect, $for_id_query);
                        $result = pg_fetch_assoc($res);
                        $reg_id_area = $result['id_area'];
                        $upd_query = "CALL upd_patient($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)";
                        $res = pg_query_params($connect, $upd_query, 
                                array($id_pat, $med_pol, $num_medbook, $firstname_pat, $name_pat, 
                                $fathername_pat, $village_pat, $street_pat, $house_pat, 
                                $flat_pat, $datebirth_pat, $reg_area, $reg_id_area));
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
                $check_query1 = 'SELECT * FROM Visit WHERE id_pat = '.$id_item;
                $res1 = pg_query($connect, $check_query1);
                $result1 = pg_fetch_assoc($res1);
                if ($result1)
                {
                    echo error_msg(9, $connect);
                }
                else{
                    $del_query = 'DELETE FROM Patient WHERE id_pat = '.$id_item;
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
                    echo '<input type="input"  class = "include_input" placeholder = "med_pol" name="med_pol" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "num_medbook"  name="num_medbook" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "firstname_pat" name="firstname_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "name_pat" name="name_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "fathername_pat" name="fathername_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "village_pat" name="village_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "street_pat" name="street_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "house_pat" name="house_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "flat_pat" name="flat_pat" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "datebirth_pat" name="datebirth_pat" value="">';
                    echo '<input type="submit" class = "log_btn" name="applyadd" value="Подтвердить">';
                echo '</form>';
            }


            if (isset($_POST['edit'])) {
                if (!isset($_POST['selected_id'])) {
                    echo error_msg(7, $connect);
                } else {
                    $edit_query = "SELECT * FROM Patient WHERE id_pat =".$_POST['selected_id'];
                    $res = pg_query($connect, $edit_query);
                    $result = pg_fetch_assoc($res);
                    $_SESSION['old_medpol'] =  $result['med_pol'];
                    $_SESSION['old_medbook'] =  $result['num_medbook'];

                    echo '<form action= "admin.php" method = "POST">';
                        echo '<input type="hidden" name="id_pat" value="'.$_POST['selected_id'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "med_pol" name="med_pol" value="'.$result['med_pol'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "num_medbook" name="num_medbook" value="'.$result['num_medbook'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "firstname_pat" name="firstname_pat" value="'.$result['firstname_pat'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "name_pat" name="name_pat" value="'.$result['name_pat'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "fathername_pat" name="fathername_pat" value="'.$result['fathername_pat'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "village_pat" name="village_pat" value="'.$result['village_pat'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "street_pat" name="street_pat" value="'.$result['street_pat'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "house_pat" name="house_pat" value="'.$result['house_pat'].'">';
                        echo '<input type="input"  class = "include_input" placeholder = "flat_pat" name="flat_pat" value="'.$result['flat_pat'].'">';
                        echo '<input type="input"  class = "include_input" splaceholder = "datebirth_pat" name="datebirth_pat" value="'.$result['datebirth_pat'].'">';
                        echo '<input type="submit" class = "log_btn"  name="applyupd" value="Подтвердить">';
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
                    $del_query = "SELECT * FROM Patient WHERE id_pat =".$_POST['selected_id'];
                    $res = pg_query($connect, $del_query);
                    $result = pg_fetch_assoc($res);
                  
                    echo '<form action= "admin.php" method = "POST">';
                        echo '<label class = "main_text">Вы уверены, что хотите удалить пациента c медицинским полисом:'.$result['med_pol'].'?</label>';
                        echo '<input type="hidden" name="id_doc" value="' . $_POST['selected_id'] . '">';
                        echo '<div class = l_button>';
                            echo '<input type="submit" class = "log_btn"  name="yesdel" value="Да">';
                            echo '<input type="submit" class = "log_btn" name="nodel" value="Нет">';
                        echo '<div>';
                    echo '</form>';
                }
            }
            if (isset($_POST['applyadd']))
            {
                @insert_in_table($connect, $reg_area, $_POST['med_pol'], $_POST['num_medbook'], 
                    $_POST['firstname_pat'], $_POST['name_pat'], $_POST['fathername_pat'], 
                    $_POST['village_pat'], $_POST['street_pat'], $_POST['house_pat'], 
                    $_POST['flat_pat'], $_POST['datebirth_pat']);
            }
            if (isset($_POST['applyupd']))
            {
                @upd_table($connect, $_POST['id_pat'], $reg_area, $_POST['med_pol'], $_POST['num_medbook'], 
                    $_POST['firstname_pat'], $_POST['name_pat'], $_POST['fathername_pat'], 
                    $_POST['village_pat'], $_POST['street_pat'], $_POST['house_pat'], 
                    $_POST['flat_pat'], $_POST['datebirth_pat']);
            }
            if (isset($_POST['yesdel']))
            {
                @delete_item($connect, $_POST['id_doc']);
            }
            $result = getPatientData($connect, $reg_area);
            echo "<form action='admin.php' method='POST'>";
            echo '<table>';
            echo '<tr>';
                echo '<th></th>';
                echo '<th>id_pat</th>';
                echo '<th>med_pol</th>';
                echo '<th>num_medbook</th>';
                echo '<th>firstname_pat</th>';
                echo '<th>name_pat</th>';
                echo '<th>fathername_pat</th>';
                echo '<th>village_pat</th>';
                echo '<th>street_pat</th>';
                echo '<th>house_pat</th>';
                echo '<th>flat_pat</th>';
                echo '<th>datebirth_pat</th>';
                echo '<th>num_area</th>';
                echo '<th>id_area</th>';
            echo '</tr>';

            foreach ($result as $row) {
                echo "
                    <tr>
                        <td><input type='radio' name='selected_id' value='{$row['id_pat']}'></td>
                        <td>{$row['id_pat']}</td>
                        <td>{$row['med_pol']}</td>
                        <td>{$row['num_medbook']}</td>
                        <td>{$row['firstname_pat']}</td>
                        <td>{$row['name_pat']}</td>
                        <td>{$row['fathername_pat']}</td>
                        <td>{$row['village_pat']}</td>
                        <td>{$row['street_pat']}</td>
                        <td>{$row['house_pat']}</td>
                        <td>{$row['flat_pat']}</td>
                        <td>{$row['datebirth_pat']}</td>
                        <td>{$row['num_area']}</td>
                        <td>{$row['id_area']}</td>
                    </tr>
                ";
            }
            echo '</table>';
            echo '<div class = l_button>';
                echo '<input class = "log_btn" type="submit" name="append" value="Добавить">';
                echo '<input class = "log_btn" type="submit" name="edit" value="Изменить">';
                echo '<input class = "log_btn" type="submit" name="delete" value="Удалить">';
            echo '</div>';
            echo "</form>";
        ?>    
    </body>
</html>