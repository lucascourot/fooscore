<?php

namespace Fooscore\Tests\Integration;

use Fooscore\Adapters\Identity\RegisteredUsersInMemory;
use Fooscore\Identity\Credentials;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class RegisteredUsersInMemoryTest extends TestCase
{
    public function testGetsRegisteredUserByUsername(): void
    {
        // Given
        $email = 'john@example.com';
        $password = 'john123';
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Credentials($email, $password));

        // Then
        self::assertSame('John Doe', $user['name']);
    }

    public function testReturnsNullIfUserNotFound(): void
    {
        // Given
        $email = 'notfound@example.com';
        $password = 'password';
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Credentials($email, $password));

        // Then
        self::assertSame(null, $user);
    }

    public function testReturnsNullIfWrongPassword(): void
    {
        // Given
        $email = 'john@example.com';
        $password = 'wrongpsw';
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Credentials($email, $password));

        // Then
        self::assertSame(null, $user);
    }
}
