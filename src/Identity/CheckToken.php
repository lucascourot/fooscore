<?php

namespace Fooscore\Identity;

interface CheckToken
{
    public function isValid(string $token): bool;
}
