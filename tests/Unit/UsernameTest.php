<?php

namespace Fooscore\Tests\Unit;

use Fooscore\Identity\Username;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UsernameTest extends TestCase
{
    public function testShouldGetValidUsernameAsValue()
    {
        // Given
        $email = 'john@example.com';

        // When
        $username = new Username($email);

        // Then
        $this->assertSame($email, $username->getUsername());
    }

    public function testShouldBeAnEmail()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $username = 'john';

        // When
        new Username($username);
    }
}
