# Release Notes - Payment Plugin für PlentyMarkets 7 und dem heidelpay merchant gateway (MGW)
Sämtliche relevanten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format basiert auf [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) und dieses Projekt hält sich an [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [1.0.3][1.0.3]
### Fix
- Die Sprache des Kunden wird nun im Redirect in den Shop berücksichtigt.

## [1.0.2][1.0.2]
### Fix
- Der Link zum Handbuch wurde korrigiert 

## [1.0.1][1.0.1]
### Change
- BasePaymentMethod erbt von PaymentMethodBaseService
- Payment Methods werden über die Migrations angelegt
- Deutsche Übersetzung für die Ereignisse hinzugefügt

### Fix
- Es wurde entfernt, dass der Typ eines Events geändert wurde, wenn die Zahlart nicht zu heidelpay gehört

## [1.0.0][1.0.0]
### Add
*   Erstveröffentlichung.

[1.0.0]: https://github.com/heidelpay/plentymarkets-merchant-gateway/tree/1.0.0
[1.0.1]: https://github.com/heidelpay/plentymarkets-merchant-gateway/compare/1.0.0..1.0.1
[1.0.2]: https://github.com/heidelpay/plentymarkets-merchant-gateway/compare/1.0.1..1.0.2