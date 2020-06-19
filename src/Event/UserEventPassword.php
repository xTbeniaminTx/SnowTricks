<?php


namespace App\Event;


use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserEventPassword extends Event
{

    private $userPass;

    public function __construct(User $userPass)
    {
        $this->userPass = $userPass;
    }
}