# Laravel Peppol Gateway - Implementation Plan

Client package for the Peppol Gateway API (Flowin integration).

## Package Structure

```
src/
├── Actions/                    # Core API operations
├── Exceptions/                 # Custom exceptions
├── Support/                    # Helper utilities
├── Models/
│   └── Concerns/               # Traits and interfaces
└── LaravelPeppolServiceProvider.php
```

## Implementation Checklist

### Config (`config/peppol-gateway.php`)
- [x] API connection settings (base_url, timeout)
- [x] Authentication credentials (client_id, client_secret)
- [x] Swappable action classes

### Support Classes (`src/Support/`)
- [x] `Config` helper class
  - [x] `getActionClass()` - resolve configurable actions
  - [x] Validation of configuration values
  - [x] Fail-fast on invalid config
  - [x] `httpClient()` - configured HTTP client

### Actions (`src/Actions/`)
- [x] `HealthCheckAction` - `GET /api/system/health`
- [x] `LookupParticipantAction` - `POST /api/peppol/lookup`
- [x] `SendInvoiceAction` - `POST /api/invoices/json`
- [x] `SendCreditNoteAction` - `POST /api/credit-notes/json`
- [x] `GetInvoiceStatusAction` - `GET /api/invoices/{id}`

Each action:
- [x] Single `execute()` method
- [x] Type-hinted parameters and return types
- [x] Uses Config helper for HTTP client
- [x] Returns DTO or throws exception

### Exceptions (`src/Exceptions/`)
- [x] `PeppolGatewayException` (base)
- [x] `AuthenticationException`
  - [x] `::invalidCredentials()`
  - [x] `::missingCredentials()`
- [x] `ConnectionException`
  - [x] `::timeout()`
  - [x] `::unreachable()`
  - [x] `::missingBaseUrl()`
- [x] `ValidationException`
  - [x] `::fromResponse(array $errors)`
- [x] `InvoiceException`
  - [x] `::notFound(string $id)`
  - [x] `::sendFailed(string $reason)`

### DTOs (`src/Support/`)
- [x] `HealthStatus` - health check response
- [x] `Participant` - lookup response
- [x] `InvoiceResult` - send invoice response
- [x] `InvoiceStatus` - status check response

### Traits & Interfaces (`src/Models/Concerns/`)
- [x] `HasPeppolId` interface
  - [x] `getPeppolParticipantId(): ?string`
  - [x] `setPeppolParticipantId(string $id): void`
- [x] `InteractsWithPeppol` trait
  - [x] Default implementation of interface

### Service Provider
- [x] Register config file
- [x] Bind actions to container

### Authentication
- [x] `X-Api-Client-Id` header (UUID)
- [x] `Authorization: Bearer {secret}` header

### Testing
- [x] `TestCase` with proper setup
- [x] Test action container resolution
- [x] Architecture tests (no dd/dump/ray)

## Out of Scope
- Logic of saving when last was fetched
- Logic when to refetch per customer/client
- Mapping BTW fields (done in PGA)
- Saving history
- Logic to fetch status from PGA

## API Endpoints Reference

| Method | Endpoint | Action |
|--------|----------|--------|
| GET | `/api/system/health` | HealthCheckAction |
| POST | `/api/peppol/lookup` | LookupParticipantAction |
| POST | `/api/invoices/json` | SendInvoiceAction |
| POST | `/api/credit-notes/json` | SendCreditNoteAction |
| GET | `/api/invoices/{id}` | GetInvoiceStatusAction |
