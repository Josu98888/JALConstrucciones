<?php

declare(strict_types=1);

namespace Tests\CategoryTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase as TestCase;
use Tests\Traits\CreateUser as TraitsCreateUser;
use Tests\Traits\CreateCategory as TraitsCreateCategory;

class CategoryControllerTest extends TestCase {

    use DatabaseTransactions;
    use TraitsCreateUser;
    use TraitsCreateCategory;
    
    protected $user;
    protected $token;
    protected $category;

    function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
        $this->token = $this->getJwtTokenForUser($this->user);
        $this->category = $this->createCategory();
    }

    #[Test]
    public function store() {
        // preparacion
        $data = [
            'name' => 'Category Test',
            'description' => 'Category Test Description',
        ];

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->postJson('/api/category', $data);

        // verificacion
        $response->assertStatus(200)->assertJson([
            'categorie' => [
                'name' => 'Category Test',
                'description' => 'Category Test Description',
            ]
        ]);
    }
}