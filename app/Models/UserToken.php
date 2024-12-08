<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Random;
use Lib\Token;
use Lib\Validations;

class UserToken extends Model
{
    protected static string $table = "user_tokens";
    protected static array $columns = ["token", "user_id"];

    public static function make(int $user_id): UserToken
    {
        return new UserToken(["token" => Random::token(), "user_id" => $user_id]);
    }

    public function validates(): void
    {
        Validations::notEmpty("token", $this);
        Validations::notEmpty("user_id", $this);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
