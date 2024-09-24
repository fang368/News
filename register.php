<?php
require 'vendor/connect.php';
require 'vendor/helpers.php';
session_start();
$message_error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $role_id_user = 1;

    if (strlen($password) < 6) {
        $message_error = 'Длина пароля должна быть более 6 символов';
    }

    // Проверка уникальности логина
    $check_login = "SELECT id FROM users WHERE login = '$login'";
    $check_login_result = mysqli_query($conn, $check_login);
    if (mysqli_num_rows($check_login_result) > 0) {
        $message_error .= " Данный пользователь уже зарегистрирован с этим логином";
    }

    // Проверка уникальности email
    $check_email = "SELECT id FROM users WHERE email = '$email'";
    $check_email_result = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($check_email_result) > 0) {
        $message_error .= " Данный пользователь уже зарегистрирован с этим E-mail";
    }

    // Проверка уникальности телефона
    $check_phone = "SELECT id FROM users WHERE phone = '$phone'";
    $check_phone_result = mysqli_query($conn, $check_phone);
    if (mysqli_num_rows($check_phone_result) > 0) {
        $message_error .= " Данный пользователь уже зарегистрирован с этим телефоном";
    }

    if (empty($message_error)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO users (login, full_name, password, phone, email) VALUES ('$login', '$full_name', '$hashed_password', '$phone', '$email')";

        if (mysqli_query($conn, $insert_sql)) {
            header('location: login.php');
            exit();
        } else {
            $message_error = 'Ошибка при регистрации: ' . mysqli_error($conn);
        }
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
            <form action="register.php" method="POST" class="form__container-main">

                <?php if(!empty($message_error)) :?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message_error; ?>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="full_name" class="form-label">ФИО</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Введите пароль" require>
                </div>

                <div class="form-group">
                    <label for="login" class="form-label">Логин</label>
                    <input type="text" name="login" class="form-control" placeholder="Введите логин" require>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="tel" name="phone" class="form-control" placeholder="Введите номер телефона" require>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" placeholder="Введите почту" require>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" placeholder="Введите пароль" require>
                </div>


                <div class="form-group">
                    <a href="login.php">Уже есть аккаунт? - Авторизоваться</a>
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
