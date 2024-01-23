<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>GIGAREAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>SMART READER</h1>
<p>Выберите, пожалуйста, книгу для прочтения:</p>
<table>
    <?php
    // Массив книг с книгам
    $books = [
        [
            'name' => 'Джоан Роулинг. Гарри Поттер и Философский камень',
            'path' => 'books/HarryPotter.txt',
            'image' => 'books/images/1.jpg',
        ],
        [
            'name' => 'Александр Пушкин. Евгений Онегин',
            'path' => 'books/Onegin.txt',
            'image' => 'books/images/4.jpg',
        ],
        [
            'name' => 'Фёдор Достоевский. Преступление и наказание',
            'path' => 'books/CrimeAndPunishment.txt',
            'image' => 'books/images/3.jpg',
        ],
        [
            'name' => 'Дуглас Адамс. Автостопом по галактике',
            'path' => 'books/Galaxy.txt',
            'image' => 'books/images/5.jpg',
        ],
        [
            'name' => 'Уильям Шекспир. Гамлет',
            'path' => 'books/Hamlet.txt',
            'image' => 'books/images/6.jpg',
        ],
    ];
    // Вывод книги
    $count = 0;
    foreach ($books as $book) 
    {
        if ($count % 4 == 0) {
            echo '<tr>';
        }

        echo '<td>';
        echo '<img src="' . $book['image'] . '" alt="Обложка книги"><br><br>';
        echo '<form action="upload.php" method="post">';
        echo '<input type="hidden" name="book_path" value="' . $book['path'] . '">';
        echo '<input type="submit" name="index_book" value="' . $book['name'] . '">';
        echo '</form>';
        echo '</td>';


        if ($count % 4 == 3) {
            echo '</tr><br>';
        }
        $count++;
    }
    if ($count % 4 != 0) {
        echo '</tr>';
    }
    ?>
</table>
</body>
</html>
