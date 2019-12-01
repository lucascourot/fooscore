<?php

declare(strict_types=1);

namespace Fooscore\Tests\Ui;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use function sprintf;

/**
 * @group ui
 */
class TeamWinsMatchTest extends TestCase
{
    public function testShouldWinMatchByTeamBlue() : void
    {
        $client = HttpClient::create([
            'base_uri' => 'http://127.0.0.1:8080/',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'johnToken',
            ],
        ]);

        $response = $client->request('POST', '/api/matches', [
            'body' => '{"players":{
                "blueBack":{"id":"1","name":"John Doe"},"blueFront":{"id":"2","name":"Alex"},
                "redBack":{"id":"4","name":"Bob"},"redFront":{"id":"3","name":"Alice"}
            }}',
        ]);
        self::assertSame(200, $response->getStatusCode());
        $matchId = $response->toArray()['id'];

        // Blue 0 - 0 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"back"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 1 - 0 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"red","position":"back"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 1 - 1 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/middlefield-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 1 - 1 Red ''

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/middlefield-goals', $matchId),
            ['body' => '{"team":"red","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 1 - 1 Red '''

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/middlefield-goals', $matchId),
            ['body' => '{"team":"red","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 1 - 1 Red ''''

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"back"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 5 - 1 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 6 - 1 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"red","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 6 - 2 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/middlefield-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 6 - 2 Red ''

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/middlefield-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 6 - 2 Red '''

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/middlefield-goals', $matchId),
            ['body' => '{"team":"red","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 6 - 2 Red ''''

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"red","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 6 - 6 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 7 - 6 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 8 - 6 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 9 - 6 Red

        $response = $client->request(
            'POST',
            sprintf('/api/matches/%s/regular-goals', $matchId),
            ['body' => '{"team":"blue","position":"front"}']
        );
        self::assertSame(200, $response->getStatusCode());

        // Blue 10 - 6 Red

        $response = $client->request('GET', sprintf('/api/matches/%s', $matchId));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(10, $response->toArray()['score']['blue']);
        self::assertSame(6, $response->toArray()['score']['red']);
        self::assertTrue($response->toArray()['isWon']);
    }
}
