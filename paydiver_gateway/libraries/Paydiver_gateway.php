<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Paydiver payment gateway for Perfex CRM — by Kodbee (https://kodbee.com).
 */
class Paydiver_gateway extends App_gateway
{
    public function __construct()
    {
        $this->setId('paydiver_gateway');
        $this->setName('Paydiver');

        parent::__construct();

        $this->setSettings([
            [
                'name' => 'base_url',
                'label' => 'Base URL',
                'default_value' => 'https://pay.kodbee.com',
            ],
            [
                'name' => 'api_key',
                'label' => 'API Key',
            ],
            [
                'name' => 'secret_key',
                'label' => 'Secret Key',
                'type' => 'password',
            ],
            [
                'name' => 'webhook_secret',
                'label' => 'Webhook Secret (optional)',
                'type' => 'password',
            ],
        ]);
    }

    /**
     * Build the Paydiver invoice and redirect the customer to the hosted page.
     *
     * @param array $data Perfex payment data (invoiceid, amount, ...).
     */
    public function process_payment($data)
    {
        $ci = &get_instance();
        $ci->load->model('invoices_model');

        $invoice = $ci->invoices_model->get($data['invoiceid']);
        $base = rtrim($this->getSetting('base_url'), '/');

        $payload = [
            'amount' => (float) $data['amount'],
            'product_name' => 'Invoice #' . $data['invoiceid'],
            'customer_name' => $invoice ? trim(($invoice->client->company ?? '')) : '',
            'customer_email' => $invoice->client->email ?? null,
            'redirect_url' => site_url('invoice/' . $data['invoiceid']),
            'callback_url' => site_url('paydiver_gateway/paydiver_gateway/webhook'),
        ];

        $response = $this->http($base . '/api/v1/payment/create', $payload);

        if (! $response || empty($response['data']['payment_url']) || empty($response['data']['invoice_id'])) {
            set_alert('warning', 'Paydiver payment could not be started.');
            redirect(site_url('invoice/' . $data['invoiceid']));

            return;
        }

        // Map Paydiver invoice -> Perfex invoice for the webhook.
        update_option('paydiver_map_' . $response['data']['invoice_id'], (string) $data['invoiceid']);

        redirect($response['data']['payment_url']);
    }

    /**
     * @param array $payload
     * @return array|null
     */
    private function http($url, array $payload)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-API-Key: ' . $this->getSetting('api_key'),
                'X-Secret-Key: ' . $this->getSetting('secret_key'),
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $raw = curl_exec($ch);
        curl_close($ch);

        if ($raw === false) {
            return null;
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
