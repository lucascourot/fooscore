<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Identity;

use Fooscore\Identity\Credentials;
use Fooscore\Identity\Identity;
use Fooscore\Identity\RegisteredUsers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
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
    use MockeryPHPUnitIntegration;

    public function testShouldLogInAsARegisteredUser() : void
    {
        // Given
        $john = [
            'username' => 'john@example.com',
            'password' => '123',
            'name' => 'John Doe',
            'token' => 'abc',
        ];

        $registeredUsers = Mockery::mock(RegisteredUsers::class, ['getUser' => $john]);

        // When
        $identity = new Identity($registeredUsers);
        $token = $identity->logIn(new Credentials($john['username'], $john['password']));

        // Then
        self::assertSame($john['token'], $token);
    }

    public function testShouldNotLogInIfNotRegistered() : void
    {
        // Given
        $registeredUsers = Mockery::mock(RegisteredUsers::class, ['getUser' => null]);

        // When
        $identity = new Identity($registeredUsers);
        $token = $identity->logIn(new Credentials('john@example.com', 'foo'));

        // Then
        self::assertNull($token);
    }
}
