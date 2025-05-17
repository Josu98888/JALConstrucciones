<?php

namespace Tests\Traits;

use App\Models\User;

trait CreateUser {

    public function createUser(): User {
        $user = User::factory()->create([
            'name' => 'Test User',
            'lastname' => 'Test Lastname',
            'email' => 'testUser@email.com',
            'password' => bcrypt('password123'),
            'role' => 'ROLE_ADMIN',
            'image' => 'null'
        ]);

        return $user;
    }
}