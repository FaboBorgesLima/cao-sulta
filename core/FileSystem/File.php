<?php

namespace Core\FileSystem;

use Error;
use Exception;

class File
{
    private function __construct(
        private string $dirname,
        private bool $uploaded,
        private string $name,
        private string $extension
    ) {
        //
    }

    public static function get(string $path): ?File
    {
        if (!file_exists($path)) {
            return null;
        }

        $info = pathinfo($path);

        return new File($info['dirname'], false, $info['filename'], $info['extension']);
    }

    public static function request(string $name): ?File
    {
        if (!isset($_FILES[$name])) {
            return null;
        }

        $info = pathinfo($_FILES[$name]['tmp_name']);

        return new File($info['dirname'], true, $info['filename'], pathinfo($_FILES[$name]['full_path'])['extension']);
    }

    public function size(): int
    {
        return (int) filesize($this->dirname);
    }

    public function basename(): string
    {
        if ($this->uploaded) {
            return $this->name;
        }

        return $this->name . '.' . $this->extension;
    }

    public function fullpath(): string
    {
        return $this->dirname . '/' . $this->basename();
    }

    public function mime(): string
    {
        return (string) mime_content_type($this->dirname);
    }

    public function rename(string $name): self
    {
        rename($this->fullpath(), $this->dirname() . '/' . $name . '.' . $this->extension());

        $this->name = $name;

        return $this;
    }

    public function move(string $to, ?string $name): self
    {

        if ($this->uploaded) {
            move_uploaded_file($this->fullpath(), $to . '/' . $this->basename() . '.' . $this->extension);

            $this->uploaded = false;
            $this->dirname = $to;

            if ($name) {
                $this->rename($name);
            }

            return $this;
        }

        rename($this->fullpath(), $to . '/' . $this->basename());

        $this->dirname = $to;

        return $this;
    }

    public function delete(): bool
    {
        try {
            $result = unlink($this->fullpath());
            return $result;
        } catch (Error $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param array<int,string>
     */
    public function isMime(array $mimes): string
    {
        return in_array($this->mime(), $mimes);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function dirname(): string
    {
        return $this->dirname;
    }

    public function extension(): string
    {
        return $this->extension;
    }

    public function hash(): string
    {
        return md5_file($this->fullpath());
    }
}
