# Migration Plan: guzzle-dynamic-pool

## Summary

- **Package**: alexeyshockov/guzzle-dynamic-pool
- **Type**: A (Pure PHP library)
- **Tier**: 1
- **Risk Level**: Low
- **Estimated Scope**: 3 source files, 2 classes + 1 function file (+ 5 example files)

## Change Inventory

### Namespace Renames Required

None. Type A library with no Silverstripe dependencies.

### Composer Dependency Changes

| Package | Current Version | Target Version | Notes |
|---|---|---|---|
| php | ~8.1 | ^8.5 | Major bump |
| guzzlehttp/guzzle | ~7.8 | ^7.8 | Widen constraint |
| phpunit/phpunit | _(not present)_ | ^11.0 | Add for test coverage |

Dev dependencies (`symfony/dom-crawler`, `danielstjules/stringy`) are example-only and can remain as-is.

### API Changes Required

None. No Silverstripe APIs used.

### PHP 8.5 Compatibility Fixes

| Issue | Fix | Files Affected |
|---|---|---|
| Missing return type on `current()` | Add `: mixed` | `src/ExpectingIterator.php` |
| Missing return type on `key()` | Add `: mixed` | `src/ExpectingIterator.php` |
| Untyped properties `$inner`, `$wasValid` | Add property type declarations | `src/ExpectingIterator.php` |
| Untyped properties `$inner`, `$handler` | Add property type declarations | `src/MapIterator.php` |
| Missing return type on `dynamic_pool()` | Add `: PromiseInterface` | `src/functions.php` |
| Untyped parameters on `dynamic_pool()` | Add `iterable`, `callable`, `int` types | `src/functions.php` |
| Untyped properties and params in examples | Add types throughout | `example/src/Page.php`, `example/src/PageReceiver.php`, `example/src/Scraper.php` |
| `$maxLevel = null` implicit nullable | Change to `?int $maxLevel = null` | `example/src/Scraper.php` |

### PHPUnit Migration

No existing test suite. Tests will be created from scratch for the 3 core source files.

| Task | Notes |
|---|---|
| Create `tests/` directory | New |
| Create `phpunit.xml.dist` | PHPUnit 11 config format |
| Write unit tests for `ExpectingIterator` | Iterator behavior |
| Write unit tests for `MapIterator` | Map + cleanup behavior |
| Write unit tests for `dynamic_pool()` | Integration with Guzzle promises |

### Config Changes

None. No `_config.php` or YAML config files.

### Logging Integration

Not applicable for this library. It is a small utility with no logging needs. If logging is added later, it should follow the Catch logging standard with Monolog 3.2+.

## Risk Assessment

| Area | Risk | Notes |
|---|---|---|
| Namespace renames | N/A | No Silverstripe namespaces |
| API changes | N/A | No Silverstripe APIs |
| PHP 8.5 compat | Low | Mostly adding type declarations and return types |
| Composer deps | Low | Guzzle 7 is compatible, just widening PHP constraint |
| Test migration | Low | No existing tests to break; creating from scratch |
| Config changes | N/A | No config files |

## Migration Steps (Ordered)

### Phase 1: composer.json
- [ ] Update `php` requirement from `~8.1` to `^8.5`
- [ ] Update `guzzlehttp/guzzle` from `~7.8` to `^7.8`
- [ ] Add `phpunit/phpunit: ^11.0` to `require-dev`
- [ ] Run `composer validate`

### Phase 2: Namespace Renames
- [ ] _(Skip - no Silverstripe namespaces)_

### Phase 3: API Changes
- [ ] _(Skip - no Silverstripe APIs)_

### Phase 4: PHP 8.5 Compatibility
- [ ] Add property type declarations to `ExpectingIterator` (`\Iterator $inner`, `bool $wasValid`)
- [ ] Add `: mixed` return type to `ExpectingIterator::current()` and `ExpectingIterator::key()`
- [ ] Add property type declarations to `MapIterator` (`\ArrayIterator $inner`, `\Closure|callable $handler`)
- [ ] Add return type `PromiseInterface` to `dynamic_pool()`
- [ ] Add parameter types to `dynamic_pool()`: `iterable $initialWorkload`, `callable $handler`, `int $concurrency`
- [ ] Add type declarations to example files (`Page.php`, `PageReceiver.php`, `Scraper.php`)
- [ ] Fix implicit nullable `$maxLevel` in `Scraper.php`

### Phase 5: Logging Integration
- [ ] _(Skip - not applicable for this utility library)_

### Phase 6: Config Updates
- [ ] _(Skip - no config files)_

### Phase 7: Test Suite
- [ ] Create `phpunit.xml.dist` with PHPUnit 11 schema
- [ ] Create `tests/ExpectingIteratorTest.php`
- [ ] Create `tests/MapIteratorTest.php`
- [ ] Create `tests/DynamicPoolTest.php`
- [ ] Verify tests pass
- [ ] Check coverage meets 80% target

## Dependencies

- **Depends on**: Nothing (Tier 1, no internal deps)
- **Blocks**: No direct dependents in catch-oss, but should be completed before higher-tier repos as good practice
