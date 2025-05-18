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
}
