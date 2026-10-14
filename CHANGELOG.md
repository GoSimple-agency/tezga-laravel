# Changelog

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and
[Semantic Versioning](https://semver.org/).

## [0.1.0] - 2026-10-15

### Added
- Service provider binding a configured `TezgaClient` singleton, plus a `Tezga` facade.
- Signed webhook route (`POST /tezga/webhook`) that verifies `X-Tezga-Signature` and fires
  `TezgaWebhookReceived`; registered only when a webhook secret is configured.
- Publishable config (`tezga-config`), driven by environment variables.

[0.1.0]: https://github.com/GoSimple-agency/tezga-laravel/releases/tag/v0.1.0
