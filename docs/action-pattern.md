# Action Pattern

This package uses the **Action Pattern** (also known as Single Action Classes or Command Pattern). This document explains the design choice for developers unfamiliar with the pattern.

## TL;DR - SWOT Analysis

| | |
|---|---|
| **Strengths** | Single responsibility, easy to test, swappable via config, explicit dependencies, self-documenting folder structure |
| **Weaknesses** | More files than service classes, slight overhead for simple operations, unfamiliar to some developers |
| **Opportunities** | Custom logging/monitoring, per-tenant behavior, A/B testing implementations, easy mocking in tests |
| **Threats** | Over-engineering for tiny projects, inconsistent usage if team doesn't follow pattern |

## What is an Action?

An Action is a class with a single public method (`execute`) that performs one specific task. Instead of grouping related methods in a service class, each operation gets its own dedicated class.

```php
// Action pattern
class SendInvoiceAction
{
    public function execute(array $data): InvoiceResult
    {
        // Send the invoice
    }
}

// vs. traditional service pattern
class InvoiceService
{
    public function send(array $data): InvoiceResult { }
    public function getStatus(string $id): InvoiceStatus { }
    public function cancel(string $id): bool { }
}
```

## Why Use Actions?

### 1. Single Responsibility

Each action does exactly one thing. This makes the code easier to understand, test, and maintain. When something breaks, you know exactly where to look.

### 2. Swappable Implementations

Actions can be swapped via configuration without changing application code:

```php
// config/peppol-gateway.php
'actions' => [
    'send_invoice' => \App\Actions\CustomSendInvoiceAction::class,
],
```

This is useful for:
- Adding custom logging or monitoring
- Integrating with other systems
- Mocking in tests
- Different behavior per environment

### 3. Explicit Dependencies

Each action declares only the dependencies it needs. No bloated service classes with dozens of injected dependencies where most methods only use a few.

### 4. Easy to Test

Testing a single action is straightforward:

```php
it('sends invoice successfully', function () {
    Http::fake([...]);

    $action = new SendInvoiceAction();
    $result = $action->execute(['type' => 'invoice', ...]);

    expect($result->status)->toBe('queued');
});
```

### 5. Discoverable

Looking at the `Actions` folder immediately tells you what operations the package supports. No need to dig through large service classes.

```
src/Actions/
├── GetInvoiceStatusAction.php
├── HealthCheckAction.php
├── LookupParticipantAction.php
├── SendCreditNoteAction.php
└── SendInvoiceAction.php
```

## How to Use

### Basic Usage

```php
use Xve\LaravelPeppol\Actions\SendInvoiceAction;
use Xve\LaravelPeppol\Support\Config;

$action = Config::getAction('send_invoice', SendInvoiceAction::class);
$result = $action->execute($invoiceData);
```

### Why Config::getAction()?

The `Config::getAction()` method provides:

1. **Fallback** - Returns the default action if none is configured
2. **Validation** - Ensures configured class extends the base action
3. **Type hints** - IDE autocompletion works correctly

### Extending an Action

To customize behavior, extend the base action:

```php
namespace App\Actions;

use Xve\LaravelPeppol\Actions\SendInvoiceAction;
use Xve\LaravelPeppol\Support\InvoiceResult;

class CustomSendInvoiceAction extends SendInvoiceAction
{
    public function execute(array $data): InvoiceResult
    {
        // Add custom logic before
        Log::info('Sending invoice', ['id' => $data['id']]);

        $result = parent::execute($data);

        // Add custom logic after
        Notification::send($admin, new InvoiceSentNotification($result));

        return $result;
    }
}
```

Then register it in your config:

```php
// config/peppol-gateway.php
'actions' => [
    'send_invoice' => \App\Actions\CustomSendInvoiceAction::class,
],
```

## Events

Each action dispatches an event after successful execution. This provides another extension point without modifying actions:

```php
// Listen to events instead of extending actions
Event::listen(InvoiceSent::class, function (InvoiceSent $event) {
    Log::info('Invoice sent', [
        'uuid' => $event->result->uuid,
        'data' => $event->data,
    ]);
});
```

Use events when you want to react to an action without changing its behavior. Extend the action when you need to modify its behavior.

## Further Reading

- [Actions Pattern by Spatie](https://freek.dev/1371-refactoring-to-actions)
- [Laravel Beyond CRUD - Actions](https://laravel-beyond-crud.com/1-domain-oriented-laravel#actions)
