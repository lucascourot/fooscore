<?php

namespace Fooscore\Identity;

final class Username
{
    /**
     * @var string
     */
    private $username;

    public function __construct(string $username)
    {
        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Please use your email address as the username.');
        }

        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
