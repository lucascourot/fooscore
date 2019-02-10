<?php

namespace Fooscore\Identity;

interface LogIn
{
    public function logIn(Credentials $username): ?string;
}
