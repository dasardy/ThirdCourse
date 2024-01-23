<?php
    session_start();
    $user_score = $_SESSION["result"];
    $all_score  = $_SESSION["all_result"];
    $name = $_SESSION["name"];

    $encoded_u_score = json_encode($user_score);
    $encoded_all_score = json_encode($all_score);
    $encoded_name = json_encode($name);
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your results</title>
    <link rel="stylesheet" type="text/css" href="../2MAIN/style.css">
</head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<body>
    
    <div class = "name_app">Результаты теста</div>
    <div class = "congrat"><?php echo $name?>, поздравляем вас с выполнением теста!</div>
    <div class = "congrat">Ваш результат: <?php echo $user_score?> балла(ов)</div>
    <div class = "info">Ниже показаны результаты всех людей, 
    прошедших тест<br> (вы находитесь в группе, отмеченной красным)</div>
    <div class= "diagram">
    <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
    <script>
    const xValues = ["0-3", "4-6", "7-9", "10-12"];
    const yValues = <?php echo $encoded_all_score ?>;
    var userScore = <?php echo $encoded_u_score ?>;
    var numBar = Math.ceil(userScore/3)-1;
    if (userScore == 0)
    {
      numBar = 0;
    }
    console.log("yValues:", yValues);
    const barColors = ["green", "green","green","green"];
    barColors[numBar] = "red";
    new Chart("myChart", {
      type: "bar",
      data: {
        labels: xValues,
        datasets: [{
          backgroundColor: barColors,
          data: yValues
        }]
      },
      options: {
        legend: {display: false},
        title: {
          display: true,
          text: ""
        }
      }
    });
    </script>
    </div>
    <div>
    <form action="index.php" method="POST">
        <div>
            <button class="begin_btn">Пройти тест заново</button>
        </div>
    </form>
    </div>
  </body>
</html>
