<?php

namespace Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGerFullNameWithParamsThenReturnTheParams()
    {
        $user = new User();
        $user->setFirstName('FirstName');
    }
}