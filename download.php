<?php

// Устанавливаем время выполнения скрипта без ограничений
set_time_limit(0);

// Открываем файл с ссылками
$file = 'links.txt';
$logFile = 'Image_download_log.txt';
$errorLogFile = 'errors.log';

$links = file($file, FILE_IGNORE_NEW_LINES); // Считываем ссылки из файла
$count = count($links);

// Логирование запуска
file_put_contents($logFile, "Запуск: " . date("Y-m-d H:i:s") . " Количество ссылок: $count\n", FILE_APPEND);

// Процесс скачивания изображений
foreach ($links as $link) {
    // Проверяем доступность ссылки
    $headers = get_headers($link, 1);

    if (strpos($headers[0], '200') === false) {
        // Записываем недоступную ссылку в error.log
        file_put_contents($errorLogFile, "Недоступна ссылка: $link\n", FILE_APPEND);
        file_put_contents($logFile, "Ошибка: ссылка недоступна: $link\n", FILE_APPEND);
        continue; // Переходим к следующей ссылке
    }

    // Пытаемся скачать изображение
    $imageData = file_get_contents($link);

    if ($imageData === false) {
        // Записываем ошибку, если не удалось скачать
        file_put_contents($errorLogFile, "Не удалось скачать изображение с: $link\n", FILE_APPEND);
        file_put_contents($logFile, "Ошибка: не удалось скачать изображение с: $link\n", FILE_APPEND);
    } else {
        // Генерируем имя файла на основе URL
        $imageName = basename($link);
        file_put_contents($logFile, "Скачано: $link\n", FILE_APPEND);

        // Сохраняем изображение в папке "downloads"
        file_put_contents('downloads/' . $imageName, $imageData);
    }
}

file_put_contents($logFile, "Завершено: " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);

