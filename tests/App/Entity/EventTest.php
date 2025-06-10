<?php

namespace App\Tests\App\Entity;

use App\Entity\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testGettersReturnsSetData(): void
    {
        $date = new \DateTimeImmutable();
        $event = (new Event())
            ->setName('Test Event')
            ->setDescription('Test Event')
            ->setAccessible(true)
            ->setStartAt($date)
        ;

        $this->assertSame('Test Event', $event->getName());
        $this->assertSame('Test Event', $event->getDescription());
        $this->assertSame($date, $event->getStartAt());
        $this->assertTrue($event->isAccessible());
    }
}
