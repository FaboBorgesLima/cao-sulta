<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

class Pet extends Model
{
    static protected string $table = "pets";
    protected static array $columns = ["user_id", "name"];

    public function validates(): void
    {
        Validations::notEmpty("user_id", $this);
        Validations::notEmpty("name", $this);
        Validations::name("name", $this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
