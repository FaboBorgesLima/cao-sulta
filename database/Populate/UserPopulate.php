<?php

namespace Database\Populate;

use App\Models\User;
use App\Models\UserToken;
use Lib\CPF;
use Lib\Token;

class UserPopulate
{
    public static function populate(): void
    {

        for ($i = 0; $i < 10; $i++) {
            $user = new User(["name" => "tonho$i", "cpf" => CPF::getRandomCPF(), "email" => "tonho$i@tonho.com"]);
            $user->save();

            $token = new UserToken(["token" => Token::make(), "user_id" => $user->id]);
            $token->save();
        }
    }
}
