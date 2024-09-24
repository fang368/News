<?php
require 'vendor/connect.php';
require_once 'vendor/helpers.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit();
}

$message_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE login = '$login'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: profile.php');
            exit();
        } else {
            $message_error = "Некорректные данные";
        }
    } else {
        $message_error = "Пользователь с таким логином не найден";
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
                <button class="btn btn-none">
                    <img src="icons/Man.svg" alt="Man"> Sign In
                </button>
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

<section class="second-section">
    <div class="container main-form__container">
        <div class="main-form-wrapper">
            <form action="login.php" method="POST" class="form__container-main">

                <?php if (!empty($message_error)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message_error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="login" class="form-label">Логин</label>
                    <input type="text" name="login" class="form-control" placeholder="Введите логин" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" placeholder="Введите пароль" required>
                </div>

                <div class="form-group">
                    <a href="register.php">Еще нет аккаунта? - зарегистрироваться</a>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn_max-width">Войти</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
