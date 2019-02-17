<?php

declare(strict_types=1);

namespace Fooscore\Tests\Unit\Identity;

use Fooscore\Identity\Credentials;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class CredentialsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testUsernameShouldBeAnEmail(): void
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
