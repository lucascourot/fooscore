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
    public function testGetsRegisteredUserByUsername()
    {
        // Given
        $email = 'john@example.com';
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Username($email));

        // Then
        $this->assertSame('John Doe', $user['name']);
    }

    public function testReturnsNullIfUserNotFound()
    {
        // Given
        $email = 'notfound@example.com';
        $adapter = new RegisteredUsersInMemory();

        // When
        $user = $adapter->getUser(new Username($email));

        // Then
        $this->assertSame(null, $user);
    }
}
