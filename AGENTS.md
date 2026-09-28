# Contributte Application

Instructions for AI coding agents working in this repository.

## Overview

`contributte/application` adds responses and UI helpers to `nette/application`: file and data responses
(`CSVResponse`, `ImageResponse`, `JsonPrettyResponse`, `PSR7StreamResponse`, `StringResponse`, `XmlResponse`),
streamed responses built from adapters (`FlyResponse`, `FlyFileResponse`), `BasePresenter`, the
`StructuredTemplates` trait and empty `NullControl` and `NullComponent`. It is a library of classes users create by
hand, with no DI extension and no configuration.

- **PHP**: 8.2 to 8.5 (`>=8.2` in `composer.json`)
- **Package**: `contributte/application`, namespace `Contributte\Application\`
- **Integrates**: `nette/application` 3.2.6+ or 4.x
- **Optional**: `psr/http-message` for `PSR7StreamResponse`, `tracy/tracy` for `CSVResponse` (both only in
  `require-dev`)

## Documentation

- `.docs/README.md` is the user documentation and the page on contributte.org. Update it in the same pull request
  when a response, adapter or constructor argument changes.
- Organization rules for code, tests and tooling are in
  [contributte/contributte specs](https://github.com/contributte/contributte/tree/master/specs).

## Commands

```bash
# Install dependencies
make install

# Run all checks (PHPStan level 9 + code style), does not run tests
make qa

# Fix code style
make csf

# Run all tests, or one file
make tests
vendor/bin/tester -s -p php --colors 1 -C tests/Cases/Response/CsvResponse.phpt

# Generate code coverage (coverage.html)
make coverage
```

CI runs the tests on PHP 8.2 to 8.5 and once on PHP 8.2 with `--prefer-lowest`.

## Conventions

- Tests in `tests/Cases` mirror `src/` (`Response/Fly/Adapter/`, `UI/`) and use `Toolkit::test()`. A response is
  tested by calling `send()` with `Nette\Http\Request` and `Response` inside `ob_start()` and asserting the output.
- The test for `CSVResponse.php` is `CsvResponse.phpt`; keep the class name, since renaming it breaks users.
- PHPStan analyses `.phpt` files too (`fileExtensions` in `phpstan.neon`), so test code must pass level 9.

## Traps

- **`CSVResponse::send()` sets `Tracy\Debugger::$productionMode = true`** when Tracy is installed, to keep the bar
  out of the file. It is a global side effect that stays after the response; don't copy it to other responses.
- **`CSVResponse` appends `.csv` to the name only when the name contains no `.csv`.** Encoding `windows-1250`
  goes through `iconv()`, other encodings through `mb_convert_encoding()`; tests cover `utf-8`, `utf-16` and
  `windows-1250`.
- **`PSR7StreamResponse` needs `psr/http-message`, which is not in `require`.** Keep it optional; the class only
  fails when a user creates it without the package.
- **`ProcessAdapter` runs its command through `popen()`.** The command is a shell string, so never build it from
  request data in examples or tests.
- **`StructuredTemplates` looks for templates next to the presenter class file**, in `templates/{view}.latte` and
  `templates/@layout.latte` of every parent class. Classes in the `Nette\` namespace are skipped, and a layout with
  a slash is used as a path.
- **`ImageResponse` checks a file path in the constructor but loads it in `send()`.** A missing file throws
  `Nette\InvalidArgumentException` early; a broken image fails only when the response is sent.
- Usage and examples for users live in `.docs/README.md`, not here.
