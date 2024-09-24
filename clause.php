<?php
require 'vendor/connect.php';
require_once 'vendor/helpers.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = $userID";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$category_sql = "SELECT * FROM category";
$result_categories = mysqli_query($conn, $category_sql);

if (!$result_categories) {
    echo "Ошибка при загрузке категорий: " . mysqli_error($conn);
}

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = $_POST['news_category'];
    $title = $_POST['news_name'];
    $content = $_POST['news_content'];

    $category_sql = "SELECT id FROM category WHERE id = '$category'";
    $category_result = mysqli_query($conn, $category_sql);
    
    if (mysqli_num_rows($category_result) > 0) {
        $category_row = mysqli_fetch_assoc($category_result);
        $category_id = $category_row['id'];
    } else {
        echo "Ошибка: выбранная категория не существует.";
    }

    $insert_post_sql = "INSERT INTO post (title, text, category, author) 
                        VALUES ('$title', '$content', '$category_id', '$userID')";

    if (mysqli_query($conn, $insert_post_sql)) {
        $message_success = 'Статья успешно добавлена';
    } else {
        $message_success = "Ошибка при добавлении статьи: " . mysqli_error($conn);
    }
}
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
                    <form action="vendor/logout.php" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-none">
                            <img src="icons/Man.svg" alt="Profile Icon"> Logout
                        </button>
                    </form>
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
            <form action="clause.php" method="POST" class="form__clause">

                <?php if (isset($message_success)): ?>
                    <div class="alert alert-info">
                        <?= htmlspecialchars($message_success) ?>
                    </div>
                <?php endif; ?>

            <form action="clause.php" method="POST" class="form__clause">
                <div class="form-group">
                    <label for="news_category" class="form-label">Категория статьи</label>
                    <select name="news_category" class="form-control" required>
                        <option value="" disabled selected>Выберите категорию</option>
                        <?php while ($category = mysqli_fetch_assoc($result_categories)): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['title']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="news_name" class="form-label">Название статьи</label>
                    <input type="text" class="form-control" name="news_name" placeholder="Введите название статьи" required>
                </div>

                <div class="form-group">
                    <label for="news_content" class="form-label">Содержание статьи</label>
                    <textarea class="form-control" name="news_content" placeholder="Введите содержание статьи" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
    </div>
</section>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>




