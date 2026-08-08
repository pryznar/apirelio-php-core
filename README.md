# Apirelio PHP Core

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
