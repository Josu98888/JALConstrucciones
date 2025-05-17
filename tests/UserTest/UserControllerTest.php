<?php

declare(strict_types=1);

namespace Tests\UserTest;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase as TestsTestCase;

class UserControllerTest extends TestsTestCase
{

    use DatabaseTransactions;

    #[Test]
    public function userLogin()
    {
        // preparación
        $user = User::factory()->create([
            'name' => 'Test User',
            'lastname' => 'Test Lastname',
            'email' => 'testUser@email.com',
            'password' => bcrypt('password123'),
            'role' => 'ROLE_ADMIN',
            'image' => 'null'
        ]);

        // llamada
        $response = $this->postJson('/api/login', [
            'json' => json_encode([
                'email' => 'testUser@email.com',
                'password' => 'password123',
            ])
        ]);

        // verificación
        $response->assertStatus(200);
    }
}
