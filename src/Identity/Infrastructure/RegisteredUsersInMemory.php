<?php

declare(strict_types=1);

namespace Fooscore\Identity\Infrastructure;

use Fooscore\Identity\Credentials;
use Fooscore\Identity\RegisteredUsers;

final class RegisteredUsersInMemory implements RegisteredUsers
{
    /** @var mixed[][] */
    private $users = [
        [
            'id' => 1,
            'username' => 'john@example.com',
            'password' => 'john123',
            'name' => 'John Doe',
            'token' => 'johnToken',
        ],
        [
            'id' => 2,
            'username' => 'alex@example.com',
            'password' => 'alex123',
            'name' => 'Alex',
            'token' => 'alexToken',
        ],
        [
            'id' => 3,
            'username' => 'alice@example.com',
            'password' => 'alice123',
            'name' => 'Alice',
            'token' => 'johnToken',
        ],
        [
            'id' => 4,
            'username' => 'bob@example.com',
            'password' => 'bob123',
            'name' => 'Bob',
            'token' => 'johnToken',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getUser(Credentials $credentials) : ?array
    {
        foreach ($this->users as $user) {
            if ($credentials->match($user['username'], $user['password'])) {
                return $user;
            }
        }

        return null;
    }

    public function tokenExists(string $token) : bool
    {
        foreach ($this->users as $user) {
            if ($user['token'] === $token) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllUsers() : array
    {
        return $this->users;
    }
}
