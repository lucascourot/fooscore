<?php

declare(strict_types=1);

namespace Fooscore\Identity;

interface CanLogIn
{
    public function logIn(Credentials $username): ?string;
}
