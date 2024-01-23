<!DOCTYPE html>
<?php
session_set_cookie_params(0);
session_start();
$id_questions = $_SESSION["questions"];
$id_answers = $_SESSION["answers"];
$dbparam = $_SESSION["conn_arr"];
$host = $dbparam[0];
$db = $dbparam[1];
$user = $dbparam[2];
$pass = $dbparam[3];
$conn = new mysqli($host, $user, $pass, $db);
$half_point = 0;
$point = 0;
$sel_answers = $_SESSION["selected_answers"];
if (count($sel_answers) == 0)
{$point=0;}
else {
    for ($i = 0; $i < count($sel_answers); $i++) {
        for ($j = 0; $j < count($sel_answers[$i]); $j++) {
            if ($i < count($sel_answers) - 1) {
                $an_t = mysqli_real_escape_string($conn, htmlspecialchars_decode($sel_answers[$i][$j]));
                $ans_query = "SELECT is_correct FROM answers WHERE question_id = $id_questions[$i] AND answer_text = '$an_t'";
                $fin_result = $conn->query($ans_query);
                $row = $fin_result->fetch_assoc();
                $f_res2 = $row["is_correct"];
                if ($f_res2 == 1) {
                    $point = $point + 2;
                };
            } else {
                $an_t = mysqli_real_escape_string($conn, htmlspecialchars_decode($sel_answers[$i][$j]));
                $ans_query = "SELECT is_correct FROM answers WHERE question_id = $id_questions[$i] AND answer_text = '$an_t'";
                $fin_result = $conn->query($ans_query);
                $row = $fin_result->fetch_assoc();
                $f_res2 = $row["is_correct"];
                if ($f_res2 == 1) {
                    $half_point++;
                } else {
                    $half_point--;
                };
            }
        }
    }
     if ($half_point > 0) {
            $point = $point + $half_point;
        }
}
$point_query = "INSERT INTO user_results (points) VALUES (?)";
$u_res = $conn->prepare($point_query);
$u_res->bind_param("i", $point);
$u_res->execute();

$allpoint = array();
$allpoint_query = "SELECT points FROM user_results";
$all_res = $conn->prepare($allpoint_query);
$all_res->execute();
$all_res->bind_result($points);
while ($all_res->fetch()) {
    array_push($allpoint, $points);
}
$_SESSION["result"] = $point;
$ready_res = array(0,0,0,0,1);
foreach ($allpoint as $point)
{
    if ($point<3)
    {$ready_res[0]++;}
    else if ($point<6)
    {$ready_res[1]++;}
    else if ($point<9)
    {$ready_res[2]++;}
    else{$ready_res[3]++;}

}
$_SESSION["all_result"] = $ready_res;
header("Location: ../2MAIN/x.php");
?>



