<?php

namespace App\Tests\Unit\Search;

use App\Entity\Event;
use App\Search\CacheableEventSearch;
use App\Search\EventSearchInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheableEventSearchTest extends TestCase
{
    public function testCacheMissCallsInnerEventSearchInterface(): void
    {
        $event = (new Event())->setName('Test Event');

        $mCache = $this->createMock(CacheInterface::class);
        $mCache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function($key, $callback) {
                return $callback($this->createMock(ItemInterface::class));
            });

        $mSearch = $this->createMock(EventSearchInterface::class);
        $mSearch->expects($this->once())
            ->method('searchByName')
            ->with('Test Event')
            ->willReturn([$event]);

        $eventSearch = new CacheableEventSearch($mSearch, $mCache);
        $result = $eventSearch->searchByName('Test Event');

        $this->assertIsArray($result);
        $this->assertSame($event, $result[0]);
    }

    public function testCacheHitReturnsResultFromCache(): void
    {
        $event = (new Event())->setName('Test Event');

        $mCache = $this->createMock(CacheInterface::class);
        $mCache->expects($this->once())
            ->method('get')
            ->willReturn([$event]);

        $mSearch = $this->createMock(EventSearchInterface::class);
        $mSearch->expects($this->never())
            ->method('searchByName');

        $eventSearch = new CacheableEventSearch($mSearch, $mCache);
        $result = $eventSearch->searchByName('Test Event');

        $this->assertIsArray($result);
        $this->assertSame($event, $result[0]);
    }
}
