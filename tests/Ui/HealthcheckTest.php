<?php

namespace Fooscore\Tests\Ui;

use Fooscore\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @group ui
 */
class HealthcheckTest extends TestCase
{
    public function testStatusRouteRespondsCorrectly(): void
    {
        // Given
        $kernel = new Kernel('test', false);

        // When
        $response = $kernel->handle(Request::create('/status'));

        // Then
        self::assertJson($response->getContent());
        self::assertSame(['status' => 'ok'], json_decode($response->getContent(), true));
    }
}
