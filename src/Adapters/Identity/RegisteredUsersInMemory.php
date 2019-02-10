<?php

namespace Fooscore\Adapters\Identity;

use Fooscore\Identity\Credentials;
use Fooscore\Identity\RegisteredUsers;

final class RegisteredUsersInMemory implements RegisteredUsers
{
    /**
     * @var array
     */
    private $users = [
        [
            'username' => 'john@example.com',
            'password' => 'john123',
            'name' => 'John Doe',
            'token' => 'johnToken',
        ],
        [
            'username' => 'alice@example.com',
            'password' => 'alice123',
            'name' => 'Alice',
            'token' => 'johnToken',
        ],
        [
            'username' => 'bob@example.com',
            'password' => 'bob123',
            'name' => 'Bob',
            'token' => 'johnToken',
        ],
    ];

    public function getUser(Credentials $credentials): ?array
    {
        foreach ($this->users as $user) {
            if ($credentials->match($user['username'], $user['password'])) {
                return $user;
            }
        }

        return null;
    }

    public function isTokenValid(string $token): bool
    {
        foreach ($this->users as $user) {
            if ($user['token'] === $token) {
                return true;
            }
        }

        return false;
    }
}
