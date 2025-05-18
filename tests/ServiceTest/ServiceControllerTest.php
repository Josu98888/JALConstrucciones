<?php

namespace Tests\ServiceTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCategory;
use Tests\Traits\CreateService;
use Tests\Traits\CreateUser;

class ServiceControllerTest extends TestCase
{
    use DatabaseTransactions;
    use CreateCategory;
    use CreateService;
    use CreateUser;

    protected $user;
    protected $token;
    protected $category;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
        $this->token = $this->getJwtTokenForUser($this->user);
        $this->category = $this->createCategory();
        $this->service = $this->createService($this->category);
    }

    #[Test]
    public function store()
    {
        // preparacion
        $idCategory = $this->category->id;
        $data = [
            'category_id' => $idCategory,
            'name' => 'Service Test',
            'description' => 'Service Test Description',
            'outstanding' => 1,
        ];

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->post('/api/service/store',  [
            'json' => json_encode($data)
        ]);

        // verificacion
        $response->assertStatus(200)->assertJson([
            'service' => [
                'category_id' => $idCategory,
                'name' => 'Service Test',
                'description' => 'Service Test Description',
                'outstanding' => 1,
            ]
        ]);
    }

    #[Test]
    public function update()
    {
        // preparacion
        $id = $this->service->id;
        $data = [
            'category_id' => $this->category->id,
            'name' => 'Service Test Updated',
            'description' => 'Service Test Description Updated',
            'outstanding' => 1,
        ];

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->put('/api/service/update/' . $id, [
            'json' => json_encode($data)
        ]);

        // verificacion
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Service Test Updated',
            'description' => 'Service Test Description Updated',
            'outstanding' => 1,
        ]);
    }

    #[Test]
    public function show()
    {
        // preparacion
        $id = $this->service->id;

        // llamada
        $response = $this->get('/api/service/' . $id);

        // verificacion
        $response->assertStatus(200);
        $response->assertJson([
            'service' => [
                'id' => $id,
                'name' => $this->service->name,
                'description' => $this->service->description,
                'outstanding' => $this->service->outstanding,
            ]
        ]);
    }

    #[Test] 
    public function getServicesByCategory() {
        // preparacion
        $idCategory = $this->category->id;

        // llamada
        $response = $this->getJson("api/services/getServicesByCategory/$idCategory");

        // verificacion
        $response->assertStatus(200);
        $response->assertJson([
            'category' => $this->category->name,
        ]);
        $response->assertJsonStructure([
            'category',
            'services' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ]
            ]
        ]);
    }

    #[Test]
    public function destroy()
    {
        // preparacion
        $id = $this->service->id;

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->delete('/api/service/delete/' . $id);

        // verificacion
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Servicio eliminado correctamente.',
        ]);
        $this->assertDatabaseMissing('services', [
            'id' => $id
        ]);
    }
}
