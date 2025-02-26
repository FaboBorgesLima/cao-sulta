<?php

namespace Database\Populate;

use App\Models\CRMVRegister;
use App\Models\User;
use App\Models\Vet;
use Lib\Random;

class VetPopulate
{
    public static function populate(): void
    {
        $users = User::all();

        for ($i = 0; $i < (int)(count($users) / 2); $i++) {
            /** @var \App\Models\Vet */
            $vet = $users[$i * 2]->vet()->new([]);

            $vet->attachCRMVRegister(CRMVRegister::make([
                "crmv" => Random::CRMV(),
                "state" => Random::state()
            ]));

            $vet->save();
        }

        echo Vet::class . " populated\n";
    }
}
