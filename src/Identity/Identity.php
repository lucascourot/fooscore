<?php

declare(strict_types=1);

namespace Fooscore\Identity;

use function array_map;

final class Identity implements CanLogIn, CanCheckToken, CanGetUsers
{
    /** @var RegisteredUsers */
    private $registeredUsers;

    public function __construct(RegisteredUsers $registeredUsers)
    {
        $this->registeredUsers = $registeredUsers;
    }

    public function logIn(Credentials $credentials) : ?string
    {
        $user = $this->registeredUsers->getUser($credentials);

        return $user['token'] ?? null;
    }

    public function isValid(string $token) : bool
    {
        return $this->registeredUsers->tokenExists($token);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsers() : array
    {
        return array_map(static function (array $user) : array {
            return [
                'id' => $user['id'],
                'name' => $user['name'],
            ];
        }, $this->registeredUsers->getAllUsers());
    }
}
