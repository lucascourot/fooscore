<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Identity;

use Fooscore\Identity\Identity;
use Fooscore\Identity\RegisteredUsers;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 *
 * In order to protect access
 * As a system
 * I want to check the auth token
 */
class CheckTokenTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldCheckValidToken() : void
    {
        // Given
        $token = 'johnToken';

        $registeredUsers = Mockery::mock(RegisteredUsers::class, ['tokenExists' => true]);

        // When
        $identity = new Identity($registeredUsers);
        $isValid = $identity->isValid($token);

        // Then
        self::assertTrue($isValid);
    }
}
