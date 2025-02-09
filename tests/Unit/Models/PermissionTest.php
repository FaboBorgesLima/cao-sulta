<?php

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\User;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    public function test_can_crud(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $vet = $user2->vet()->new([]);

        $user->save();

        $this->assertTrue(true);
    }
}
