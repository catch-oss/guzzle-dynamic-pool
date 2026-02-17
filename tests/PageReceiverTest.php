<?php

namespace AlexS\GuzzleDynamicPool\Tests;

use AlexS\GuzzleDynamicPool\Example\PageReceiver;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class PageReceiverTest extends TestCase
{
    public function testGeneratePageThrowsWhenStatsUnavailable(): void
    {
        $receiver = new PageReceiver(0);
        $receiver(new Response(200, [], '<html></html>'));

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('transfer stats unavailable');

        $receiver->generatePage();
    }

    public function testGeneratePageSucceedsWithStats(): void
    {
        $receiver = new PageReceiver(1);

        $stats = new TransferStats(new Request('GET', 'https://example.com'), new Response(200));
        ($receiver->onStats())($stats);
        $receiver(new Response(200, [], '<html></html>'));

        $page = $receiver->generatePage();

        $this->assertSame(1, $page->getLevel());
        $this->assertSame(200, $page->getStatusCode());
    }
}
