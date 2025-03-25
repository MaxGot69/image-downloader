<?php
class ImageSaver {
    private $savePath;

    public function __construct($savePath) {
        $this->savePath = $savePath;
    }

    public function saveImage($url) {
        $imageName = basename($url);
        $saveTo = $this->savePath . '/' . $imageName;

        if (!file_exists($saveTo)) {
            $imageData = file_get_contents($url);
            if ($imageData) {
                file_put_contents($saveTo, $imageData);
                return true;
            }
        }
        return false; // Если файл уже существует, пропускаем
    }
}
