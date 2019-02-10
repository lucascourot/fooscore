<?php

namespace Fooscore\Identity;

final class Identity implements LogIn
{
    /**
     * @var RegisteredUsers
     */
    private $registeredUsers;

    public function __construct(RegisteredUsers $registeredUsers)
    {
        $this->registeredUsers = $registeredUsers;
    }

    public function logIn(Credentials $credentials): ?string
    {
        $user = $this->registeredUsers->getUser($credentials);

        return $user['token'] ?? null;
    }
}
