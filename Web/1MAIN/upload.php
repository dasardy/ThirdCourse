<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="loadstyle.css">
    </head>
    <body> 
    <?php
    session_cache_expire(0);
    session_start();

    $_POST["index_book"] ?? "";
    $upload_path = $_POST["search_path"] ?? $_POST["book_path"]??"";
    $book = file_get_contents($upload_path);
    $book = preg_replace("<br>", "", $book);
    $book = preg_replace("/\n/", "<br>", $book);
    $book = preg_replace("/\r/", "", $book);
    $book = preg_replace("/\r\n/", "<br>", $book);
    $hight_text = $_POST["search_res"] ?? $_SESSION["s_text"] ?? "";

    if (isset($_POST["search_res"]) && !isset($_POST["del_hight"])) {
        $_SESSION["s_text"] = $_POST["search_res"];
        $_SESSION["highlight"] = true; // Устанавливаем флаг выделения
        $hight_text = $_POST["search_res"];
    } elseif (isset($_SESSION["s_text"]) && !isset($_POST["del_hight"])) {
        $hight_text = $_SESSION["s_text"];
    } else {
        $_SESSION["highlight"] = false; // Если не нажата кнопка и флаг выделения равен false
    }

    $book = $_SESSION["highlight"] ? str_replace($hight_text, '<span class="highlight">' . $hight_text . '</span>', $book) : $book;
    $lines = explode("<br>", $book);
    $book_name = preg_replace("/\./", "*", $upload_path);
    $curr_page = $_POST["search_page"] ?? $_POST["book_page"] ?? 0;
    define("LINES_ON_PAGE", 35);
    setcookie($book_name, $curr_page);

    if (isset($_POST["prev"])) {
        $curr_page = max(0, $curr_page - 1);
        setcookie($book_name, $curr_page);
    } elseif (isset($_POST["next"])) {
        $curr_page = min(round(count($lines) / LINES_ON_PAGE), $curr_page + 1);
        setcookie($book_name, $curr_page);
    }

    if (isset($_POST["del_hight"])) {
        $_SESSION["s_text"] = "";
        $_SESSION["highlight"] = false; // Устанавливаем флаг выделения в false при нажатии кнопки
    }

    function show_page($page, $array)
    {
        $i = $page * LINES_ON_PAGE;
        while ($i < $page * LINES_ON_PAGE + LINES_ON_PAGE && $i < count($array)) {
            echo $array[$i] . "<br>";
            $i++;
        }
    }
    ?>
    
    <form class = "back_to_start" action="index.php" method="post">
        <input type="submit" value="Вернуться на главную страницу">
    </form>
    <form action="search.php" method="post">
        <input type="hidden" name="search_path" value="<?php echo $upload_path ?>">
        <input  type="text" name="search_text">
        <button type="submit">Искать в книге</button>
    </form>
    <form action="upload.php" method="post">
        <input type="submit" name="del_hight" value="Убрать выделения">
        <input type="hidden" name="book_page" value="<?php echo $curr_page ?>">
        <input type="hidden" name="book_path" value="<?php echo $upload_path ?>">
        <div class="pages"><b>Страница № <?php echo $curr_page + 1 ?> </b></div>
        <div class="page_btns">
            <input type="submit" name="prev" value="Предыдущая страница">
            <input class="prev_btn" type="submit" name="next" value="Следущая страница">
        <div>
    </form>
    <?php
    echo show_page($curr_page, $lines);
    ?>
    </body>
</html>