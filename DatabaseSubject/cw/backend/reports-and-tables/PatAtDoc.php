<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../frontend/style.css">
        <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    </head>
    <body>
        <script>
           function downloadPDF() {
            var element = document.getElementById("PatAtDocTable");
            html2pdf(element, {
                margin: 10,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            }).from(element).outputPdf().then(function (pdfDoc) {
                var pdf = new jsPDF('p', 'mm', 'a4');
                pdf.addPage();
                pdf.addImage(pdfDoc, 'JPEG', 0, 0, 210, 297);
                pdf.save('PatAtDoc.pdf');
            });
        }
        </script>
        <?php
            function error_msg($num_msg, $connect)
            {
                $text_msg = match($num_msg)
                {
                    1=>"<div class = 'main_text'>
                            <label class = 'error'>Пустые поля в формах, заполните все поля и повторите попытку!<label>
                        </div>",
                    2=>"<div class = 'main_text'>
                            <label class = 'error'>Запрос вернул пустой результат или произошла ошибка!<br>". pg_last_error($connect)."<label>
                        </div>"
                };
                return $text_msg;
            }
            function getPatAtDocData($connect, $serv_num, $start_date, $end_date)
            {
                $query = "SELECT * FROM pat_to_date($1, $2, $3) ORDER BY  date_visit ASC";
                $res = pg_query_params($connect, $query, array($serv_num, $start_date, $end_date));
                if (!$res)
                {
                    echo error_msg(2, $connect);
                }
                else{
                    $result = pg_fetch_all($res);
                    if($result)
                    {
                        echo '<table id = "PatAtDocTable">';
                        echo '<caption class = "main_text"> Отчёт "Пациенты у опр. врача за опр. период"</caption>';
                        echo '<tr>';
                            echo '<th>serv_num</th>';
                            echo '<th>name_doc</th>';
                            echo '<th>firstname_doc</th>';
                            echo '<th>specialization</th>';
                            echo '<th>num_medbook</th>';
                            echo '<th>name_pat</th>';
                            echo '<th>firstname_pat</th>';
                            echo '<th>diagnosis</th>';
                            echo '<th>date_visit</th>';
                        echo '</tr>';

                        foreach ($result as $row) {
                            echo "
                                <tr>
                                    <td>{$row['serv_num']}</td>
                                    <td>{$row['name_doc']}</td>
                                    <td>{$row['firstname_doc']}</td>
                                    <td>{$row['specialization']}</td>
                                    <td>{$row['num_medbook']}</td>
                                    <td>{$row['name_pat']}</td>
                                    <td>{$row['firstname_pat']}</td>
                                    <td>{$row['diagnosis']}</td>
                                    <td>{$row['date_visit']}</td>
                                </tr>
                            ";
                        }
                        echo '</table>';
                    }
                    else{echo error_msg(2, $connect);}
                }    

            }
            if (isset($_SESSION["db_param"])) {
                $user = $_SESSION['user_info'];
                $connect = pg_connect($_SESSION["db_param"]);
                echo '<form action= "admin.php" method = "POST">';
                    if ($user['jobtitle'] == 'Главный врач')
                    {
                        echo "<label class = 'main_text' >Введите необходимые значения полей в формы ниже:</label><br>";
                        echo '<input type="input" class = "include_input" placeholder = "serv_num"  name="serv_num" value="">';
                    }
                    else{
                        echo "<label class = 'main_text' >Введите необходимые значения полей в формы ниже:</label><br>";
                        echo '<input type="hidden" name="serv_num" value="'.$user['serv_num'].'">';
                    }
                    echo '<input type="input" class = "include_input" placeholder = "start_date"  name="start_date" value="">';
                    echo '<input type="input" class = "include_input" placeholder = "end_date" name="end_date" value="">';
                    echo '<input type="submit" class="log_btn" name="applyout" value="Вывести">';
                echo '</form>';
            } else {
                die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
            }

            if(isset($_POST['applyout']))
            {
                if ($_POST['serv_num']==''||$_POST['start_date']==''||$_POST['end_date']=='')
                {
                    echo error_msg(1, $connect);
                }
                else
                {
                    if ($user['jobtitle']=='Главный врач')
                    {
                        $serv_num = htmlspecialchars($_POST['serv_num']);
                    }
                    $serv_num = $_POST['serv_num'];
                    $start_date = htmlspecialchars($_POST['start_date']);
                    $end_date = htmlspecialchars($_POST['end_date']);
                    @getPatAtDocData($connect, $serv_num, $start_date, $end_date);
                }
            }
        ?>
        <button class = "log_btn" onclick="downloadPDF()">Скачать PDF</button>
    </body>
</html>