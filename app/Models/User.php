<?php

namespace App\Models;

use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

class User extends Model
{
    protected static string $table = "users";
    protected static array $columns = ["name", "email", "cpf"];

    public function validates(): void
    {
        Validations::notEmpty("name", $this);
        Validations::notEmpty("email", $this);
        Validations::notEmpty("cpf", $this);
        Validations::uniqueness("email", $this);
        Validations::uniqueness("cpf", $this);
        Validations::isCPF("cpf", $this);
    }

    public function userTokens(): HasMany
    {
        return $this->hasMany(UserToken::class, "user_id");
    }
}
