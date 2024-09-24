<?php
require 'vendor/connect.php';
require_once 'vendor/helpers.php';
session_start();

$postID = $_GET['post_id']; // Получаем ID поста из URL

// Получаем данные поста
$sql = "SELECT post.text, post.title, category.title AS category_title, users.full_name AS author_name 
        FROM post 
        JOIN category ON post.category = category.id 
        JOIN users ON post.author = users.id 
        WHERE post.id = $postID";
$post_result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($post_result);

$message_error = '';
$message_success = '';

// Проверяем, был ли отправлен комментарий или запрос на удаление
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_comment_id'])) {
        // Обработка удаления комментария
        $commentID = $_POST['delete_comment_id'];
        $userID = $_SESSION['user_id'];

        // Проверяем, является ли текущий пользователь автором комментария
        $check_sql = "SELECT * FROM comments WHERE id = $commentID AND user_id = $userID";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            // Удаляем комментарий
            $delete_sql = "DELETE FROM comments WHERE id = $commentID";
            if (mysqli_query($conn, $delete_sql)) {
                $message_success = 'Комментарий успешно удалён!';
            } else {
                $message_error = 'Ошибка при удалении комментария: ' . mysqli_error($conn);
            }
        } else {
            $message_error = 'Вы не можете удалить этот комментарий.';
        }
    } elseif (isset($_POST['comment'])) {
        // Обработка добавления комментария
        if (isset($_SESSION['user_id'])) { // Только авторизованный пользователь может оставлять комментарии
            $userID = $_SESSION['user_id'];
            $comment = trim($_POST['comment']);
            if (!empty($comment)) {
                $insert_sql = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES ('$postID', '$userID', '$comment', NOW())";
                if (mysqli_query($conn, $insert_sql)) {
                    $message_success = 'Комментарий успешно добавлен!';
                } else {
                    $message_error = 'Ошибка при добавлении комментария: ' . mysqli_error($conn);
                }
            } else {
                $message_error = 'Комментарий не может быть пустым!';
            }
        } else {
            $message_error = 'Вы должны войти в систему, чтобы оставить комментарий.';
        }
    }
}

// Получаем список комментариев
$comments_sql = "SELECT comments.id, comments.comment, comments.created_at, users.full_name, users.id AS user_id 
                 FROM comments 
                 JOIN users ON comments.user_id = users.id 
                 WHERE post_id = $postID 
                 ORDER BY created_at DESC";
$comments_result = mysqli_query($conn, $comments_sql);
$comments = mysqli_fetch_all($comments_result, MYSQLI_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Авторизация</title>
</head>
<body>

<section class="main-section">
        <div class="container main-section__mini-nav-container">
            
        <div class="main-section__mini-nav-item">
            <div class="mini-nav-item__wrapper">
                <img src="icons/Hamburger menu.svg" alt="hamburger" height="20px">
                <a href="#">Sections</a>
            </div>
            
            <div class="mini-nav-item__wrapper">
                <form action="" class="nav-item__form">
                    <div class="nav-item__form-item">
                        <img src="icons/Search.svg" alt="">
                        <p class="text-input">Search</p>
                    </div>
                    <input class="input-nav nav__input" type="text">
                </form>
            </div>

            <div class="mini-nav-item__wrapper">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-none">
                        <img src="icons/Man.svg" alt="Profile Icon">
                        <a href="profile.php">Профиль</a>
                    </button>
                <?php else: ?>
                    <button class="btn btn-none">
                        <img src="icons/Man.svg" alt="Man">
                        <a href="login.php">Sign In</a>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        </div>

        <div class="container main-section__nav-container">
            <!-- Навигационное меню -->
            <header class="main-section__header">
                <img class="main-section__logo" src="/icons/image-252.svg" alt="Изображение логотипа">
                <p class="main-section__text text-nav">Boston and New York Bear Brunt</p>
            </header>
           
            <div class="main-section__nav-img">
                <a href="index.php"><img src="/icons/Uppercase text.svg" alt=""></a>
            </div>


            <div class="main-section__date-container">
            <div class="main-box">
                <p class="main-box__text text_dark">
                    <?php
                        $months = [
                            1 => 'января', 
                            2 => 'февраля', 
                            3 => 'марта', 
                            4 => 'апреля', 
                            5 => 'мая', 
                            6 => 'июня', 
                            7 => 'июля', 
                            8 => 'августа', 
                            9 => 'сентября', 
                            10 => 'октября', 
                            11 => 'ноября', 
                            12 => 'декабря'
                        ];

                        $day = date('d');
                        $month = date('n');
                        $year = date('Y');

                        echo $day . ' ' . $months[$month] . ' ' . $year . 'г';
                    ?>
                </p>
            </div>

            <div class="main-section__weather">
                <p class="main-section__text weather_text"><?php displayWeather('Ufa'); ?></p>
            </div>
        </div>
        </div>
</section> 


<section class="profile-section">
    <div class="container profile-section__container">
        <div class="profile__left-wrapper">
                <form action="profile.php">
                    <button class="btn btn-primary btn_max-width">Профиль</button>
                </form>

                <form action="clause.php">
                    <button class="btn btn-primary btn_max-width">Статьи</button>
                </form>

                <form action="clause_edit.php">
                    <button class="btn btn-primary btn_max-width">Редактирование Статьи</button>
                </form>

        </div>

        <div class="profile__right-wrapper">
            <h1>Автор: <?= htmlspecialchars($post['author_name']) ?></h1>
            <h1>Категория: <?= htmlspecialchars($post['category_title']) ?></h1>
            <h1>Тема: <?= htmlspecialchars($post['title']) ?></h1>
            <p><?= htmlspecialchars($post['text']) ?></p>

            <!-- Сообщения об ошибках и успехах -->
            <?php if ($message_error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($message_error) ?></div>
            <?php endif; ?>

            <?php if ($message_success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message_success) ?></div>
            <?php endif; ?>

            <!-- Форма добавления комментария -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="comment">Ваш комментарий:</label>
                        <textarea name="comment" id="comment" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить комментарий</button>
                </form>
            <?php else: ?>
                <p>Войдите в систему, чтобы оставить комментарий.</p>
                <a href="login.php" class="btn btn-primary">Войти</a>
            <?php endif; ?>

            <!-- Отображение комментариев -->
            <h3 class="mt-4">Комментарии:</h3>
            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <p>Комментариев пока нет.</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <strong><?= htmlspecialchars($comment['full_name']) ?></strong>
                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                            <small><?= date('M d, Y H:i', strtotime($comment['created_at'])) ?></small>

                            <!-- Проверка, является ли текущий пользователь автором комментария -->
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_comment_id" value="<?= $comment['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>




