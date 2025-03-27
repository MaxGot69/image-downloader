<?php
// Файл логов
$logFile = 'Image_download_log.txt';
// Файл ошибок
$errorLogFile = 'errors.log';
// Файл с ссылками на изображения
$linksFile = 'links.txt';

// Проверяем существование файла с ссылками
if (!file_exists($linksFile)) {
    echo "Ошибка: файл с ссылками не найден!\n";
    exit();
}

// Получаем все ссылки из файла
$links = file($linksFile, FILE_IGNORE_NEW_LINES);
if ($links === false) {
    echo "Ошибка: не удалось прочитать файл с ссылками!\n";
    exit();
}

// Функция для записи в лог
function writeToLog($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "$timestamp - $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Функция для проверки доступности ссылки
function checkLink($url) {
    $headers = get_headers($url, 1);
    return $headers ? $headers[0] : false;
}

// Функция для загрузки файла
function downloadFile($url, $logFile, $errorLogFile) {
    $headers = get_headers($url, 1);
    if (strpos($headers[0], '200') !== false) {
        // Получаем имя файла
        $filename = basename($url);
        $fileData = file_get_contents($url);
        if ($fileData) {
            file_put_contents($filename, $fileData);
            writeToLog("Ссылка доступна: $url", $logFile);
        } else {
            writeToLog("Ошибка: не удалось скачать файл с $url", $errorLogFile);
        }
    } else {
        writeToLog("Ошибка: ссылка недоступна: $url (код ответа: $headers[0])", $errorLogFile);
    }
}

// Запускаем процесс
echo "Запуск: " . date('Y-m-d H:i:s') . "\n";
writeToLog("Запуск: " . date('Y-m-d H:i:s'), $logFile);

foreach ($links as $link) {
    $link = trim($link); // Убираем лишние пробелы
    if (!empty($link)) {
        echo "Ссылка: $link | Код ответа: " . checkLink($link) . "\n";
        downloadFile($link, $logFile, $errorLogFile);
    }
}

echo "Завершено: " . date('Y-m-d H:i:s') . "\n";
writeToLog("Завершено: " . date('Y-m-d H:i:s'), $logFile);
?>
