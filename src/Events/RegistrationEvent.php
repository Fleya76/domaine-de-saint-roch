<?php

namespace App\Events;
use App\Entity\User;

use Symfony\Contracts\EventDispatcher\Event;
 
class RegistrationEvent extends Event
{
    protected $comment;
 
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    public function getUser()
    {
        return $this->user;
    }
}
