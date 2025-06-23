<?php

namespace App;

class OrderFileLoader
{
    public function __construct(
        private string $filePath
    ) {
    }


    /**
     * @return array
     */
    public function load(): array
    {
        $this->validateFile();

        $content = file_get_contents($this->filePath);
        if ($content === false) {
            throw new \RuntimeException("Ошибка загрузки файла {$this->filePath}");
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Некорректный JSON: " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * @return void
     */
    private function validateFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException("Файл {$this->filePath} отсутствует");
        }

        if (!is_readable($this->filePath)) {
            throw new \RuntimeException("Файл {$this->filePath} невозможно прочитать");
        }
    }
}