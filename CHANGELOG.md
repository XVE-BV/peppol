# Changelog

All notable changes to `laravel-peppol-gateway` will be documented in this file.

## v1.3.0

### Added

- `ValidateVatCombinationAction` and the `VatValidationResult` value object, reachable as `PeppolGatewayService::validateVatCombination()`. Asks the gateway which of a set of VAT combination strings are still unmapped for the calling client, so a caller can block a send before the gateway rejects it. Additive.

## v1.2.0 - 2026-06-24

### Added

- Transmission-detail fields on the `InvoiceStatus` DTO, mapped from the gateway's snake_case status payload: `invoiceNumber`, `buyerPeppolId`, `buyerName`, `transmissionId`, `format`, `submittedAt`, `sentAt`, `acknowledgedAt`, `deliveredAt`, and `errors`. Additive — existing fields (including `technicalStatus`) are unchanged.

## v1.0.0 - Unreleased

### Added

- Health check action to verify API connectivity
- Participant lookup action to check Peppol network registration
- Send invoice action (JSON format)
- Send credit note action (JSON format)
- Get invoice status action
- Events for all actions (HealthChecked, ParticipantLookedUp, InvoiceSent, CreditNoteSent, InvoiceStatusRetrieved)
- DTOs for API responses (HealthStatus, Participant, InvoiceResult, InvoiceStatus)
- Custom exceptions with factory methods (AuthenticationException, ConnectionException, ValidationException, InvoiceException)
- Config helper class with HTTP client factory
- HasPeppolId trait for models
- InteractsWithPeppol interface
- Support for Laravel 10, 11, and 12
