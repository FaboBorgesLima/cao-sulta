<?php

namespace App\Models;

use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\HasOne;
use Core\Database\ActiveRecord\Model;
use Core\Database\ActiveRecord\HasFactory;
use Lib\CPF;
use Lib\Random;
use Lib\Validations;

class User extends Model implements HasFactory
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
        Validations::CPF("cpf", $this);
        Validations::email("email", $this);
        Validations::name("name", $this);
    }

    public static function factory(): self
    {
        return new User([
            "email" => Random::email(),
            "cpf" => CPF::getRandomCPF(),
            "name" => Random::name()
        ]);
    }

    public function userTokens(): HasMany
    {
        return $this->hasMany(UserToken::class, "user_id");
    }

    public function vet(): HasOne
    {
        return $this->hasOne(Vet::class, "user_id");
    }

    public function isVet(): bool
    {
        return (bool) $this->vet();
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, "user_id");
    }
}
