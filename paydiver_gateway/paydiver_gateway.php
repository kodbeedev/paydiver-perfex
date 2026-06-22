<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Paydiver Gateway
Description: Accept bKash, Nagad, Rocket and Upay payments in Perfex CRM through the Paydiver payment gateway by Kodbee.
Version: 1.0.0
Requires at least: 2.3.*
Author: Kodbee
Author URI: https://kodbee.com
*/

define('PAYDIVER_GATEWAY_MODULE', 'paydiver_gateway');

/**
 * Register the payment gateway once the app boots.
 */
hooks()->add_action('app_init', 'paydiver_gateway_init');

function paydiver_gateway_init()
{
    $ci = &get_instance();
    // Loading the library registers it as a payment gateway (App_gateway).
    $ci->load->library('modules/paydiver_gateway/Paydiver_gateway');
}

register_activation_hook(PAYDIVER_GATEWAY_MODULE, 'paydiver_gateway_activation_hook');

function paydiver_gateway_activation_hook()
{
    // Nothing to migrate — settings are stored as gateway options.
}
