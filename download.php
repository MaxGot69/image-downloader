<?php

// Файл для записи логов с текущей датой и временем
$logFile = 'logs/log_' . date('Y-m-d_H-i-s') . '.txt';

// Массив ссылок на изображения
$urls = [
    "http://sp.vseproprognozy.ru/upload/iblock/36a/tgwbrsik6v3mynvm8k1p6y6gb9wcz2rh.webp",
    "http://sp.vseproprognozy.ru/upload/iblock/43a/adefj56re4kjewwwm02mgjszsloh67564gf.webp",
    // Добавь сюда другие ссылки
];

// Папка для сохранения изображений
$downloadFolder = 'downloads';

// Создаем папку для скачивания, если ее нет
if (!file_exists($downloadFolder)) {
    mkdir($downloadFolder, 0777, true);
}

// Логирование начала выполнения
file_put_contents($logFile, "Запуск: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Функция для скачивания файла
function downloadImage($url, $logFile, $downloadFolder)
{
    $fileName = basename($url);
    $filePath = $downloadFolder . DIRECTORY_SEPARATOR . $fileName;

    // Проверяем, был ли файл уже скачан
    if (file_exists($filePath)) {
        file_put_contents($logFile, "Файл уже существует: $url\n", FILE_APPEND);
        return;
    }

    // Получаем код ответа сервера
    $headers = get_headers($url, 1);
    $httpCode = isset($headers[0]) ? $headers[0] : 'No Response';

    // Логируем код ответа
    file_put_contents($logFile, "Ссылка: $url | Код ответа: $httpCode\n", FILE_APPEND);

    // Если код ответа 200, скачиваем файл
    if ($httpCode == 'HTTP/1.1 200 OK') {
        $imageData = file_get_contents($url);
        if ($imageData === false) {
            file_put_contents($logFile, "Ошибка скачивания: $url\n", FILE_APPEND);
        } else {
            file_put_contents($filePath, $imageData);
            file_put_contents($logFile, "Скачано: $url\n", FILE_APPEND);
        }
    } else {
        file_put_contents($logFile, "Ошибка: $url (код $httpCode)\n", FILE_APPEND);
    }
}

// Обрабатываем каждую ссылку
foreach ($urls as $url) {
    downloadImage($url, $logFile, $downloadFolder);
}

// Логируем завершение работы
file_put_contents($logFile, "Завершено: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
