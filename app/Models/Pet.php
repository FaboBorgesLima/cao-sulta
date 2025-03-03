<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

class Pet extends Model
{
    protected static string $table = "pets";
    protected static array $columns = ["user_id", "name", "image"];

    public function validates(): void
    {
        Validations::notEmpty("user_id", $this);
        Validations::notEmpty("name", $this);
        Validations::name("name", $this);
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function canDestroy(User $user): bool
    {
        return $this->user_id == $user->id;
    }

    public function canUpdate(User $user): bool
    {
        return $this->user_id == $user->id;
    }
}
