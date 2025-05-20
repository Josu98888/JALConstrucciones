<?php

declare(strict_types=1);

namespace Tests\CategoryTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase as TestCase;
use Tests\Traits\CreateUser as TraitsCreateUser;
use Tests\Traits\CreateCategory as TraitsCreateCategory;

class CategoryControllerTest extends TestCase
{

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
    public function store()
    {
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

    #[Test]
    public function update()
    {
        // preparacion
        $data = [
            'name' => 'Category Test Updated',
            'description' => 'Category Test Description Updated',
        ];
        $id = $this->category->id;

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->putJson('/api/category/' . $id, [
            'json' => json_encode($data)
        ]);

        // verificacion
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Category Test Updated',
            'description' => 'Category Test Description Updated',
        ]);
    }

    #[Test]
    public function index() {
        // llamada
        $response = $this->getJson('api/category');

        // verificacion
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'categories' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'image',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    #[Test]
    public function getImage() {
        // preparación 
        $filename = 'test-image.jpg';
        $file = UploadedFile::fake()->image($filename);                          // Crea un archivo de imagen falso
        Storage::disk('public')->putFileAs('categories', $file, $filename);      // Guarda el archivo en el disco público

        // llamada 
        $response = $this->get("/api/category/getImage/$filename");

        // verificación
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');

        // limpieza
        Storage::disk('public')->delete("categories/{$filename}");
    }

    #[Test] 
    public function destroy() {
        // preparación
        $id = $this->category->id;

        // llamada
        $response = $this->withHeaders([
            'Authorization' => $this->token
        ])->deleteJson('/api/category/' . $id);

        // verificación
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'La categoría ha sido eliminada correctamente.'
        ]);
        $this->assertDatabaseMissing('categories', [
        'id' => $id
    ]);
    }
}
