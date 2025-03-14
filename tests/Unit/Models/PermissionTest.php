<?php

namespace Tests\Unit\Models;

use App\Models\CRMVRegister;
use App\Models\Permission;
use App\Models\User;
use Database\Factory\VetFactory;
use Lib\Random;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    public function test_can_crud(): void
    {
        $user = User::factory()->create();
        $vet = VetFactory::create();

        $vet->attachCRMVRegister(CRMVRegister::make([
            "crmv" => Random::CRMV(),
            "state" => Random::state()
        ]));

        $vet->save();

        // Create
        $permission = $user->permissions()->new(["vet_id" => $vet->id, "accepted" => 0]);

        $this->assertTrue($permission->save());

        // Read

        $this->assertInstanceOf(Permission::class, Permission::findById($permission->id));

        // Update

        $permission->accepted = true;

        $permission->save();

        $this->assertEquals(1, Permission::findById($permission->id)->accepted);

        // Delete
        $permission->destroy();

        $this->assertNull(Permission::findById($permission->id));
    }

    public function test_relationship(): void
    {
        $user = User::factory()->create();
        $vet = VetFactory::create();

        $vet->attachCRMVRegister(CRMVRegister::make([
            "crmv" => Random::CRMV(),
            "state" => Random::state()
        ]));

        $vet->save();

        $permission = $user->permissions()->new(["vet_id" => $vet->id, "accepted" => 0]);

        $permission->save();

        $this->assertEquals(1, count($user->permissionsVets()->get()));
        $this->assertEquals(1, count($vet->permissionsUsers()->get()));

        $user->permissions()->new(["vet_id" => $vet->id, "accepted" => 0])->save();

        $this->assertEquals(1, count($user->permissionsVets()->get()));
        $this->assertEquals(1, count($vet->permissionsUsers()->get()));
    }

    public function test_alter_permissions(): void
    {
        $user = User::factory()->create();
        $vet = VetFactory::create();

        $vet->attachCRMVRegister(CRMVRegister::make([
            "crmv" => Random::CRMV(),
            "state" => Random::state()
        ]));

        $vet->save();

        $permission = $user->permissions()->new(["vet_id" => $vet->id, "accepted" => 0]);

        $permission->save();

        $permission = Permission::findById($permission->id);

        $this->assertTrue($permission->canUserDelete($user));
        $this->assertTrue($permission->canUserUpdate($user));

        $user2 = User::factory()->create();

        $this->assertFalse($permission->canUserDelete($user2));
        $this->assertFalse($permission->canUserUpdate($user2));
    }
}
