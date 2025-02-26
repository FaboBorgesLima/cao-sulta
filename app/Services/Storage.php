<?php

namespace App\Services;

use Core\Constants\Constants;
use Core\Database\ActiveRecord\Model;
use Error;
use Exception;

class Storage
{
    public function __construct(private string $dir) {}

    public function upload(mixed $file, string $name): string
    {
        move_uploaded_file($file["tmp_name"], $this->storeDir() . $name . '.png');

        return $this->baseDir() . $name . '.png?' . md5_file($this->storeDir() . $name . '.png');
    }

    public function delete(string $name): bool
    {
        try {
            $result = unlink($this->storeDir() . "$name");
            return $result;
        } catch (Error $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
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
