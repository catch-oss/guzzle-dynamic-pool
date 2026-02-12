<?php

namespace AlexS\GuzzleDynamicPool;

use ArrayIterator;
use Closure;

/**
 * @internal
 */
// Do not extend IteratorIterator, because it cashes the return values somehow!
class MapIterator implements \Iterator
{
    private ArrayIterator $inner;
    /** @var callable */
    private Closure $handler;

    public function __construct(ArrayIterator $inner, callable $handler)
    {
        $this->inner = $inner;
        $this->handler = $handler(...);
    }

    public function next(): void
    {
        // Cleanup current (processed) entry. We cannot unset completely, unfortunately, because then indexing will be
        // broken (and the whole execution will be broken).
        $this->valid() && $this->inner->offsetSet($this->inner->key(), null);

        $this->inner->next();
    }

    public function current(): mixed
    {
        return ($this->handler)($this->inner->current(), $this->inner);
    }

    public function rewind(): void
    {
        $this->inner->rewind();
    }

    public function key(): mixed
    {
        return $this->inner->key();
    }

    public function valid(): bool
    {
        return $this->inner->valid();
    }
}
