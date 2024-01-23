<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../frontend/style.css">
        <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
       
    <body>
        
        <script>
           function downloadPDF() {
            var element = document.getElementById("PatAtDiagTable");
            html2pdf(element, {
                margin: 10,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(element).outputPdf().then(function (pdfDoc) {
                var pdf = new jsPDF('p', 'mm', 'a4');
                pdf.text('Отчёт', 10, 10);
                pdf.addPage();
                pdf.addImage(pdfDoc, 'JPEG', 0, 0, 210, 297);
                
                // Сохраняем PDF только после добавления текста и изображения на страницу
                pdf.save('PatAtDiag.pdf');
            });
        }

        </script>
        <?php
        
            if (isset($_SESSION["db_param"])) {
                $connect = pg_connect($_SESSION["db_param"]);
                $diag_query = 'SELECT name_diag FROM  Diagnosis';
                $res = pg_query($connect, $diag_query);
                $result = pg_fetch_all($res);
                $diag_arr = array();
                foreach ($result as $value)
                {
                    array_push($diag_arr, $value['name_diag']);
                }
                echo '<form action= "admin.php"  name = "select_diag" method = "POST">';
                    echo "<label class = 'main_text'>Выберите диагноз из списка диагнозов: </label><br>";
                    echo '<select class = "include_input" name = "selected_diagnosis">';
                    foreach ($diag_arr as $diagnosis)
                    {
                        echo '<option>'.$diagnosis.'</option>';
                    }
                    echo '</select>';
                    echo '<input type="submit" class = "log_btn" name="applyout" value="Вывести">';
                echo '</form>';
            } 
            else {
                die('Вы не вошли в учётную запись! Вернитесь на страницу авторизации и повторите попытку!');
            }
            if (isset($_POST['applyout']))
            {
                if (isset($_POST['selected_diagnosis']))
                {
                   $name_diag = $_POST['selected_diagnosis'];
                   getPatAtDiagData($connect, $name_diag);
                }
                else
                {
                    echo "Пустые поля в формах, заполните все поля и повторите попытку!";
                }
            }

            function getPatAtDiagData($connect, $name_diag)
            {
                $query = "SELECT * FROM pat_with_diag($1) ORDER BY  firstname_pat  ASC";
                $res = pg_query_params($connect, $query, array($name_diag));
                $result = pg_fetch_all($res);
                if($result)
                {
                    echo '<table id="PatAtDiagTable">';
                    echo '<caption class = "main_text">Отчёт "Пациенты с определённым диагнозом"</caption>';
                    echo '<tr>';
                        echo '<th>pat_medbook</th>';
                        echo '<th>name_pat</th>';
                        echo '<th>firstname_pat</th>';
                        echo '<th>diagnosis</th>';
                        echo '<th>num_area</th>';
                    echo '</tr>';
                    foreach ($result as $row) {
                        echo "
                            <tr>
                                <td>{$row['num_pat']}</td>
                                <td>{$row['name_pat']}</td>
                                <td>{$row['firstname_pat']}</td>
                                <td>{$row['diagnosis']}</td>
                                <td>{$row['num_area']}</td>
                            </tr>
                        ";
                    }
                echo '</table>';
                }
                else {
                    echo "Запрос вернул пустой результат или произошла ошибка: " . pg_last_error($connect);
                }
            }    
        ?>
 
        <button class = "log_btn" onclick="downloadPDF()">Скачать PDF</button>
    </body>
</html>