<?php

namespace Database\Populate;

use App\Models\User;

class UserPopulate
{
    public static function populate(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory();

            $user->save();
        }

        echo "user populated\n";
    }
}
