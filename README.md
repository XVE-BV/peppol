# Laravel Peppol Gateway

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xve/laravel-peppol-gateway.svg?style=flat-square)](https://packagist.org/packages/xve/laravel-peppol-gateway)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/xve/laravel-peppol-gateway/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/xve/laravel-peppol-gateway/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xve/laravel-peppol-gateway.svg?style=flat-square)](https://packagist.org/packages/xve/laravel-peppol-gateway)

Laravel client package for the Peppol Gateway API. Uses the [Action Pattern](docs/action-pattern.md) for swappable, testable operations. See [Examples](docs/examples.md) for detailed usage.

## Installation

```bash
composer require xve/laravel-peppol-gateway
```

Add to your `.env`:

```env
PEPPOL_GATEWAY_URL=https://your-gateway-url.com
PEPPOL_GATEWAY_CLIENT_ID=your-client-id
PEPPOL_GATEWAY_CLIENT_SECRET=your-client-secret
```

## Usage

See [Examples](docs/examples.md) for detailed usage.

### Available Actions

| Action | Description |
|--------|-------------|
| `HealthCheckAction` | Verify API connectivity |
| `LookupParticipantAction` | Check if VAT is Peppol-capable |
| `SendInvoiceAction` | Send invoice (JSON) |
| `SendCreditNoteAction` | Send credit note (JSON) |
| `GetInvoiceStatusAction` | Check invoice/credit note status |

## Events

All actions dispatch events after successful execution:

| Action | Event |
|--------|-------|
| `HealthCheckAction` | `HealthChecked` |
| `LookupParticipantAction` | `ParticipantLookedUp` |
| `SendInvoiceAction` | `InvoiceSent` |
| `SendCreditNoteAction` | `CreditNoteSent` |
| `GetInvoiceStatusAction` | `InvoiceStatusRetrieved` |

## Customization

Actions can be swapped via config. See [Extending an Action](docs/action-pattern.md#extending-an-action) for details.

## Configuration

Publish the config file (optional):

```bash
php artisan vendor:publish --tag="peppol-gateway-config"
```

## Stubs

Publish the migration stubs (optional):

```bash
php artisan vendor:publish --tag="peppol-gateway-stubs"
```

This will publish the following stubs to `stubs/peppol-gateway/`:

- `add_peppol_fields_to_table.php.stub` - Migration for adding Peppol participant fields to a table
- `add_peppol_tracking_to_table.php.stub` - Migration for adding Peppol document tracking fields to a table

Use these stubs as templates for your own migrations. Replace `{{ table }}` and `{{ after_column }}` placeholders with your values.

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
