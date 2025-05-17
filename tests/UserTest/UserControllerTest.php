<?php

declare(strict_types=1);

namespace Tests\UserTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase as TestsTestCase;
use Tests\Traits\CreateUser as TraitsCreateUser;

class UserControllerTest extends TestsTestCase
{

    use DatabaseTransactions;
    use TraitsCreateUser;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createUser();
    }

    #[Test]
    public function userLogin()
    {
        // llamada
        $response = $this->postJson('/api/login', [
            'json' => json_encode([
                'email' => 'testUser@email.com',
                'password' => 'password123',
            ])
        ]);

        // verificación
        $response->assertStatus(200)->assertJsonStructure([
            'token',
            'user',
        ]);
    }

    #[Test]
    public function update()
    {
        // preparación
        $token = $this->getJwtTokenForUser($this->user);
        $data = [
            'name' => 'Test User Updated',
            'lastname' => 'Test Lastname Updated',
            'email' => 'testUserUpdated@email.com'
        ];

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $token,
        ])->postJson('/api/user/update', $data); 

        // verificación
        // $response->dump();
        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'name' => 'Test User Updated',
                'lastname' => 'Test Lastname Updated',
                'email' => 'testUserUpdated@email.com',
            ]
        ]);
    }
}
