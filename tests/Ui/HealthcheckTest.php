<?php

declare(strict_types=1);

namespace Fooscore\Tests\Ui;

use Fooscore\Kernel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use function json_decode;

/**
 * @group ui
 */
class HealthcheckTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testStatusRouteRespondsCorrectly() : void
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
