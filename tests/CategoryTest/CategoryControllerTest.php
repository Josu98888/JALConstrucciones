<?php

namespace Tests\CategoryTest;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase as TestCase;
use Tests\Traits\CreateUser as TraitsCreateUser;
use Tests\Traits\CreateCategory as TraitsCreateCategory;

class CategoryTest extends TestCase {

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

}