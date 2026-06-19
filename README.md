# Jomabee for Perfex CRM

Perfex CRM payment gateway module for the [Jomabee](https://kodbee.com) payment
API by **Kodbee**. Accept bKash, Nagad, Rocket and Upay; invoices are marked
paid automatically through a signature-verified webhook.

## Install

1. Copy the `jomabee_gateway` folder into `modules/` of your Perfex install.
2. **Setup → Modules** → activate **Jomabee Gateway**.
3. **Setup → Settings → Payment Gateways → Jomabee** → set Base URL, API Key,
   Secret Key and (optionally) Webhook Secret.

## How it works

1. On payment the module calls `POST /api/v1/payment/create` and redirects the
   client to the hosted Jomabee payment page.
2. The Jomabee → Perfex invoice mapping is stored as an option
   (`jomabee_map_{invoice}`).
3. Jomabee posts a webhook to `<site>/jomabee_gateway/jomabee_gateway/webhook`;
   the controller verifies the `X-Jomabee-Signature` over the raw body and
   records the payment for the mapped invoice.

## License

MIT © [Kodbee](https://kodbee.com)
