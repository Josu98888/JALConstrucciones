<?php

namespace Tests\ServiceTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\CreateService;
use Tests\Traits\CreateUser;

class ServiceControllerTest extends TestCase {
    use DatabaseTransactions;
    use CreateService;
    use CreateUser;

    protected $user;
    protected $token;
    protected $service;
    
    protected function setUp(): void {
        parent::setUp();
        $this->user = $this->createUser();
        $this->token = $this->getJwtTokenForUser($this->user);
        $this->service = $this->createService();
    }
}