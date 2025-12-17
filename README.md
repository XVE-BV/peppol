# Laravel Peppol Gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xve/laravel-peppol-gateway.svg?style=flat-square)](https://packagist.org/packages/xve/laravel-peppol-gateway)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/xve/laravel-peppol-gateway/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/xve/laravel-peppol-gateway/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xve/laravel-peppol-gateway.svg?style=flat-square)](https://packagist.org/packages/xve/laravel-peppol-gateway)

Laravel client package for the Peppol Gateway API.

## Features

- Health check / ping
- Customer lookup (VAT to Peppol participant ID)
- Send invoices (JSON)
- Send credit notes (JSON)
- Check invoice/credit note status

## Installation

```bash
composer require xve/laravel-peppol-gateway
```

## Configuration

Add to your `.env`:

```env
PEPPOL_GATEWAY_URL=https://your-gateway-url.com
PEPPOL_GATEWAY_CLIENT_ID=your-client-id
PEPPOL_GATEWAY_CLIENT_SECRET=your-client-secret
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="peppol-gateway-config"
```

## Usage

### Health Check

```php
use Xve\LaravelPeppol\Actions\HealthCheckAction;

$action = app(HealthCheckAction::class);
$health = $action->execute();

$health->ok;        // true/false
$health->status;    // 200
```

### Customer Lookup

```php
use Xve\LaravelPeppol\Actions\LookupParticipantAction;

$action = app(LookupParticipantAction::class);
$participant = $action->execute('BE0123456789');

$participant->participantId;  // "9925:BE0123456789"
$participant->capable;        // true/false
```

### Send Invoice

```php
use Xve\LaravelPeppol\Actions\SendInvoiceAction;

$action = app(SendInvoiceAction::class);
$result = $action->execute([
    'type' => 'invoice',
    'id' => 'INV-2025-001',
    'issue_date' => '2025-01-15',
    'due_date' => '2025-02-15',
    'buyer_vat' => 'BE0123456789',
    'total' => 121.00,
    'currency' => 'EUR',
    'metadata' => [
        'buyer_name' => 'Acme Corp',
        'lines' => [
            [
                'description' => 'Consulting',
                'quantity' => 1,
                'unit_price' => 100.00,
                'vat_rate' => 21.00,
            ],
        ],
    ],
]);

$result->status;  // "queued"
$result->uuid;    // "550e8400-e29b-41d4-a716-446655440000"
```

### Send Credit Note

```php
use Xve\LaravelPeppol\Actions\SendCreditNoteAction;

$action = app(SendCreditNoteAction::class);
$result = $action->execute([
    'type' => 'credit_note',
    'total' => -121.00,
    // ...
]);
```

### Check Status

```php
use Xve\LaravelPeppol\Actions\GetInvoiceStatusAction;

$action = app(GetInvoiceStatusAction::class);
$status = $action->execute('550e8400-e29b-41d4-a716-446655440000');

$status->status;     // "delivered", "rejected", "failed", etc.
$status->flowinId;   // External reference
```

### Model Integration

Add the interface and trait to your model:

```php
use Xve\LaravelPeppol\Models\Concerns\HasPeppolId;
use Xve\LaravelPeppol\Models\Concerns\InteractsWithPeppol;

class Customer extends Model implements HasPeppolId
{
    use InteractsWithPeppol;
}
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
