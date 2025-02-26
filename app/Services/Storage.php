<?php

namespace App\Services;

use Core\Constants\Constants;
use Core\Database\ActiveRecord\Model;

class Storage
{
    public function __construct(private string $dir) {}

    public function upload($file, string $name): string
    {
        move_uploaded_file($file["tmp_name"], $this->storeDir() . $name);

        return $this->baseDir() . $name . '?' . md5_file($this->storeDir() . $name);
    }

    public function delete(string $name): bool
    {
        return unlink($this->storeDir() . "$name");
    }

    private function baseDir(): string
    {
        return "/assets/uploads/{$this->dir}/";
    }

    private function storeDir(): string
    {
        $path = Constants::rootPath()->join('public' . $this->baseDir());
        if (!is_dir($path)) {
            mkdir(directory: $path, recursive: true);
        }

        return $path;
    }
}
