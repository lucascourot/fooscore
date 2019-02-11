<?php

namespace Fooscore\Identity;

final class Identity implements LogIn, CheckToken, GetUsers
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
        return $this->registeredUsers->tokenExists($token);
    }

    public function getUsers(): array
    {
        return array_map(function (array $user): array {
            return [
                'id' => $user['id'],
                'name' => $user['name'],
            ];
        }, $this->registeredUsers->getAllUsers());
    }
}
