<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Jomabee Gateway
Description: Accept bKash, Nagad, Rocket and Upay payments in Perfex CRM through the Jomabee payment gateway by Kodbee.
Version: 1.0.0
Requires at least: 2.3.*
Author: Kodbee
Author URI: https://kodbee.com
*/

define('JOMABEE_GATEWAY_MODULE', 'jomabee_gateway');

/**
 * Register the payment gateway once the app boots.
 */
hooks()->add_action('app_init', 'jomabee_gateway_init');

function jomabee_gateway_init()
{
    $ci = &get_instance();
    // Loading the library registers it as a payment gateway (App_gateway).
    $ci->load->library('modules/jomabee_gateway/Jomabee_gateway');
}

register_activation_hook(JOMABEE_GATEWAY_MODULE, 'jomabee_gateway_activation_hook');

function jomabee_gateway_activation_hook()
{
    // Nothing to migrate — settings are stored as gateway options.
}
