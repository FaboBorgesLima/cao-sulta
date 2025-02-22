<?php

namespace Database\Populate;

use App\Models\User;

class UserPopulate
{
    public static function populate(): void
    {
        User::factory()->createMany(10);

        echo User::class . " populated\n";
    }
}
