# Guzzle Dynamic Pool

Dynamic, growable request pools for [Guzzle](https://docs.guzzlephp.org/). Process concurrent HTTP requests where the workload isn't known upfront -- new requests can be added from within handlers as results come back.

<!-- PROJECT SHIELDS -->
[![SonarCloud](https://github.com/catch-oss/guzzle-dynamic-pool/actions/workflows/sonar.yml/badge.svg)](https://github.com/catch-oss/guzzle-dynamic-pool/actions/workflows/sonar.yml)
[![Test](https://github.com/catch-oss/guzzle-dynamic-pool/actions/workflows/test.yml/badge.svg)](https://github.com/catch-oss/guzzle-dynamic-pool/actions/workflows/test.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=catch-design_catch-oss-guzzle-dynamic-pool&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=catch-design_catch-oss-guzzle-dynamic-pool)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=catch-design_catch-oss-guzzle-dynamic-pool&metric=coverage)](https://sonarcloud.io/component_measures?id=catch-design_catch-oss-guzzle-dynamic-pool)

## The Problem

Guzzle's built-in `Pool` requires you to know all your requests upfront. But many real-world tasks are recursive -- a web crawler discovers new URLs as it processes pages, a paginated API reveals the next page in each response, or a dependency resolver finds new packages to fetch as it resolves the tree.

`dynamic_pool` wraps Guzzle's concurrent execution with an `ArrayIterator` workload that handlers can append to at runtime. The pool keeps running until the workload is fully drained.

## Installation

```bash
composer require alexeyshockov/guzzle-dynamic-pool
```

## Quick Start

```php
use function AlexS\GuzzleDynamicPool\dynamic_pool;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\FulfilledPromise;
use Psr\Http\Message\ResponseInterface;

$client = new Client();

$pool = dynamic_pool(
    // Initial workload -- at least one item required
    ['https://example.com'],

    // Handler receives each item + the workload ArrayIterator
    function (string $url, \ArrayIterator $workload) use ($client) {
        return $client->getAsync($url)->then(
            function (ResponseInterface $response) use ($url, $workload) {
                echo $url . ': ' . $response->getStatusCode() . PHP_EOL;

                // Dynamically add more work based on the response
                // $workload->append('https://example.com/next-page');
            }
        );
    },

    // Concurrency limit (default: 10)
    5
);

// Block until all work (including dynamically added) is done
$pool->wait();
```

## API

### `dynamic_pool(iterable $initialWorkload, callable $handler, int $concurrency = 10): PromiseInterface`

| Parameter | Type | Description |
|-----------|------|-------------|
| `$initialWorkload` | `iterable` | Seed items to start processing. Must contain at least one item. |
| `$handler` | `callable` | `function(mixed $item, \ArrayIterator $workload): PromiseInterface` -- processes each item and returns a promise. Append to `$workload` to add more work. |
| `$concurrency` | `int` | Maximum parallel requests (default `10`). |

**Returns** a `PromiseInterface` that resolves when all work is complete.

## How It Works

The library uses two custom iterators:

- **`MapIterator`** -- wraps the `ArrayIterator` workload, passing each item through your handler and cleaning up processed entries to free memory.
- **`ExpectingIterator`** -- watches for the workload growing between iterations. When the inner iterator was previously exhausted but new items appear, it picks them up instead of stopping.

These are combined with Guzzle's `Each::ofLimit()` for concurrency control.

## Examples

The `example/` directory contains two runnable demos:

- **`example/app1.php`** -- basic dynamic pool that adds URLs from a queue as each request completes
- **`example/scraper.php`** -- recursive web crawler that discovers and follows links with depth limiting

## Compatibility

| Branch | PHP | Guzzle |
|---------|-----|--------|
| release/6 | ^8.5 | ^7.8 |
| release/5 | ~8.1 | ~7.8 |

## License

MIT -- see [LICENSE](LICENSE) for details.

Originally created by [Alexey Shokov](https://github.com/alexeyshockov). See the [StackOverflow answer](https://stackoverflow.com/a/43525426/322079) that inspired this library.
