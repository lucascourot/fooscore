<?php

declare(strict_types=1);

namespace Fooscore\Identity;

interface CanCheckToken
{
    public function isValid(string $token) : bool;
}
