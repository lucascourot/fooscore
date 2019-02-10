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

    public function logIn(Username $username): ?string
    {
        $user = $this->registeredUsers->getUser($username);

        return $user['token'] ?? null;
    }
}
