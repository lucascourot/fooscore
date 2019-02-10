<?php

namespace Fooscore\Identity;

final class Identity implements LogIn, CheckToken
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

    public function isValid(string $token): bool
    {
        return $this->registeredUsers->isTokenValid($token);
    }
}
