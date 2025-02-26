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
        $user = User::make([
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
            $user = User::factory()->make();

            $this->assertTrue($user->save());
        }
    }

    public function test_is_vet_method(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isVet());
        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(CRMVRegister::make(["crmv" => "2024001", "state" => "SP"]));

        $vet->save();

        $this->assertTrue($user->isVet());
    }

    public function test_cannot_put_wrong_name(): void
    {
        $user = User::factory()->make();

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
        $user = User::factory()->make();

        $this->assertTrue($user->save());

        $user->email = "email";

        $this->assertFalse($user->save());

        $user->email = "email@email";

        $this->assertFalse($user->save());

        $user->email = "ema@il@email.com";

        $this->assertFalse($user->save());

        $user->email = "email@email.com";

        $this->assertTrue($user->save());

        $user2 = User::factory()->make();

        $user2->email = "email@email.com";

        $this->assertFalse($user2->save());
    }

    public function test_cannot_have_same_email(): void
    {

        $user = User::factory()->make();

        $this->assertTrue($user->save());
        $this->assertTrue($user->save());

        $user2 = User::factory()->make();

        $user2->email = $user->email;

        $this->assertFalse($user2->save());
    }

    public function test_cannot_put_wrong_cpf(): void
    {
        $user = User::factory()->make();

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

    public function test_cannot_have_same_cpf(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($user->save());
        $this->assertTrue($user->save());

        $user2 = User::factory()->make();

        $user2->cpf = $user->cpf;

        $this->assertFalse($user2->save());
    }

    public function test_to_array_works(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($user->save());
        $expected = ['id', 'updated_at', "created_at", "name"];
        $actual = array_keys($user->toArray());

        sort($actual);
        sort($expected);

        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $expected,
            $actual,
            ["just-for-phpstan"]
        );
    }

    public function test_to_array_can_send_email(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($user->save());

        $this->assertArrayNotHasKey("email", $user->toArray());

        $this->assertArrayHasKey("email", $user->makeVisible("email")->toArray());
    }

    public function test_can_hidde_attributtes(): void
    {
        $user = User::factory()->make();

        $this->assertTrue($user->save());

        $this->assertArrayHasKey("name", $user->toArray());

        $this->assertArrayNotHasKey("name", $user->makeHidden("name")->toArray());
    }

    public function test_find_by_email(): void
    {
        $user = User::factory()->createMany(20);
        $user = User::factory()->make();

        $user->email = "findbyemail@test.com";

        $user->save();

        $filter_users = User::where(['email', "LIKE", "%findbyemail%"]);

        $emails = [];

        foreach ($filter_users as $user) {
            $emails[] = $user->email;
        }

        $this->assertContains($user->email, $emails);
    }
}
