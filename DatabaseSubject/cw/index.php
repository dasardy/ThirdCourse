<!DOCTYPE html>
<html class="no_body" lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="frontend/style.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <title>Login</title>
    </head>
    <body class="no_body">
        <div class = "l_page">
            <form  action="../cw/backend/login.php" method="POST">
                <div class = "c_text"> <label >Авторизация</label><br></div>
               
                <input class = "lform_text" type="email" placeholder="Введите свой email" name="email" required><br>
                <input class = "lform_text"type="password" placeholder="Введите пароль" name="password" required><br>
                
                <div class = "l_button">
                    <input class = "log_btn"type="submit" name="login" value="Войти">
                </div>
            </form>
        </div>
    </body>
</html>