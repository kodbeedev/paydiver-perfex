# Paydiver for Perfex CRM

Perfex CRM payment gateway module for the [Paydiver](https://kodbee.com) payment
API by **Kodbee**. Accept bKash, Nagad, Rocket and Upay; invoices are marked
paid automatically through a signature-verified webhook.

## Install

1. Copy the `paydiver_gateway` folder into `modules/` of your Perfex install.
2. **Setup → Modules** → activate **Paydiver Gateway**.
3. **Setup → Settings → Payment Gateways → Paydiver** → set Base URL, API Key,
   Secret Key and (optionally) Webhook Secret.

## How it works

1. On payment the module calls `POST /api/v1/payment/create` and redirects the
   client to the hosted Paydiver payment page.
2. The Paydiver → Perfex invoice mapping is stored as an option
   (`paydiver_map_{invoice}`).
3. Paydiver posts a webhook to `<site>/paydiver_gateway/paydiver_gateway/webhook`;
   the controller verifies the `X-Paydiver-Signature` over the raw body and
   records the payment for the mapped invoice.

## License

MIT © [Kodbee](https://kodbee.com)
