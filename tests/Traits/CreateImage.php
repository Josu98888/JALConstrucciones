<?php

namespace Tests\Traits;

use App\Models\Image;
use App\Models\Service;

trait CreateImage {
    public function createImage(Service $Service):Service 
    {
        $image = Image::create([
            'service_id' => $Service->id,
            'url' => 'TestImage.jpg',
        ]);

        return $image;
    }
}