# Tracium PHP Core

Framework-independent building blocks shared by the official Tracium PHP SDKs.
Applications normally install a framework adapter such as
`tracium/laravel` or `tracium/symfony`; Composer installs this package
transitively.

The core owns the stable event contract, customer and application value
objects, metadata privacy rules, error-code extraction, HTTP retry orchestration
and the locked local file buffer. Framework packages only adapt requests,
routes, dependency injection and their native queue systems.

## Requirements

- PHP 8.2, 8.3 or 8.4

## Direct extension

Custom framework adapters can implement:

```php
Tracium\Core\Contracts\IngestionClient
Tracium\Core\Contracts\EventTransport
```

Use `EventFactory` with `EventContext` to produce the same ingestion payload as
the official Laravel and Symfony packages.
