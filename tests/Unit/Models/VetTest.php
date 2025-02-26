<?php

namespace Tests\Unit\Models;

use App\Models\CRMVRegister;
use App\Models\User;
use Tests\TestCase;

class VetTest extends TestCase
{
    public function test_can_create_with_cmrv(): void
    {
        $user = User::factory()->create();

        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(CRMVRegister::make(["crmv" => "2024001", "state" => "SP"]));

        $this->assertTrue($vet->save());

        $crmvs = $vet->CRMVRegisters()->get();

        foreach ($crmvs as $crmv) {
            $this->assertIsInt($crmv->id);
        }
    }

    public function test_cannot_create_without_cmrv(): void
    {
        $user = User::factory()->create();

        $user->save();

        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $this->assertFalse($vet->save());

        $vet->attachCRMVRegister(CRMVRegister::make(["crmv" => "2024001", "state" => "SP"]));

        $this->assertTrue($vet->save());
    }
}
