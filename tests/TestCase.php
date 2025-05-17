<?php

namespace Tests;

use App\Helpers\JwtAuth;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getJwtTokenForUser(User $user)
    {
        $jwt = new JwtAuth();
        $token = $jwt->signup($user->email, true); // true para recibir el token como string

        return $token;
    }
}
