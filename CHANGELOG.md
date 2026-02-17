# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Changed
- Updated PHP requirement from `~8.1` to `^8.5`
- Updated Guzzle requirement from `~7.8` to `^7.8`
- Added property type declarations and return types throughout source and examples
- Converted callable properties to Closure using first-class callable syntax
- Replaced `call_user_func()` with direct closure invocation

### Added
- PHPUnit 11 test suite (20 tests, 100% code coverage)
- `phpunit.xml.dist` configuration
- `MIGRATION-PLAN.md` documenting all changes
- GitHub Actions CI workflows (test, review, sonar)

### Fixed
- PHP 8.5 compatibility: implicit nullable parameter (`$maxLevel`) in example Scraper
- PHP 8.5 compatibility: missing return type declarations on Iterator methods
