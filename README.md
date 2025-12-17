# Laravel Peppol Gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xve/laravel-peppol-gateway.svg?style=flat-square)](https://packagist.org/packages/xve/laravel-peppol-gateway)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/xve/laravel-peppol-gateway/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/xve/laravel-peppol-gateway/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xve/laravel-peppol-gateway.svg?style=flat-square)](https://packagist.org/packages/xve/laravel-peppol-gateway)

Laravel client package for the Peppol Gateway API.

## Installation

```bash
composer require xve/laravel-peppol-gateway
```

Publish the config file:

```bash
php artisan vendor:publish --tag="peppol-gateway-config"
```

## Configuration

Add to your `.env`:

```env
PEPPOL_GATEWAY_URL=https://your-gateway-url.com
PEPPOL_GATEWAY_CLIENT_ID=your-client-id
PEPPOL_GATEWAY_CLIENT_SECRET=your-client-secret
```

## Usage

```php
use Xve\LaravelPeppol\Facades\Peppol;

// Health check
$health = Peppol::health();

// Lookup customer
$participant = Peppol::lookup('BE0123456789');

// Send invoice
$result = Peppol::sendInvoice([...]);

// Send credit note
$result = Peppol::sendCreditNote([...]);

// Check status
$status = Peppol::status($uuid);
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
