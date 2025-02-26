<?php

namespace Database\Factory;

use App\Models\User;
use Core\Database\ActiveRecord\Factory;
use Lib\Random;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static string $model = User::class;

    protected static function definition(): array
    {
        return [
            'name' => Random::name(),
            'email' => Random::email(),
            'cpf' => Random::CPF()
        ];
    }
}
