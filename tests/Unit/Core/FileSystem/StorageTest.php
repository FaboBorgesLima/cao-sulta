<?php

namespace Tests\Unit\Core\FileSystem;

use Core\FileSystem\Storage;
use Tests\TestCase;

class StorageTest extends TestCase
{
    public function test_to_string(): void
    {
        $storage = new Storage('post');
    }
}
