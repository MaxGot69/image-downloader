<?php


function check_url($url) {
    $headers = @get_headers($url);
    return strpos($headers[0], '200') !== false;  
}

function log_error($message) {
    file_put_contents('logs/errors.log', $message . PHP_EOL, FILE_APPEND);
}


function download_image($url, $save_path) {
    
    if (!check_url($url)) {
        log_error("Недоступна ссылка: $url");
        echo "Ошибка: ссылка недоступна: $url\n";
        return;
    }

    if (file_exists($save_path)) {
        echo "Файл уже существует: $save_path\n";
        return;
    }

    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    
    if ($data === false) {
        log_error("Ошибка скачивания с URL $url: " . curl_error($ch));
        echo "Ошибка скачивания: $url\n";
    } else {
        file_put_contents($save_path, $data);
        echo "Скачано: $url\n";
    }
    
    curl_close($ch);
}


function load_links($filename) {
    if (!file_exists($filename)) {
        log_error("Файл с ссылками не найден: $filename");
        return [];
    }

    return file($filename, FILE_IGNORE_NEW_LINES);
}


function download_images_from_links($links) {
    $saved_count = 0;
    $skipped_count = 0;
    $failed_count = 0;

    foreach ($links as $link) {
        $filename = basename($link);
        $save_path = 'images/' . $filename;

        download_image($link, $save_path);

        if (file_exists($save_path)) {
            $saved_count++;
        } else {
            $failed_count++;
        }

        
        if (!file_exists($save_path) && $failed_count == 0) {
            $skipped_count++;
            log_error("Пропущено: $link");
        }
    }

    echo "Завершение: Скачано: $saved_count, Пропущено: $skipped_count, Ошибок: $failed_count\n";
}


function main() {
    $links = load_links('links.txt');
    if (empty($links)) {
        echo "Нет ссылок для скачивания.\n";
        return;
    }

    $start_time = date('Y-m-d H:i:s');
    echo "Запуск: $start_time\n";
    echo "Количество ссылок: " . count($links) . "\n";

    
    download_images_from_links($links);

    $end_time = date('Y-m-d H:i:s');
    echo "Завершение: $end_time\n";
}


main();

