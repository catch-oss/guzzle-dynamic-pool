<?php

namespace AlexS\GuzzleDynamicPool;

/**
 * @internal
 */
// Do not extend IteratorIterator, because it cashes the return values somehow!
class ExpectingIterator implements \Iterator
{
    private \Iterator $inner;
    private bool $wasValid = false;

    public function __construct(\Iterator $inner)
    {
        $this->inner = $inner;
    }

    public function next(): void
    {
        if (!$this->wasValid && $this->valid()) {
            // Just do nothing, because the inner iterator has became valid
        } else {
            $this->inner->next();
        }

        $this->wasValid = $this->valid();
    }

    public function current(): mixed
    {
        return $this->inner->current();
    }

    public function rewind(): void
    {
        $this->inner->rewind();

        $this->wasValid = $this->valid();
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
