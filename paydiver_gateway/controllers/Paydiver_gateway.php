<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Public webhook endpoint for the Paydiver Perfex gateway — by Kodbee.
 *
 * URL: <site>/paydiver_gateway/paydiver_gateway/webhook
 */
class Paydiver_gateway extends App_Controller
{
    public function webhook()
    {
        $raw = file_get_contents('php://input') ?: '';
        $signature = $_SERVER['HTTP_X_PAYDIVER_SIGNATURE'] ?? '';

        $secret = get_option('paydiver_gateway_webhook_secret');
        if (empty($secret)) {
            $secret = get_option('paydiver_gateway_secret_key');
        }

        if (empty($secret) || ! hash_equals(hash_hmac('sha256', $raw, $secret), (string) $signature)) {
            show_404();

            return;
        }

        $event = json_decode($raw, true);
        if (! is_array($event) || ($event['event'] ?? '') !== 'payment.verified') {
            echo 'ignored';

            return;
        }

        $paydiverInvoice = (string) ($event['invoice_id'] ?? '');
        $perfexInvoiceId = get_option('paydiver_map_' . $paydiverInvoice);

        if (empty($perfexInvoiceId)) {
            show_404();

            return;
        }

        $this->load->model('payments_model');
        $this->load->model('invoices_model');

        $invoice = $this->invoices_model->get((int) $perfexInvoiceId);
        if (! $invoice) {
            show_404();

            return;
        }

        $this->payments_model->add([
            'invoiceid' => (int) $perfexInvoiceId,
            'amount' => (float) ($event['amount'] ?? 0),
            'paymentmode' => 'paydiver_gateway',
            'transactionid' => (string) ($event['trx_id'] ?? ''),
            'note' => 'Paydiver (' . ($event['gateway'] ?? '-') . ')',
        ]);

        echo 'ok';
    }
}
