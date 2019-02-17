<?php

declare(strict_types=1);

namespace Fooscore\Identity;

interface LogIn
{
    public function logIn(Credentials $username): ?string;
}
