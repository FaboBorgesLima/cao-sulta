<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;

class User extends Model
{
    protected static string $table = "";
    protected static array $columns = [];

    public function validates(): void
    {
        $this->addError("implementation", "must implement");
    }
}
