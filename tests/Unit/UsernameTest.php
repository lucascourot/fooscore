<?php

namespace Fooscore\Tests\Unit;

use Fooscore\Identity\Username;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UsernameTest extends TestCase
{
    public function testShouldGetValidUsernameAsValue(): void
    {
        // Given
        $email = 'john@example.com';

        // When
        $username = new Username($email);

        // Then
        self::assertSame($email, $username->getUsername());
    }

    public function testShouldBeAnEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $username = 'john';

        // When
        new Username($username);
    }
}
