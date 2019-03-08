<?php

declare(strict_types=1);

namespace Fooscore\Identity;

interface CanGetUsers
{
    public function getUsers(): array;
}
