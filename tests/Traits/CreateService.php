<?php

namespace Tests\Traits;
use App\Models\Service;

trait CreateService {
    public function createService():Service 
    {
        $service = Service::create([
            'category_id' => 1,
            'name' => 'Test Service',
            'description' => 'Test Description',
        ]);

        return $service;
    }
}