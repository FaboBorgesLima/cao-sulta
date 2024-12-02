<?php

namespace Database\Populate;

use App\Models\User;
use Lib\CPF;

class UserPopulate
{
    public static function populate(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User(["name" => "tonho$i", "cpf" => CPF::getRandomCPF(), "email" => "tonho$i@tonho.com"]);

            $user->save();
        }
        var_dump(User::all());
    }
}
