<?php

namespace Database\Factory;

use App\Models\Vet;
use Core\Database\ActiveRecord\Factory;


/**
 * @extends Factory<Vet>
 */
class VetFactory extends Factory
{
    protected static string $model = Vet::class;

    protected static function definition(): array
    {
        return [
            'user_id' => new UserFactory()
        ];
    }
}
