<?php

namespace Fooscore\Identity;

interface RegisteredUsers
{
    public function getUser(Username $username): ?array;
}
