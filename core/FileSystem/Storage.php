<?php

namespace Core\FileSystem;

use Core\Constants\Constants;

class Storage
{
    public function __construct(private string $dir)
    {
        //
    }

    public function upload(File $file, string $name): string
    {
        $file->move($this->storeDir(), $name);

        return $this->toURL($file);
    }

    public function fromURL(string $url): ?File
    {
        $path = Constants::rootPath()->join('public' . explode('?', $url)[0]);

        return File::get($path);
    }

    public function toURL(File $file): string
    {
        return $this->baseDir() . $file->basename() . '?v=' . $file->hash();
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
