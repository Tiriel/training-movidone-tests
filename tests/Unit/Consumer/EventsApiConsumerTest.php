<?php

namespace App\Tests\Unit\Consumer;

use App\Consumer\EventsApiConsumer;
use App\Entity\Event;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class EventsApiConsumerTest extends TestCase
{
    public function testConsumerReturnsArrayOfEventsWithoutName(): void
    {
        $response = new JsonMockResponse(['name' => 'Test Event']);

        $client = new MockHttpClient($response);

        $consumer = new EventsApiConsumer($client);

        $result = $consumer->searchByName();
        $this->assertIsArray($result);
        $this->assertSame('Test Event', $result['name']);
    }

    public function testConsumerReturnsArrayOfEventsWithName(): void
    {
        $response = new JsonMockResponse(['name' => 'Test Event']);

        $client = new MockHttpClient($response);

        $consumer = new EventsApiConsumer($client);

        $result = $consumer->searchByName('Test Event');

        $this->assertArrayHasKey('query', $response->getRequestOptions());
        $this->assertSame($response->getRequestOptions()['query'], ['name' => 'Test Event']);
        $this->assertIsArray($result);
        $this->assertSame('Test Event', $result['name']);
    }
}
