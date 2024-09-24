<?php
require_once 'connect.php';

// Функция для получения данных о погоде по названию города
function getWeatherByCity($city) {
    $apiKey = 'dcfe0ffb1628b7aed4517b056396f3c3'; // Ваш API ключ
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$city}&units=metric&appid={$apiKey}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $weatherData = json_decode($response, true);

    if (isset($weatherData['main'])) {
        return [
            'temp' => $weatherData['main']['temp'],
            'description' => $weatherData['weather'][0]['description'],
            'icon' => $weatherData['weather'][0]['icon']
        ];
    } else {
        return ['error' => 'Не удалось получить данные о погоде для этого города'];
    }
}

// Функция для вывода погоды с кешированием
function displayWeather($city = 'Ufa') {
    $cacheFile = 'weather_cache.json'; 
    $cacheTime = 3600; 

    // Проверяем, существует ли файл кеша и не истек ли срок его действия
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        // Читаем данные из кеша
        $weather = json_decode(file_get_contents($cacheFile), true);
    } else {
        // Получаем новые данные о погоде
        $weather = getWeatherByCity($city);
        
        // Сохраняем данные в кеш
        if (!isset($weather['error'])) {
            file_put_contents($cacheFile, json_encode($weather));
        }
    }

    if (isset($weather['error'])) {
        echo '<div class="error-message">' . $weather['error'] . '</div>';
    } else {
        $temperature = round($weather['temp']); 
        $icon = $weather['icon'];

        echo '
        <div class="main-section__weather" id="weather">
            <img src="http://openweathermap.org/img/wn/' . $icon . '.png" alt="weather icon" height="23px">
            <p class="main-section__text weather_text">' . $temperature . ' °C</p>
        </div>';
    }
}
?>
