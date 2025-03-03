<?php

namespace Tests\Unit\Core\FileSystem;

use Core\FileSystem\File;
use Tests\TestCase;

class FileTest extends TestCase
{
    public function test_get(): void
    {
        $file = File::get(__DIR__ . '/TestFiles/test.txt');

        $this->assertNotNull($file);

        $this->assertEquals('txt', $file->extension());

        $this->assertEquals('test', $file->name());
    }
}
