<?php

declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class TestGetEndpointTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:9501',
            'http_errors' => false
        ]);
    }

    public function testGetEndpointReturnsSuccessResponse(): void
    {
        $response = $this->client->get('/test');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));

        $body = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($body);
        $this->assertArrayHasKey('status', $body);
        $this->assertEquals('success', $body['status']);
    }
}
