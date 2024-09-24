<?php
require 'vendor/connect.php';
require_once 'vendor/helpers.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location: login.php');
    exit();
}

$userID = $_SESSION['user_id'];

// Обработка удаления статьи
if (isset($_POST['delete_post_id'])) {
    $postIdToDelete = $_POST['delete_post_id'];
    $delete_sql = "DELETE FROM post WHERE id = $postIdToDelete AND author = $userID";
    if (mysqli_query($conn, $delete_sql)) {
        $message_success = "Статья успешно удалена.";
    } else {
        $message_error = "Ошибка при удалении статьи: " . mysqli_error($conn);
    }
}

// Получение статей пользователя
$posts_sql = "SELECT p.*, c.title AS category_title FROM post p JOIN category c ON p.category = c.id WHERE p.author = $userID";
$result_posts = mysqli_query($conn, $posts_sql);

$category_sql = "SELECT * FROM category";
$result_categories = mysqli_query($conn, $category_sql);

// Обработка редактирования статьи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_post_id'])) {
    $postId = $_POST['post_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];

    if (empty($title) || empty($content) || empty($category)) {
        $message_error = "Пожалуйста, заполните все поля.";
    } else {
        $update_sql = "UPDATE post SET title = '$title', text = '$content', category = '$category' WHERE id = $postId AND author = $userID";
        if (mysqli_query($conn, $update_sql)) {
            $message_success = "Статья успешно обновлена.";
        } else {
            $message_error = "Ошибка: " . mysqli_error($conn);
        }
    }
}

$hasPosts = mysqli_num_rows($result_posts) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Редактирование Статей</title>
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
            <?php if (isset($message_success)): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($message_success) ?>
                </div>
            <?php endif; ?>
            <?php if (isset($message_error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($message_error) ?>
                </div>
            <?php endif; ?>

        <h2>Ваши статьи</h2>
        <div class="articles-container">
            <?php if ($hasPosts): ?>
                <?php while ($post = mysqli_fetch_assoc($result_posts)): ?>
                    <div class="article-card">
                        <h3 class="title__arcticle"><?= htmlspecialchars($post['title']) ?></h3>
                        <p>
                            <strong>Категория:</strong> 
                            <?= htmlspecialchars($post['category_title']) ?>
                        </p>
                        <p class="arcticle__card-text">
                            <?= htmlspecialchars($post['text']) ?>
                        </p>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $post['id'] ?>">Редактировать</button>

                        <div class="modal fade" id="editModal<?= $post['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Редактирование статьи</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="clause_edit.php" method="POST">
                                            <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Название статьи</label>
                                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="content" class="form-label">Содержание статьи</label>
                                                <textarea name="content" class="form-control" required><?= htmlspecialchars($post['text']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="category" class="form-label">Категория</label>
                                                <select name="category" class="form-control" required>
                                                    <option value="" disabled>Выберите категорию</option>
                                                    <?php
                                                    mysqli_data_seek($result_categories, 0); // Сброс указателя результата категорий
                                                    while ($category = mysqli_fetch_assoc($result_categories)): ?>
                                                        <option value="<?= htmlspecialchars($category['id']) ?>" <?= $post['category'] == $category['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($category['title']) ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Обновить статью</button>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $post['id'] ?>">Удалить</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="deleteModal<?= $post['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Вы уверены, что хотите удалить статью "<?= htmlspecialchars($post['title']) ?>"?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="clause_edit.php" method="POST">
                                            <input type="hidden" name="delete_post_id" value="<?= htmlspecialchars($post['id']) ?>">
                                            <button type="submit" class="btn btn-danger">Удалить</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Пока что вы не оставили статью.</p>
            <?php endif; ?>
        </div>
        </div>
    </div>
</section>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
