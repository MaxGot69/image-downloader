<?php
// Чтение ссылок из файла
$links = file('links.txt', FILE_IGNORE_NEW_LINES);

// Выводим количество ссылок для удобства
echo "Запуск: " . date('Y-m-d H:i:s') . " Количество ссылок: " . count($links) . "\n";

// Обрабатываем каждую ссылку
foreach ($links as $url) {
    // Инициализация cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true); // Нам не нужно тело ответа
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Следуем за редиректами
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Таймаут запроса

    // Выполняем запрос
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Проверяем успешность запроса
    if ($response && $httpCode >= 200 && $httpCode < 400) {
        echo "Ссылка доступна: $url\n";
    } else {
        echo "Ошибка: ссылка недоступна: $url\n";
    }
}

// Выводим время завершения
echo "Завершено: " . date('Y-m-d H:i:s') . "\n";


