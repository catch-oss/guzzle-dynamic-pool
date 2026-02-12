<?php

namespace AlexS\GuzzleDynamicPool\Tests;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;

use function AlexS\GuzzleDynamicPool\dynamic_pool;

class DynamicPoolTest extends TestCase
{
    public function testReturnsPromiseInterface(): void
    {
        $promise = dynamic_pool(
            ['item'],
            fn($item) => new FulfilledPromise(null),
            1
        );

        $this->assertInstanceOf(PromiseInterface::class, $promise);
    }

    public function testProcessesAllInitialWorkload(): void
    {
        $processed = [];

        $promise = dynamic_pool(
            ['a', 'b', 'c'],
            function ($item) use (&$processed) {
                $processed[] = $item;
                return new FulfilledPromise(null);
            },
            5
        );

        $promise->wait();

        $this->assertSame(['a', 'b', 'c'], $processed);
    }

    public function testHandlesDynamicAdditions(): void
    {
        $processed = [];

        $promise = dynamic_pool(
            ['first'],
            function ($item, \ArrayIterator $workload) use (&$processed) {
                $processed[] = $item;
                if ($item === 'first') {
                    $workload->append('second');
                }
                return new FulfilledPromise(null);
            },
            1
        );

        $promise->wait();

        $this->assertSame(['first', 'second'], $processed);
    }

    public function testRespectsDefaultConcurrency(): void
    {
        // Simply verify it doesn't throw with default concurrency
        $promise = dynamic_pool(
            ['item'],
            fn($item) => new FulfilledPromise(null)
        );

        $promise->wait();

        $this->assertTrue(true);
    }

    public function testHandlesEmptyWorkload(): void
    {
        $processed = [];

        $promise = dynamic_pool(
            [],
            function ($item) use (&$processed) {
                $processed[] = $item;
                return new FulfilledPromise(null);
            }
        );

        $promise->wait();

        $this->assertSame([], $processed);
    }

    public function testHandlesMultipleDynamicAdditions(): void
    {
        $processed = [];

        $promise = dynamic_pool(
            ['start'],
            function ($item, \ArrayIterator $workload) use (&$processed) {
                $processed[] = $item;
                if ($item === 'start') {
                    $workload->append('child-1');
                    $workload->append('child-2');
                } elseif ($item === 'child-1') {
                    $workload->append('grandchild');
                }
                return new FulfilledPromise(null);
            },
            1
        );

        $promise->wait();

        $this->assertContains('start', $processed);
        $this->assertContains('child-1', $processed);
        $this->assertContains('child-2', $processed);
        $this->assertContains('grandchild', $processed);
        $this->assertCount(4, $processed);
    }
}
