<?php

namespace App\Tests\App\Transformer;

use App\Entity\Event;
use App\Transformer\ApiToEventTransformer;
use PHPUnit\Framework\TestCase;

class ApiToEventTransformerTest extends TestCase
{
    public function testMethodTransformReturnsEvent(): void
    {
        $data = [
            'name' => 'Event',
            'description' => 'Event description',
            'accessible' => true,
            'startDate' => '2018-01-01',
            'endDate' => '2018-01-01',
        ];

        $transformer = new ApiToEventTransformer();
        $result = $transformer->transform($data);

        $this->assertInstanceOf(Event::class, $result);
        $this->assertSame('Event', $result->getName());
        $this->assertSame('Event description', $result->getDescription());
        $this->assertTrue($result->isAccessible());
        $this->assertEquals(new \DateTimeImmutable('2018-01-01'), $result->getStartAt());
        $this->assertEquals(new \DateTimeImmutable('2018-01-01'), $result->getEndAt());
    }

    public function testMethodTransformThrowsWhenMissingData(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing keys in data provided by the API');

        $data = [
            'name' => 'Event',
            'accessible' => true,
            'endDate' => '2018-01-01',
        ];

        $transformer = new ApiToEventTransformer();
        $result = $transformer->transform($data);
    }
}
