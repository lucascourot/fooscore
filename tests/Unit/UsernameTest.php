<?php

namespace Fooscore\Tests\Unit;

use Fooscore\Identity\Username;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class UsernameTest extends TestCase
{
    public function testShouldBeAnEmail()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Given
        $username = 'john';

        // When
        new Username($username);
    }
}
