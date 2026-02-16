# Roadmap

This roadmap captures likely next improvements for the package.

## Near Term (v1.x)

- Add optional validation of `.env.example` values (not only key presence).
- Add support for validating multiple files in one run (for example `.env`, `.env.testing`, `.env.staging`).
- Add `--fail-on-warning` option for stricter CI behavior.
- Add richer JSON output (`validated_keys`, `missing_keys`, `invalid_keys`, `duration_ms`).
- Add dedicated exit codes per failure type (invalid env, unknown preset, missing env file).
- Improve dotenv parser compatibility (escaped values, multiline values, edge cases).

## Presets

- Add framework-versioned presets (Laravel 10/11/12 specific defaults).
- Add preset inheritance (`my-preset` extends `standard`).
- Add optional remote preset loading (from package or URL) with caching.
- Add command to scaffold custom preset blocks into config.

## Developer Experience

- Add `env:validate --explain` mode to show which rule failed and why.
- Add `env:validate --dry-run` mode to preview what will be validated.
- Add table output format for CLI readability.
- Add docs section with copy-paste CI examples (GitHub Actions, GitLab CI, Bitbucket).

## Quality and Tooling

- Add static analysis (PHPStan/Larastan) with a baseline and strictness plan.
- Add mutation or robustness tests around parser behavior.
- Add CI matrix for Laravel versions in addition to PHP versions.
- Add contract tests for command behavior and JSON schema stability.

## Integrations

- Add optional pre-deploy check command for common deployment pipelines.
- Add optional health endpoint integration for runtime env validation status.
- Add first-party support docs for Forge, Vapor, and containerized deployments.

## Release and Governance

- Automate changelog generation from conventional commits.
- Define semantic versioning policy and deprecation policy in docs.
- Add `CONTRIBUTING.md` and issue/PR templates.
- Add security policy (`SECURITY.md`) and disclosure process.
