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
            var element = document.getElementById("StatTalon");
            html2pdf(element, {
                margin: 10,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            }).from(element).outputPdf().then(function (pdfDoc) {
                var pdf = new jsPDF('p', 'mm', 'a4');
                pdf.addPage();
                pdf.addImage(pdfDoc, 'JPEG', 0, 0, 210, 297);
                pdf.save('AreasAndDocs.pdf');
            });
        }

        </script>
        <?php
            if (isset($_SESSION["db_param"])) {
                $connect = pg_connect($_SESSION["db_param"]);
                echo '<form action= "admin.php" method = "POST">';
                    echo '<input type="input" class = "include_input" placeholder = "num_ticket" name="num_ticket" value="">';
                    echo '<input type="submit" class = "log_btn" name="applyout" value="Выдать статистический талон">';
                echo '</form>';
            } 
            else {
                exit("Ошибка: Соединение отсутствует.");
            }
            if (isset($_POST['applyout']))
            {
                if ($_POST['num_ticket']!='')
                {
                    $num_ticket = htmlspecialchars($_POST['num_ticket']);
                    @getStatTicketData($connect, $num_ticket);
                }
                else
                {
                    echo 
                    "<span class = 'main_text'>
                        <label class = 'error'>Пустые поля в формах, заполните все поля и повторите попытку!<label>
                    </span>";
                }
            }

            function getStatTicketData($connect, $num_ticket)
            {
                $query = "SELECT * FROM statistic_ticket($1)";
                $res = pg_query_params($connect, $query, array($num_ticket));
                if(!$res)
                {
                   echo 
                   "<span class = 'main_text'>
                        <label class = 'error'>Произошла ошибка при выполнении запроса:<br>". pg_last_error($connect)."<label>
                    </span>";
                }
                else {
                    $result = pg_fetch_assoc($res);
                    if($result)
                    {   
                        echo "<div id='StatTalon' class = 'main_text'>";
                        echo "СТАТИСТИЧЕСКИЙ ТАЛОН<br><br>";
                        echo "Номер талона: ". $result['num_ticket'].'<br>';
                        echo "Дата визита: ". $result['date_visit'].'<br>';
                        echo "Табельный номер врача: ". $result['doc_service_number'].'<br>';
                        echo "Фамилия врача:". $result['firstname_doc'].'<br>';
                        echo "Имя врача: ". $result['name_doc'].'<br>';
                        echo "Специализация врача: ". $result['specialization'].'<br>';
                        echo "Категория врача: ". $result['category'].'<br>';
                        echo "Номер мед. книжки пациента: ". $result['med_book_num'].'<br>';
                        echo "Фамилия пациента: ". $result['firstname_pat'].'<br>';
                        echo "Имя пациента: ". $result['name_pat'].'<br>';
                        echo "Отчество пациента: ". $result['fathername_pat'].'<br>';
                        echo "Цель посещения: ". $result['visit_goal'].'<br>';
                        echo "Поставленный диагноз: ". $result['diagnosis'].'<br>';
                        echo "Описание диагноза: ". $result['desc_of_diag'].'<br>';
                        echo "Статус посещения: ". $result['status_visit'].'<br>';
                        echo "</div>";
                    }
                    else{
                        echo 
                        "<span class = 'main_text'>
                                <label class = 'error'>Запрос вернул пустой результат<label>
                        </span>";
                    }
                }   
            }
        ?>
        <button class = "log_btn" onclick="downloadPDF()">Скачать PDF</button>
    </body>
</html>