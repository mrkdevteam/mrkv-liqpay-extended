<?php
/*
 * Plugin Name: Morkva Liqpay Extended
 * Description: LiqPay Payment Gateway with callback by Morkva
 * Version: 0.5.2
 * Tested up to: 6.5
 * Requires at least: 5.2
 * Requires PHP: 7.1
 * Author: MORKVA
 * Author URI: https://morkva.co.ua
 * Text Domain: mrkv-liqpay-extended
 * WC requires at least: 5.4.0
 * WC tested up to: 8.8.0
 */

# This prevents a public user from directly accessing your .php files
if (! defined('ABSPATH')) 
{
    # Exit if accessed directly
    exit;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

# Include liqpay to menu Wordpress
require_once plugin_dir_path(__FILE__) . 'includes/class-morkva-liqpay-menu.php';

# Create page and show in menu
new MorkvaLiqpayMenu();

# Add Morkva liqpay Gateway to Woocommerce
function add_morkva_liqpay_gateway_class($methods)
{
    # Iclude Morkva Liqpay Gateway
    require_once(__DIR__ . '/includes/class-wc-gateway-morkva-liqpay.php');

    # Include Liqpay
    $methods[] = 'WC_Gateway_Morkva_Liqpay';

    # Return all methods
    return $methods;
}

# Add filter to Payment Gateway
add_filter('woocommerce_payment_gateways', 'add_morkva_liqpay_gateway_class');
