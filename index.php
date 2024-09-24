<?php
require 'vendor/connect.php';
require 'vendor/helpers.php';
session_start();

$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;





// Запрос для получения данных статей и авторов
$sql = "SELECT post.id, post.title, post.text, post.created_at, users.full_name, users.profile_image 
        FROM post 
        JOIN users ON post.author = users.id";
$result = mysqli_query($conn, $sql);

// Проверка выполнения запроса
if (!$result) {
    echo "Ошибка при выполнении запроса: " . mysqli_error($conn);
} else {
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Запрос для получения категорий с наибольшим количеством комментариев (максимум 4 категории)
$category_comments_sql = "
    SELECT category.title AS category_title, COUNT(comments.id) AS comments_count 
    FROM comments
    JOIN post ON comments.post_id = post.id
    JOIN category ON post.category = category.id
    GROUP BY category.id
    ORDER BY comments_count DESC
    LIMIT 6";
    
$category_comments_result = mysqli_query($conn, $category_comments_sql);

if (!$category_comments_result) {
    echo "Ошибка при выполнении запроса категорий: " . mysqli_error($conn);
} else {
    $top_categories = mysqli_fetch_all($category_comments_result, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/reset.css">
        <link rel="stylesheet" href="css/style.css">
        <title>Document</title>
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
                <a href="#"><img src="/icons/Uppercase text.svg" alt=""></a>
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
    <div class="container second-section__container">
        <nav class="second-section__nav nav-menu">
            <ul class="nav-menu__list">
                <a href="#"><li class="nav-menu__list-item">News</li></a>
                <a href="#"><li class="nav-menu__list-item">Opinion</li></a>
                <a href="#"><li class="nav-menu__list-item">Science</li></a>
                <a href="#"><li class="nav-menu__list-item">Life</li></a>
                <a href="#"><li class="nav-menu__list-item">Travel</li></a>
                <a href="#"><li class="nav-menu__list-item">Moneys</li></a>
                <a href="#"><li class="nav-menu__list-item">Art & Design</li></a>
                <a href="#"><li class="nav-menu__list-item">Sports</li></a>
                <a href="#"><li class="nav-menu__list-item">People</li></a>
                <a href="#"><li class="nav-menu__list-item">Health</li></a>
                <a href="#"><li class="nav-menu__list-item">Education</li></a>
                
            </ul>
        </nav>
    </div>

    <div class="container second-section__box-container">
        <div class="second-section__card-box">

            <!-- 1 карточка -->

            <div class="card-box__element">
                <div class="card-box__item-left">
                    <p class="second-section__text text_card">25 Songs That Tell Us Where Music Is Going</p>
                </div>

                <div class="card-box__item-right">
                    <img src="/img/image-173.png" alt="">
                </div>

                <hr class="line-card-box">
            </div>

            <!-- 2 карточка -->

            <div class="card-box__element">
                <div class="card-box__item-left">
                    <p class="second-section__text text_card">
                        These Ancient Assassins Eat Their Own Kind
                    </p>
                </div>

                <div class="card-box__item-right">
                    <img src="/img/Image.png" alt="">
                </div>

                <hr class="line-card-box">
            </div>


            <!-- 3 карточка -->

            <div class="card-box__element">
                <div class="card-box__item-left">
                    <p class="second-section__text text_card">
                        How Do You Teach People to Love Difficult Music?
                    </p>
                </div>

                <div class="card-box__item-right">
                    <img src="/img/Image3.png" alt="">
                </div>

                <hr class="line-card-box">
            </div>


            <!-- 3 карточка -->

            <div class="card-box__element">
                <div class="card-box__item-left">
                    <p class="second-section__text text_card">
                        International Soccer’s Man of Mystery
                    </p>
                </div>

                <div class="card-box__item-right">
                    <img src="/img/Image4.png" alt="">
                </div>

                <hr class="line-card-box">
            </div>

        </div>
    </div>


    <div class="container second-section__news-container">
        <div class="second-section__article">
            <div class="second-section__author">
                <img src="/img/Avatar.png" alt="Author image" class="second-section__author-photo" height="45px" width="45px">
                <div class="second-section__author-wrapper ">
                    <span class="second-section__author-name text_author">By Benjamin Turner</span>
                    <span class="second-section__author-role sub_author">Traveler</span>
                </div>
            </div>

            <div class="second-section__content">
                <div class="second-section__category">
                    <span class="second-section__category-text sub_blue">Destinations</span>
                </div>
                <h1 class="second-section__title title_fnt40">
                    In Southeast England, White Cliffs and Fish
                </h1>
                <div class="test">
                    <button type="submit" class="second-section__link-button">
                        Read more <img src="img/Arrow-right-big.svg" alt="">
                    </button>
                    <div class="second-section__video">
                        <img src="icons/Play.svg" alt="The chalk cliff of Beachy Head" class="second-section__video-thumbnail">
                        <div class="second-section__video-wrapper">
                            <span class="second-section__video-duration-title text_author">
                                The chalk cliff of Beachy Head
                            </span>
                            <span class="second-section__video-duration sub_author">18:05</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <aside class="second-section__sidebar">
            <div class="second-section__recommendations">
                <h3 class="second-section__recommendations-title title_black">Recommended for You</h3>
                <ul class="second-section__recommendations-list">
                    <li class="second-section__recommendation-item">
                        <span class="second-section__recommendation-category text_green">Food</span>
                        <a href="#" class="second-section__recommendation-link text_author text_author-black">For Chicken-Fried Steak, Too Much Is Just Enough</a>
                    </li>

                    <li class="second-section__recommendation-item">
                        <span class="second-section__recommendation-category text_green">Cars</span>
                        <a href="#" class="second-section__recommendation-link text_author text_author-black">Storm Has Car Dealers Doing Swift Business</a>
                    </li>

                    <li class="second-section__recommendation-item">
                        <span class="second-section__recommendation-category text_green">Movies</span>
                        <a href="#" class="second-section__recommendation-link text_author text_author-black">War Is Hell? In New Military Dramas, It's One-Dimensional</a>
                    </li>

                    <li class="second-section__recommendation-item">
                        <span class="second-section__recommendation-category text_green">NFL</span>
                        <a href="#" class="second-section__recommendation-link text_author text_author-black">11 surprising stat rankings for active NFL players</a>
                    </li>

                    <li class="second-section__recommendation-item">
                        <span class="second-section__recommendation-category text_green">Tech Reviews</span>
                        <a href="#" class="second-section__recommendation-link text_author text_author-black">The bookcases you can buy online and assemble yourself</a>
                    </li>

                </ul>
            </div>
        </aside>
    </div>


    </div>
</section>

<section class="third-section">
    <div class="container third-section__container">
        <div class="third-section__content-wrapper">

            <!-- Карточки -->
            <div class="third-section__card-container">
             

                <?php foreach ($posts as $post): ?>
                    <div class="third-section__card card__news">
                        <div class="third-section__author-wrapper">
                            <img src="<?= htmlspecialchars($post['profile_image']) ?>" alt="Author image" class="second-section__author-photo" height="50px" width="50px">
                            <div class="author-wrapper_card">
                                <span class="second-section__author-name text">
                                    By <?= htmlspecialchars($post['full_name']) ?>
                                </span>
                            </div>
                        </div>
                        <h3 class="third-section__title subtitle">
                            <?= htmlspecialchars($post['title']) ?>
                        </h3>
                        <p class="third-section__text text">
                        <?= htmlspecialchars(implode(' ', array_slice(explode(' ', $post['text']), 0, 15))) . (str_word_count($post['text']) > 15 ? '...' : '...') ?>
                        </p>
                        <a href="comments.php?post_id=<?= $post['id'] ?>" class="btn btn-primary">
                            <img src="icons/chat-dots.svg" alt="">
                        </a>
                        <p class="third-section__date text_date">
                            <?= date('M d', strtotime($post['created_at'])) ?>
                        </p>
                       
                    </div>
                <?php endforeach; ?>

            </div>

            <!-- Боковая колонка -->
            <div class="third-section__aside-container">
                <div class="third-section__aside aside__card">
                    <h3 class="third-section__titlle text_white">Top Categories by Comments</h3>
                </div>
                <hr>
                <?php if (!empty($top_categories)): ?>
                    <?php foreach ($top_categories as $category): ?>
                        <div class="third-section__aside aside__card">
                            <p class="third-section__text text_aside">
                                <?= htmlspecialchars($category['category_title']) ?>
                                (<?= htmlspecialchars($category['comments_count']) ?> comments)
                            </p>
                        </div>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="third-section__aside aside__card">
                        <p class="third-section__text text_aside">No categories available</p>
                    </div>
                    <hr>
                <?php endif; ?>
                <div class="third-section__aside aside__card">
                    <a href="#" class="third-section__titlle text_white">Read More</a>
                </div>
            </div>

        </div>
    </div>
</section>


<footer class="footer">
  <div class="footer__subscribe">
    <h2 class="footer__title">Subscribe now and get 20% off</h2>
    <form class="footer__form">
      <input type="email" class="footer__input" placeholder="Enter your email" />
      <button type="submit" class="footer__button">Subscribe</button>
    </form>
  </div>
  <div class="footer__menu">

    <div class="footer__column footer__column--news">
      <h3 class="footer__column-title">News</h3>
      <ul class="footer__list">
        <li class="footer__item"><a href="#" class="footer__link">Nation</a></li>
        <li class="footer__item"><a href="#" class="footer__link">World</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Politics</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Solar Eclipse</a></li>
      </ul>
    </div>

    <div class="footer__column footer__column--arts">
      <h3 class="footer__column-title">Arts</h3>
      <ul class="footer__list">
        <li class="footer__item"><a href="#" class="footer__link">Art & Design</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Movies</a></li>
        <li class="footer__item"><a href="#" class="footer__link">People</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Video: Arts</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Theater</a></li>
      </ul>
    </div>

    <div class="footer__column footer__column--arts">
      <h3 class="footer__column-title">Arts</h3>
      <ul class="footer__list">
        <li class="footer__item"><a href="#" class="footer__link">Art & Design</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Movies</a></li>
        <li class="footer__item"><a href="#" class="footer__link">People</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Video: Arts</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Theater</a></li>
      </ul>
    </div>

    <div class="footer__column footer__column--arts">
      <h3 class="footer__column-title">Arts</h3>
      <ul class="footer__list">
        <li class="footer__item"><a href="#" class="footer__link">Art & Design</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Movies</a></li>
        <li class="footer__item"><a href="#" class="footer__link">People</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Video: Arts</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Theater</a></li>
      </ul>
    </div>

    <div class="footer__column footer__column--arts">
      <h3 class="footer__column-title">Arts</h3>
      <ul class="footer__list">
        <li class="footer__item"><a href="#" class="footer__link">Art & Design</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Movies</a></li>
        <li class="footer__item"><a href="#" class="footer__link">People</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Video: Arts</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Theater</a></li>
      </ul>
    </div>

    <div class="footer__column footer__column--arts">
      <h3 class="footer__column-title">Arts</h3>
      <ul class="footer__list">
        <li class="footer__item"><a href="#" class="footer__link">Art & Design</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Movies</a></li>
        <li class="footer__item"><a href="#" class="footer__link">People</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Video: Arts</a></li>
        <li class="footer__item"><a href="#" class="footer__link">Theater</a></li>
      </ul>
    </div>

  </div>
  <div class="footer__bottom">
    <div class="footer__links">
      <a href="#" class="footer__bottom-link">Contact Us</a>
      <a href="#" class="footer__bottom-link">Work with Us</a>
      <a href="#" class="footer__bottom-link">Advertise</a>
      <a href="#" class="footer__bottom-link">Your Ad Choice</a>
    </div>
    <div class="footer__social">
      <a href="#" class="footer__social-link"><img src="facebook-icon.png" alt="Facebook" /></a>
      <a href="#" class="footer__social-link"><img src="twitter-icon.png" alt="Twitter" /></a>
      <a href="#" class="footer__social-link"><img src="youtube-icon.png" alt="YouTube" /></a>
      <a href="#" class="footer__social-link"><img src="instagram-icon.png" alt="Instagram" /></a>
    </div>
  </div>
</footer>


<script src="js/bootstrap.bundle.js"></script>
</body>
</html>
