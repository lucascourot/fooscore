<?php

namespace Fooscore\Identity;

interface RegisteredUsers
{
    public function getUser(Credentials $username): ?array;

    public function tokenExists(string $token): bool;

    public function getAllUsers(): array;
}
