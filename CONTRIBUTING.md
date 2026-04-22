# Contributing

Thanks for contributing to `wwaz/components-php`.

## Local setup

1. Clone the repository.
2. Install dependencies:

```bash
composer install
```

## Development commands

- Run tests:

```bash
composer test
```

- Check coding style:

```bash
composer lint
```

- Auto-fix coding style:

```bash
composer format
```

- Run static analysis:

```bash
composer stan
```

- Run full local quality gate:

```bash
composer qa
```

## Pull request expectations

- Keep changes focused and small when possible.
- Ensure `composer qa` passes before opening a PR.
- Avoid breaking public APIs (`Factory`, `Component`, `Config`) unless explicitly planned and documented.
- Add or update tests whenever behavior changes.

## Versioning and releases

- Package versions are managed via Git tags.
- Do not add a static `version` field to `composer.json`.
