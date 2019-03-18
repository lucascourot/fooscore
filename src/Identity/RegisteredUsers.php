<?php

declare(strict_types=1);

namespace Fooscore\Identity;

interface RegisteredUsers
{
    /**
     * @return mixed[]|null
     */
    public function getUser(Credentials $username) : ?array;

    public function tokenExists(string $token) : bool;

    /**
     * @return mixed[][]
     */
    public function getAllUsers() : array;
}
