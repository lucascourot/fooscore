<?php

namespace Fooscore\Adapters\Identity;

use Fooscore\Identity\RegisteredUsers;
use Fooscore\Identity\Username;

final class RegisteredUsersInMemory implements RegisteredUsers
{
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

    public function getUser(Username $username): ?array
    {
        foreach ($this->users as $user) {
            if ($user['username'] === $username->getUsername()) {
                return $user;
            }
        }

        return null;
    }
}
