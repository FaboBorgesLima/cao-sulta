<?php

namespace Tests\Unit\Models;

use App\Models\CRMVRegister;
use App\Models\User;
use Lib\CPF;
use Lib\Random;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_can_crud(): void
    {
        // create
        $user = new User([
            "name" => "fulano",
            "email" => "fake@fake.com",
            "cpf" => CPF::getRandomCPF()
        ]);

        $this->assertTrue($user->save());

        $this->assertTrue($user->isValid());
        // update

        $user->name = "testeee";

        $this->assertTrue($user->save());

        // read

        $user = User::findById($user->id);

        $this->assertNotNull($user);

        // delete

        $this->assertTrue($user->destroy());
    }

    public function test_factory_works(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = User::factory();

            $this->assertTrue($user->save());
        }
    }

    public function test_is_vet_method(): void
    {
        $user = User::factory();

        $user->save();

        $this->assertFalse($user->isVet());
        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $crmvRegister = new CRMVRegister(["crmv" => "2024001", "state" => "SP"]);

        $vet->attachCRMVRegister($crmvRegister);

        $vet->save();

        $this->assertTrue($user->isVet());
    }

    public function test_cannot_put_wrong_name(): void
    {
        $user = User::factory();

        $this->assertTrue($user->save());

        $user->name = "f4k3 n4m3";

        $this->assertFalse($user->save());

        $user->name = "\$teve";

        $this->assertFalse($user->save());

        $user->name = "legit name";

        $this->assertTrue($user->save());
    }

    public function test_cannot_put_wrong_email(): void
    {
        $user = User::factory();

        $this->assertTrue($user->save());

        $user->email = "email";

        $this->assertFalse($user->save());

        $user->email = "email@email";

        $this->assertFalse($user->save());

        $user->email = "ema@il@email.com";

        $this->assertFalse($user->save());

        $user->email = "email@email.com";

        $this->assertTrue($user->save());
    }

    public function test_cannot_put_wrong_cpf(): void
    {
        $user = User::factory();

        $this->assertTrue($user->save());

        $user->cpf = "test";

        $this->assertFalse($user->save());

        $user->cpf = "12345678910";

        $this->assertFalse($user->save());

        $user->cpf = "00000000001";

        $this->assertFalse($user->save());

        $user->cpf = Random::CPF();

        $this->assertTrue($user->save());
    }
}
