<?php

namespace Database\Populate;

use App\Models\Pet;
use App\Models\User;
use Lib\Random;

class PetPopulate
{
    public static function populate(): void
    {
        $users = User::all();

        for ($i = 0; $i < (int)(count($users) / 2); $i++) {
            $users[$i]->pets()->new(["name" => Random::name()])->save();
            $users[$i]->pets()->new(["name" => Random::name()])->save();
            $users[$i]->pets()->new(["name" => Random::name()])->save();
        }
        echo Pet::class . " populated\n";
    }
}
