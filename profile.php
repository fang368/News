<?php
require 'vendor/connect.php';
require_once 'vendor/helpers.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = $userID";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$message_error = '';
$message_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['btn_delete'])) {
        $delete_query = "DELETE FROM users WHERE id = $userID"; 
        if (mysqli_query($conn, $delete_query)) {
            session_destroy(); 
            header('location: login.php');
            exit();
        } else {
            $message_error = 'Ошибка удаления аккаунта';
        }
    } else {
        $full_name = trim($_POST['full_name']);
        $login = trim($_POST['login']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $profile_image = $_FILES['profile_image'];


        if (substr_count($full_name, ' ') !== 2) {
            $message_error = "Неккоректное ФИО";
        }

        if (!preg_match("/^[a-zA-Z0-9_]{3,}$/", $login)) {
            $message_error = "Логин должен содержать не менее 3 символов и состоять только из букв, цифр и подчеркиваний";
        }

        $login_query = "SELECT id FROM users WHERE login = '$login' AND id != $userID"; 
        $login_result = mysqli_query($conn, $login_query);
        if (mysqli_num_rows($login_result) > 0) {
            $message_error = "Логин уже существует";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message_error = "Некорректный формат электронной почты";
        }

        $email_query = "SELECT id FROM users WHERE email = '$email' AND id != $userID";
        $email_result = mysqli_query($conn, $email_query);
        if (mysqli_num_rows($email_result) > 0) {
            $message_error = "Электронная почта уже существует";
        }

        if (!preg_match("/^\+?[0-9]{11,}$/", $phone)) {
            $message_error = "Некорректный номер телефона";
        }

        if (!empty($password) || !empty($password_confirm)) {
            if ($password !== $password_confirm) {
                $message_error = "Пароли не совпадают";
            } elseif (strlen($password) < 6) {
                $message_error = "Длина пароля должна быть не менее 6 символов";
            }
        }

        if (!empty($profile_image['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($profile_image['type'], $allowed_types)) {
                $message_error = "Недопустимый формат изображения. Разрешены только JPEG, PNG и GIF.";
            } else {
                $upload_dir = 'uploads/';
                $target_file = $upload_dir . basename($profile_image['name']);
                if (!move_uploaded_file($profile_image['tmp_name'], $target_file)) {
                    $message_error = "Ошибка загрузки изображения.";
                }
            }
        }

    
        if (empty($message_error)) {
            $update_query = "UPDATE users SET "; 
            $update_fields = [];

            if ($full_name !== $user['full_name']) {
                $update_fields[] = "full_name = '$full_name'";
            }
            if ($login !== $user['login']) {
                $update_fields[] = "login = '$login'";
            }
            if ($email !== $user['email']) {
                $update_fields[] = "email = '$email'";
            }
            if ($phone !== $user['phone']) {
                $update_fields[] = "phone = '$phone'";
            }
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_fields[] = "password = '$hashed_password'";
            }
            if (!empty($profile_image['name'])) {
                $update_fields[] = "profile_image = '$target_file'";
            }

            if (!empty($update_fields)) {
                $update_query .= implode(', ', $update_fields) . " WHERE id = $userID";
                if (mysqli_query($conn, $update_query)) {
                    $message_success = 'Профиль успешно обновлен';
 
                    $_SESSION['user_name'] = $full_name;
                    $_SESSION['user_login'] = $login;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_phone'] = $phone;
                    if (!empty($profile_image['name'])) {
                        $_SESSION['user_profile_image'] = $target_file;
                    }
                    header('location: profile.php');
                } else {
                    $message_error = 'Ошибка обновления данных';
                }
            } else {
                $message_success = 'Нет изменений для обновления';
            }
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
        <form action="profile.php" method="POST" class="form__login-box" enctype="multipart/form-data">
                <?php if(!empty($message_error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message_error; ?>
                    </div>
                <?php endif; ?>
                <?php if(!empty($message_success)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message_success; ?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($user['profile_image'])): ?>
                <div class="form-group">
                    <label for="current_profile_image" class="form-label">Изображение профиля</label><br>
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Фото профиля" class="img_profile" >
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="full_name" class="form-label">ФИО</label>
                    <input class="form-control" type="text" name="full_name" placeholder="Иванов Иван Иванович" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                </div>

                <div class="form-group">
                    <label for="login" class="form-label">Логин</label>
                    <input class="form-control" type="text" name="login" placeholder="Введите логин" value="<?php echo htmlspecialchars($user['login']); ?>">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Почта</label>
                    <input class="form-control" type="email" name="email" placeholder="Введите почту" value="<?php echo htmlspecialchars($user['email']); ?>">
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Телефон</label>
                    <input class="form-control" type="tel" name="phone" placeholder="Введите телефон" value="<?php echo htmlspecialchars($user['phone']); ?>">
                </div>

                <div class="form-group">
                    <label for="profile_image" class="form-label">Фото профиля</label>
                    <input class="form-control" type="file" name="profile_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Пароль</label>
                    <input class="form-control" type="password" name="password" placeholder="Введите пароль">
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Подтверждение пароля</label>
                    <input class="form-control" type="password" name="password_confirm" placeholder="Введите пароль">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn_login">Обновить</button>
                </div>

                <div class="form-group__button">
                    <button type="submit" class="btn_delete" name="btn_delete">Удалить аккаунт</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>




