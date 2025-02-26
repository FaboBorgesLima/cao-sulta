<?php

namespace Database\Factory;

use App\Models\CRMVRegister;
use Core\Database\ActiveRecord\Factory;
use Lib\Random;

/**
 * @extends Factory<CRMVRegister>
 */
class CRMVRegisterFactory extends Factory
{
    protected static string $model = CRMVRegister::class;

    protected static function definition(): array
    {
        return [
            'vet_id' => new VetFactory(),
            'crmv' => Random::CRMV(),
            'state' => Random::state(),
        ];
    }
}
