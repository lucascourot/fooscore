<?php

namespace Fooscore\Tests\Unit;

use Fooscore\Identity\Identity;
use Fooscore\Identity\RegisteredUsers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * In order to select users
 * As a referee
 * I want to get all available users
 */
class GetUsersTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldGetAllUsers(): void
    {
        // Given
        $users = [
            [
                'id' => 1,
                'username' => 'alice@example.com',
                'password' => 'alice123',
                'name' => 'Alice',
                'token' => 'johnToken',
            ],
            [
                'id' => 2,
                'username' => 'bob@example.com',
                'password' => 'bob123',
                'name' => 'Bob',
                'token' => 'johnToken',
            ],
        ];

        /** @var RegisteredUsers $registeredUsers */
        $registeredUsers = Mockery::mock(RegisteredUsers::class, [
            'getAllUsers' => $users,
        ]);

        // When
        $identity = new Identity($registeredUsers);
        $fetchedUsers = $identity->getUsers();

        // Then
        self::assertEquals($fetchedUsers, [
            [
                'id' => 1,
                'name' => 'Alice',
            ],
            [
                'id' => 2,
                'name' => 'Bob',
            ],
        ]);
    }

    public function testNoUserExists(): void
    {
        // Given
        /** @var RegisteredUsers $registeredUsers */
        $registeredUsers = Mockery::mock(RegisteredUsers::class, [
            'getAllUsers' => [],
        ]);

        // When
        $identity = new Identity($registeredUsers);
        $fetchedUsers = $identity->getUsers();

        // Then
        self::assertEquals($fetchedUsers, []);
    }
}
