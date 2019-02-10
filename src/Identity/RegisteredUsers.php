<?php

namespace Fooscore\Identity;

interface RegisteredUsers
{
    public function getUser(Credentials $username): ?array;
}
