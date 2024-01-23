<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="searchstyle.css">
    </head>
    <body>
        <?php
            echo '
            <form action="upload.php" method="post">
                <input type="submit" value="Вернуться назад"><br>
                <p></p>
                <input type="hidden" name="search_path" value="'. $_POST["search_path"].'">
            </form>
            ';
            if ($_POST["search_text"]!="")
            {
                $sb_path = $_POST["search_path"];
                $search_book = file_get_contents($sb_path);
                $search_book  = preg_replace("/\n/", "<br>", $search_book );
                $search_book = preg_replace("/\r/", "", $search_book);
                $search_book  = preg_replace("/\r\n/", "<br>", $search_book );
                $search_words = $_POST["search_text"];
                $search_words = explode(" ", $search_words);
                
                $pattern = "/[ ,\n].{0,100}" . quotemeta($search_words[0]);
                $i = 1;
                while ($i < count($search_words)) {
                    $pattern .= ".{1,100}" . quotemeta($search_words[$i]);
                    $i++;
                }
                $pattern .= ".{0,100}[ ,\n]/";
                $search_matches = array();
                $search_count = preg_match_all($pattern, $search_book, $search_matches, PREG_OFFSET_CAPTURE);
                if ($search_count > 0) 
                    {   
                        for ($overlap = 0; $overlap < $search_count; $overlap++)
                        {
                            echo "...".$search_matches[0][$overlap][0]."..."."<br>";
                            $search_result = $search_matches[0][$overlap][0];
                            $page_s = (int)$search_matches[0][$overlap][1];
                            $u_res = countBrBeforeSymbol($search_book, $page_s);
                            $realpage = (round($u_res/35, PHP_ROUND_HALF_DOWN));
                            $nr = (int)($realpage)+1;
                            echo "Страница книги: ".$nr."<br>";
                            echo '
                            <form action="upload.php" method="post">
                                <input type="hidden" name="search_page" value="'.($nr-1).'">
                                <input type="hidden" name="search_path" value="'.$sb_path.'">
                                <input type="hidden" name="search_res" value="'.htmlspecialchars($search_result).'">
                                <input type="submit" value="Перейти на страницу">
                                <p><hr></p>
                            </form>
                            ';
                        }
                    } 
                    else {
                        echo "Совпадений не найдено.";
                    }
                }else
            {
                echo "Строка поиска пуста!Вернитесь на предыдущую страницу!";
            }
            function countBrBeforeSymbol($text, $symbolIndex) {
                $count = 0;
                $currentIndex = 0;
            
                while (($brIndex = strpos($text, '<br>', $currentIndex)) !== false) {
                    if ($brIndex < $symbolIndex) {
                        $count++;
                        $currentIndex = $brIndex + strlen('<br>');
                    } else {
                        break;
                    }
                }
                return $count;
            }
        ?>
    </body>

<html>