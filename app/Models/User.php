<?php

namespace App\Models;

use Core\Database\ActiveRecord\Factory;
use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\HasManyThrough;
use Core\Database\ActiveRecord\HasOne;
use Core\Database\ActiveRecord\Model;
use Database\Factory\UserFactory;
use Lib\CPF;
use Lib\Random;
use Lib\Validations;

class User extends Model
{
    protected static string $table = "users";
    protected static array $columns = ["name", "email", "cpf"];
    protected static array $hidden = ["email", "cpf"];

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

    /**
     * @return Factory<self>
     */
    public static function factory(): Factory
    {
        return new UserFactory();
    }

    /**
     * @return HasMany<UserToken>
     */
    public function userTokens(): HasMany
    {
        return $this->hasMany(UserToken::class, "user_id");
    }

    /**
     * @return HasOne<Vet>
     */
    public function vet(): HasOne
    {
        return $this->hasOne(Vet::class, "user_id");
    }

    public function isVet(): bool
    {
        return (bool) $this->vet()->get();
    }

    /**
     * @return HasMany<Pet>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, "user_id");
    }

    /**
     * @return HasMany<Permission>
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, "user_id");
    }

    /**
     * @return HasManyThrough<Vet,Permission>
     */
    public function permissionsVets(): HasManyThrough
    {
        return $this->hasManyThrough(Vet::class, Permission::class, "user_id", "vet_id");
    }
}
