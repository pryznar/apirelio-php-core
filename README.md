# Apirelio PHP Core

[![Packagist](https://img.shields.io/packagist/v/apirelio/php-core?style=flat-square&logo=packagist)](https://packagist.org/packages/apirelio/php-core)
[![Live demo](https://img.shields.io/badge/live_demo-explore-8EF0B5?style=flat-square&logo=googlechrome&logoColor=0B0E10)](https://apirelio.com/demo?utm_source=github&utm_medium=readme&utm_campaign=php-core)

## See the customer behind every API request

[![Apirelio live demo dashboard](https://apirelio.com/img/apirelio-live-demo-dashboard.jpg)](https://apirelio.com/demo?utm_source=github&utm_medium=readme&utm_campaign=php-core)

Follow a release regression from the failing endpoint to the exact customer accounts it affects in the public, read-only workspace.

**[Explore the live demo →](https://apirelio.com/demo?utm_source=github&utm_medium=readme&utm_campaign=php-core)**

## Try it in 30 seconds

```bash
composer require apirelio/php-core
export APIRELIO_API_KEY=apr_live_your_project_key
```

Copy the minimal setup below or run the [quickstart example](./examples/quickstart). Delivery is fail-safe and no request or response payloads are captured.


[Documentation](https://apirelio.com/docs/php) · [Packagist](https://packagist.org/packages/apirelio/php-core) · [Apirelio](https://apirelio.com)

> Build customer-aware PHP API monitoring integrations on a privacy-safe event contract with fail-safe delivery.

Framework-independent building blocks shared by the official Apirelio PHP SDKs.
Applications normally install a framework adapter such as
`apirelio/laravel`, `apirelio/symfony` or `apirelio/nette`; Composer installs
this package transitively.

The core owns the stable event contract, customer and application value
objects, metadata privacy rules, error-code extraction, HTTP retry orchestration
and the locked local file buffer. Framework packages only adapt requests,
routes, dependency injection and their native queue systems.

## Requirements

- PHP 8.2, 8.3 or 8.4

## Direct extension

Custom framework adapters can implement:

```php
Apirelio\Core\Contracts\IngestionClient
Apirelio\Core\Contracts\EventTransport
```

Use `EventFactory` with `EventContext` to produce the same ingestion payload as
the official Laravel, Symfony and Nette packages.
