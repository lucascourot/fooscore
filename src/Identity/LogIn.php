<?php

namespace Fooscore\Identity;

interface LogIn
{
    public function logIn(Username $username): ?string;
}
