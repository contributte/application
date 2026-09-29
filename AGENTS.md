# Contributte Application

Extra components, responses and presenters for Nette Application.

## Stack

- Language: PHP >=8.2
- Framework: Nette Application 3.2/4
- Tests: Nette Tester; static analysis: PHPStan (level 9); code style: Contributte coding standard

## Development

```bash
make install     # install dependencies
make qa          # PHPStan and code style
make csf         # fix code style
make tests       # run all tests
vendor/bin/tester -s -p php --colors 1 -C tests/Cases/Response/XmlResponse.phpt   # run one test file
make coverage    # code coverage
```

Run `make` to list every target.

## Principles

- KISS: write the simplest code that works; no speculative abstractions.
- DRY: one source of truth; reuse existing code before adding new.
- YAGNI: build what is needed now, not what might be needed later.
- Small, final classes with typed properties and `declare(strict_types = 1)`.
- Every change comes with a test; `make qa tests` must pass before a commit.
