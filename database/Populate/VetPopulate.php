<?php

namespace Database\Populate;

use App\Models\CRMVRegister;
use App\Models\User;

class VetPopulate
{
    public static function populate(): void
    {
        $users = User::all();

        for ($i = 0; $i < (int)(count($users) / 2); $i++) {
            /** @var \App\Models\Vet */
            $vet = $users[$i]->vet()->new([]);

            $vet->attachCRMVRegister(new CRMVRegister(["crmv" => "202200$i", "state" => "PR"]));

            $vet->save();
        }

        echo "vet populated\n";
    }
}
