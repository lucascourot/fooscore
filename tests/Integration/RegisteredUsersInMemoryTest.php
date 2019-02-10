<?php

namespace Fooscore\Tests\Integration;

use Fooscore\Adapters\Identity\RegisteredUsersInMemory;
use Fooscore\Identity\Username;
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
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Username($email));

        // Then
        self::assertSame('John Doe', $user['name']);
    }

    public function testReturnsNullIfUserNotFound(): void
    {
        // Given
        $email = 'notfound@example.com';
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Username($email));

        // Then
        self::assertSame(null, $user);
    }
}
