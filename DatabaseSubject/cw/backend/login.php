<?php
    session_set_cookie_params(0);
    session_start();
    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        $db_param = 
        "host=localhost
        port=5432
        dbname=coursework
        user=postgres
        password=postgres";
        $connect = pg_connect(
            $db_param                
        );
        if ($connect) {
            $query = "SELECT * FROM users WHERE email = $1 AND user_password = $2";
            $res = pg_query_params($connect, $query, array($email, $password));

            if ($res) {
                $result = pg_fetch_assoc($res);

                if ($result) {
                    $_SESSION["db_param"] = $db_param;
                    $_SESSION['user_info'] = $result;
                    
                    header("Location:../frontend/finelog.php");
                } else {
                    header("Location: ../frontend/notlog.html");
                }
            } else {
                echo "Ошибка выполнения запроса: " . pg_last_error($connect);
            }
        } else {
            echo "Ошибка подключения к базе данных.";
        }
    }

?>