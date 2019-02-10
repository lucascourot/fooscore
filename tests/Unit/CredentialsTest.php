<?php

namespace Fooscore\Tests\Unit;

use Fooscore\Identity\Credentials;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class CredentialsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testShouldMatchCredentials(): void
    {
        // Given
        $email = 'john@example.com';
        $password = 'john123';

        // When
        $credentials = new Credentials($email, $password);

        // Then
        self::assertTrue($credentials->match($email, $password));
    }

    public function testShouldBeAnEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $username = 'john';

        // When
        new Credentials($username, 'test');
    }

    public function testShouldNotBeAnEmptyPassword(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $username = 'john@example.com';

        // When
        new Credentials($username, '');
    }
}
