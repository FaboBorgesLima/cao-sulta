<?php

namespace Tests\Unit\Models;

use App\Models\CRMVRegister;
use App\Models\User;
use Tests\TestCase;

class CRMVRegisterTest extends TestCase
{
    public function test_can_crud(): void
    {
        $user = User::factory();

        $user->save();

        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(new CRMVRegister(["crmv" => "2024001", "state" => "SP"]));

        $this->assertTrue($vet->save());

        $cmrvPR = $vet->CRMVRegisters()->new(["crmv" => "2024001", "state" => "PR"]);

        $cmrvPR->save();

        $crmvs = $vet->CRMVRegisters()->get();

        $this->assertEquals(2, count($crmvs));

        $cmrvPR->destroy();

        $crmvs = $vet->CRMVRegisters()->get();

        $this->assertEquals(1, count($crmvs));
    }

    public function test_cannot_leave_vet_without_cmrv(): void
    {
        $user = User::factory();

        $user->save();

        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(new CRMVRegister(["crmv" => "2024001", "state" => "SP"]));

        $this->assertTrue($vet->save());

        $crmvs = $vet->CRMVRegisters()->get();

        $this->assertEquals(1, count($crmvs));

        $crmv = $vet->CRMVRegisters()->get()[0];

        $this->assertFalse($crmv->destroy());
    }
}
