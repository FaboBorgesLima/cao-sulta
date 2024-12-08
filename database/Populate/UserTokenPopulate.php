<?php

namespace Database\Populate;

use App\Models\User;
use App\Models\UserToken;

class UserTokenPopulate
{
    public static function populate(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            for ($i = 0; $i < 2; $i++) {
                $token = UserToken::make($user->id);
                $token->save();
            }
        }

        echo UserToken::class . " populated\n";
    }
}
