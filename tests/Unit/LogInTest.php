<?php

namespace Fooscore\Tests\Unit;

use Fooscore\Identity\Identity;
use Fooscore\Identity\RegisteredUsers;
use Fooscore\Identity\Username;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * In order to score goals of my team mates
 * As a referee
 * I want to log in
 */
class LogInTest extends TestCase
{
    public function testShouldLogInAsARegisteredUser()
    {
        // Given
        $john = [
            'username' => 'john@example.com',
            'password' => '123',
            'token' => 'abc'
        ];

        /** @var RegisteredUsers $registeredUsers */
        $registeredUsers = Mockery::mock(RegisteredUsers::class, [
            'getUser' => $john,
        ]);

        // When
        $identity = new Identity($registeredUsers);
        $token = $identity->logIn(new Username('john@example.com'));

        // Then
        $this->assertSame('abc', $token);
    }
}