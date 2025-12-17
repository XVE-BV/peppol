# Laravel Peppol Gateway - Implementation Plan

Client package for the Peppol Gateway API (Flowin integration).

## Features

### Core API Methods
- [ ] Health/ping check (`GET /api/system/health`)
- [ ] Customer/participant lookup (`POST /api/peppol/lookup`)
- [ ] Send invoice JSON (`POST /api/invoices/json`)
- [ ] Send credit note JSON (`POST /api/credit-notes/json`)
- [ ] Check invoice/credit status (`GET /api/invoices/{id}`)

### Package Structure
- [ ] Config file (`config/peppol-gateway.php`)
  - API base URL
  - Client ID
  - Client Secret
  - Timeout settings
- [ ] HTTP Client service (`PeppolGatewayClient`)
- [ ] Facade (`PeppolGateway`)
- [ ] DTOs for requests/responses
  - [ ] `InvoiceData`
  - [ ] `CreditNoteData`
  - [ ] `LookupResult`
  - [ ] `InvoiceStatus`
  - [ ] `HealthStatus`
- [ ] Exception handling
  - [ ] `PeppolGatewayException`
  - [ ] `AuthenticationException`
  - [ ] `ValidationException`

### Authentication
- [ ] Bearer token auth (`Authorization: Bearer {secret}`)
- [ ] Client ID header (`X-Api-Client-Id: {uuid}`)

### Out of Scope
- Logic of saving when last was fetched
- Logic when to refetch per customer/client
- Mapping BTW fields (done in PGA)
- Saving history
- Logic to fetch status from PGA
