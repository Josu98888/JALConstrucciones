<?php

namespace Tests\Traits;
use App\Models\Category;

trait CreateCategory 
{
    public function createCategory(): Category
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'Test Description',
            'image' => 'null'
        ]);

        return $category;
    }
}