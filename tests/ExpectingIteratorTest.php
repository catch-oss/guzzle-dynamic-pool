<?php

namespace AlexS\GuzzleDynamicPool\Tests;

use AlexS\GuzzleDynamicPool\ExpectingIterator;
use ArrayIterator;
use PHPUnit\Framework\TestCase;

class ExpectingIteratorTest extends TestCase
{
    public function testIteratesOverStaticData(): void
    {
        $inner = new ArrayIterator(['a', 'b', 'c']);
        $iterator = new ExpectingIterator($inner);

        $result = [];
        foreach ($iterator as $key => $value) {
            $result[$key] = $value;
        }

        $this->assertSame([0 => 'a', 1 => 'b', 2 => 'c'], $result);
    }

    public function testHandlesEmptyIterator(): void
    {
        $inner = new ArrayIterator([]);
        $iterator = new ExpectingIterator($inner);

        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
        }

        $this->assertSame([], $result);
    }

    public function testHandlesSingleElement(): void
    {
        $inner = new ArrayIterator(['only']);
        $iterator = new ExpectingIterator($inner);

        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
        }

        $this->assertSame(['only'], $result);
    }

    public function testPicksUpDynamicallyAddedItems(): void
    {
        $inner = new ArrayIterator(['first']);
        $iterator = new ExpectingIterator($inner);

        $result = [];
        foreach ($iterator as $value) {
            $result[] = $value;
            if ($value === 'first') {
                $inner->append('second');
            }
        }

        $this->assertSame(['first', 'second'], $result);
    }

    public function testRewindResetsIteration(): void
    {
        $inner = new ArrayIterator(['a', 'b']);
        $iterator = new ExpectingIterator($inner);

        // Iterate once
        $first = [];
        foreach ($iterator as $value) {
            $first[] = $value;
        }

        // Rewind and iterate again
        $iterator->rewind();
        $second = [];
        foreach ($iterator as $value) {
            $second[] = $value;
        }

        $this->assertSame($first, $second);
    }

    public function testValidReturnsFalseWhenExhausted(): void
    {
        $inner = new ArrayIterator([]);
        $iterator = new ExpectingIterator($inner);

        $iterator->rewind();

        $this->assertFalse($iterator->valid());
    }

    public function testCurrentReturnsInnerValue(): void
    {
        $inner = new ArrayIterator(['value']);
        $iterator = new ExpectingIterator($inner);

        $iterator->rewind();

        $this->assertSame('value', $iterator->current());
    }

    public function testKeyReturnsInnerKey(): void
    {
        $inner = new ArrayIterator(['a' => 1, 'b' => 2]);
        $iterator = new ExpectingIterator($inner);

        $iterator->rewind();

        $this->assertSame('a', $iterator->key());
    }
}
