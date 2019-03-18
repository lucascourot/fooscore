<?php

declare(strict_types=1);

namespace Fooscore\Identity;

interface CanGetUsers
{
    /**
     * @return mixed[]
     */
    public function getUsers() : array;
}
