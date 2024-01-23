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
            var element = document.getElementById("AreasAndDocs");
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
            } else {
                die("Ошибка: Соединение отсутствует или вы не вошли в учётную запись!");
            }
            function getAreaAndDocData($connect)
            {
                $query = "SELECT * FROM list_of_docs() ORDER BY num_area";
                $result = pg_query($connect, $query);

                if ($result && pg_num_rows($result) > 0) {
                    return pg_fetch_all($result);
                } else {
                    echo "Запрос вернул пустой результат или произошла ошибка: " . pg_last_error($connect);
                    return array();
                }
            }
            
           $result = getAreaAndDocData($connect);
            echo '<table id="AreasAndDocs">';
                echo '<caption class = "main_text">Отчёт "Список участков и участковых врачей"</caption>';
                echo '<tr>';
                echo '<th>num_area</th>';
                echo '<th>village_area</th>';
                echo '<th>street_area</th>';
                echo '<th>house_area</th>';
                echo '<th>serv_num</th>';
                echo '<th>name_doc</th>';
                echo '<th>firstname_doc</th>';
                echo '<th>specialization</th>';
                echo '</tr>';

                foreach ($result as $row) {
                    echo "
                        <tr>
                            <td>{$row['num_area']}</td>
                            <td>{$row['village_area']}</td>
                            <td>{$row['street_area']}</td>
                            <td>{$row['house_area']}</td>
                            <td>{$row['serv_num']}</td>
                            <td>{$row['name_doc']}</td>
                            <td>{$row['firstname_doc']}</td>
                            <td>{$row['specialization']}</td>
                        </tr>
                    ";
                }
            echo '</table>';
        ?>
        <button class = "log_btn" onclick="downloadPDF()">Скачать PDF</button>
    </body>
</html>