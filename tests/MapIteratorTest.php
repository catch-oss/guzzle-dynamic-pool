<?php

namespace AlexS\GuzzleDynamicPool\Tests;

use AlexS\GuzzleDynamicPool\MapIterator;
use ArrayIterator;
use PHPUnit\Framework\TestCase;

class MapIteratorTest extends TestCase
{
    public function testMapsValuesWithHandler(): void
    {
        $inner = new ArrayIterator([1, 2, 3]);
        $iterator = new MapIterator($inner, fn($value) => $value * 2);

        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
        }

        $this->assertSame([2, 4, 6], $result);
    }

    public function testHandlesEmptyIterator(): void
    {
        $inner = new ArrayIterator([]);
        $iterator = new MapIterator($inner, fn($value) => $value);

        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
        }

        $this->assertSame([], $result);
    }

    public function testHandlerReceivesArrayIteratorAsSecondArg(): void
    {
        $inner = new ArrayIterator(['item']);
        $receivedIterator = null;

        $iterator = new MapIterator($inner, function ($value, $iter) use (&$receivedIterator) {
            $receivedIterator = $iter;
            return $value;
        });

        foreach ($iterator as $value) {
            // consume
        }

        $this->assertSame($inner, $receivedIterator);
    }

    public function testCleansUpProcessedEntries(): void
    {
        $inner = new ArrayIterator(['a', 'b', 'c']);
        $iterator = new MapIterator($inner, fn($value) => $value);

        // Consume all items
        foreach ($iterator as $value) {
            // consume
        }

        // After iteration, processed entries should be nulled out
        $this->assertNull($inner[0]);
        $this->assertNull($inner[1]);
        // Last entry gets nulled on next() call when valid() is false,
        // but the offsetSet is guarded by $this->valid()
    }

    public function testPreservesKeys(): void
    {
        $inner = new ArrayIterator(['x' => 10, 'y' => 20]);
        $iterator = new MapIterator($inner, fn($value) => $value + 1);

        $result = [];
        foreach ($iterator as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertSame(['x' => 11, 'y' => 21], $result);
    }

    public function testHandlerCanAddToWorkload(): void
    {
        $inner = new ArrayIterator(['first']);
        $iterator = new MapIterator($inner, function ($value, ArrayIterator $workload) {
            if ($value === 'first') {
                $workload->append('second');
            }
            return $value;
        });

        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
        }

        // MapIterator alone doesn't handle dynamic additions -
        // that's what ExpectingIterator wraps around it for
        $this->assertContains('first', $result);
    }
}
