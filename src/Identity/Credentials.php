<?php

declare(strict_types=1);

namespace Fooscore\Identity;

use InvalidArgumentException;
use const FILTER_VALIDATE_EMAIL;
use function filter_var;
use function strlen;

final class Credentials
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    public function __construct(string $username, string $password)
    {
        if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('Please use your email address as the username.');
        }

        if (strlen($password) === 0) {
            throw new InvalidArgumentException('Password cannot be empty.');
        }

        $this->username = $username;
        $this->password = $password;
    }

    public function match(string $username, string $password) : bool
    {
        return $this->username === $username && $this->password === $password;
    }
}
