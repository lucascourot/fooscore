<?php

declare(strict_types=1);

namespace Fooscore\Tests\Integration\Identity;

use Fooscore\Identity\Adapters\RegisteredUsersInMemory;
use Fooscore\Identity\Credentials;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class RegisteredUsersInMemoryTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

    public function testShouldCheckValidToken(): void
    {
        // Given
        $token = 'johnToken';
        $adapter = new RegisteredUsersInMemory();

        // When
        $isValid = $adapter->tokenExists($token);

        // Then
        self::assertTrue($isValid);
    }

    public function testShouldCheckInvalidToken(): void
    {
        // Given
        $token = 'not valid';
        $adapter = new RegisteredUsersInMemory();

        // When
        $isValid = $adapter->tokenExists($token);

        // Then
        self::assertFalse($isValid);
    }

    public function testShouldFetchAllUsers(): void
    {
        // Given
        $adapter = new RegisteredUsersInMemory();

        // When
        $users = $adapter->getAllUsers();

        // Then
        self::assertSame($users, [
            [
                'id' => 1,
                'username' => 'john@example.com',
                'password' => 'john123',
                'name' => 'John Doe',
                'token' => 'johnToken',
            ],
            [
                'id' => 2,
                'username' => 'alex@example.com',
                'password' => 'alex123',
                'name' => 'Alex',
                'token' => 'alexToken',
            ],
            [
                'id' => 3,
                'username' => 'alice@example.com',
                'password' => 'alice123',
                'name' => 'Alice',
                'token' => 'johnToken',
            ],
            [
                'id' => 4,
                'username' => 'bob@example.com',
                'password' => 'bob123',
                'name' => 'Bob',
                'token' => 'johnToken',
            ],
        ]);
    }
}
