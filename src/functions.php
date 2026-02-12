<?php

namespace AlexS\GuzzleDynamicPool;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Each;

function dynamic_pool(iterable $initialWorkload, callable $handler, int $concurrency = 10): PromiseInterface
{
    $workload = new \ArrayIterator();
    foreach ($initialWorkload as $item) {
        $workload->append($item);
    }

    // MapIterator is just better for readability
    $generator = new MapIterator(
        // Initial data. This object will be always passed as the second parameter to the callback below.
        $workload,
        $handler
    );

    // The "magic"
    $generator = new ExpectingIterator($generator);

    // And the concurrent runner
    return Each::ofLimit($generator, $concurrency);
}
