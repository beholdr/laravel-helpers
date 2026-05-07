# AGENTS.md

## Repository Shape
- This is a PHP 8.3+ Laravel package, not a full Laravel app; source is PSR-4 autoloaded from `src/` as `Beholdr\LaravelHelpers\`.
- Package bootstrapping is in `src/LaravelHelpersServiceProvider.php`; it uses Spatie Laravel Package Tools and publishes `config/helpers.php` via `hasConfigFile()`.
- Tests run through Pest on Orchestra Testbench; `tests/TestCase.php` registers the package service provider and sets the default database connection to `testing`.

## Commands
- Install/update dependencies with Composer; `post-autoload-dump` runs `composer run prepare`, which calls `vendor/bin/testbench package:discover --ansi`.
- Run the full test suite: `composer test` or `vendor/bin/pest`.
- Run a focused test file: `vendor/bin/pest tests/UtmTest.php`.
- Run static analysis: `composer analyse` or `vendor/bin/phpstan --memory-limit=1G analyse`.
- Format PHP: `composer format` or `vendor/bin/pint`; there is no custom Pint config in this repo.
- CI runs tests with `vendor/bin/pest --ci`, not the Composer script.

## Verification Notes
- GitHub Actions tests PHP `8.3` and `8.4` across Laravel `11.*` and `12.*`, `prefer-lowest` and `prefer-stable`, on Ubuntu and Windows.
- PHPStan runs at level 5 with `phpstan-baseline.neon`, only analyzes `src/`, and writes cache under `build/phpstan`.
- PHPUnit randomizes test order and fails on warnings, risky tests, empty suites, and output during tests.
- The arch test forbids `dd`, `dump`, and `ray` usages.

## Generated/Ignored Files
- Do not preserve changes under `vendor/`, `build/`, `.phpunit.cache/`, `coverage/`, `phpunit.xml`, `phpstan.neon`, or `testbench.yaml`; these are ignored local/generated files.
- `composer.lock` is ignored by `.gitignore` even though it may exist locally; do not assume lockfile changes are intended for commits.

## Repo-Specific Gotchas
- The executable config is the source of truth when README examples drift; for example `config/helpers.php` defaults `http_client_log` to `false`.
- The package advertises a `LaravelHelpers` facade alias in `composer.json`, but there is no `src/Facades/LaravelHelpers.php` in the current tree; verify before relying on that alias.
