<?php

declare(strict_types=1);

namespace Tests\UserTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    #[Test]
    public function getImage()
    {
        // preparación 
        $filename = 'test-image.jpg';
        $file = UploadedFile::fake()->image($filename);                     // Crea un archivo de imagen falso
        Storage::disk('public')->putFileAs('users', $file, $filename);      // Guarda el archivo en el disco público

        // llamada 
        $response = $this->get("/api/user/avatar/$filename");

        // verificación
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');

        // limpieza
        Storage::disk('public')->delete("users/{$filename}");
    }

    #[Test]
    public function detail() {
        // preparacion
        $id = $this->user->id;

        // llamda
        $response = $this->getJson("/api/user/detail/$id");

        // verificacion
        $response->dump();
        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'id' => $id,
            ]
        ]);
    }
}
