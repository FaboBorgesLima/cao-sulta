<?php

namespace Database\Populate;

use App\Models\CRMVRegister;
use App\Models\User;
use Lib\Token;

class UserPopulate
{
    public static function populate(): void
    {

        for ($i = 0; $i < 10; $i++) {
            $user = User::factory();

            $user->save();

            $user->userTokens()->new(["token" => Token::make()])->save();

            if (rand(0, 1)) {


                /** @var \App\Models\Vet */
                $vet = $user->vet()->new([]);

                $vet->attachCRMVRegister(new CRMVRegister(["state" => "PR", "crmv" => "200000$i"]));

                $vet->save();
            }
        }
        var_dump(array_map(fn($model) => $model->vet()->get(), User::all()));
    }
}
