<?php

namespace Tests\Traits;

use App\Models\Category;
use App\Models\Service;

trait CreateService {
    public function createService(Category $category):Service 
    {
        $service = Service::create([
            'category_id' => $category->id,
            'name' => 'Test Service',
            'description' => 'Test Description',
        ]);

        return $service;
    }
}