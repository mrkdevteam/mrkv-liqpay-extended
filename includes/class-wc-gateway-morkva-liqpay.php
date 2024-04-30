<?php
/**
 * Class WC_Gateway_Morkva_Liqpay file.
 *
 * @package WooCommerce\Gateways
 * 
 */

# This prevents a public user from directly accessing your .php files
if (!defined('ABSPATH')) 
{
    # Exit if accessed directly
    exit; 
}

use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * WC_Gateway_Morkva_Liqpay Class
 * 
 */
class WC_Gateway_Morkva_Liqpay extends WC_Payment_Gateway
{
    /**
     * Constructor for the gateway
     */
    public function __construct()
    {
        # Setup general properties
        $this->setup_properties();

        # Load the settings
        $this->init_form_fields();
        $this->init_settings();

        # Get settings
        # Save Gateway title
        $this->title = $this->get_option('title');

        # Save Gateway description
        $this->description = $this->get_option('description');

        # Save Gateway instruction
        $this->instructions = $this->get_option('instructions');

        # Save Gateway default language
        $this->lang = $this->get_option('lang', 'uk');

        # Save Gateway enabled method switcher
        $this->enable_for_methods = $this->get_option('enable_for_methods', array());

        # Save Gateway enabled virtual
        $this->enable_for_virtual = $this->get_option('enable_for_virtual', 'yes') === 'yes';

        # Save type taxonomies support
        $this->supports = array(
                'products',
                'refunds',
        );

        # Check woo plugin version
        if (version_compare(WOOCOMMERCE_VERSION, '2.0.0', '>=')) 
        {
            # Version Woocommerce 2.0.0 
            add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'check_response'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        } 
        else 
        {
            # Version Woocommerce 1.6.6
            add_action('init', array(&$this, 'check_response'));
            add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
        }    
    }

    /**
     * Setup general properties for the gateway
     * 
     */
    protected function setup_properties()
    {
        # Save slug of Morkva Liqpay
        $this->id = 'morkva-liqpay';
        $this->icon = apply_filters('woocommerce_cod_icon', '');
        $this->method_title = 'Morkva LiqPay ';
        $this->method_description = __('Платіжний сервіс, який дає змогу здійснювати миттєві платежі в інтернеті та платіжних карт Visa, MasterCard у всьому світі.', 'mrkv-liqpay-extended');
        $this->has_fields = false;
    }

    /**
     * Get gateway icon
     *
     * @return string
     * 
     */
    public function get_icon()
    {
        # Icon html Gateway Morkva liqpay
        $icon_html = '';

        # Return html 
        return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    /**
     * Initialise Gateway Settings Form Fields
     * 
     */
    public function init_form_fields()
    {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Увімкнути/вимкнути', 'mrkv-liqpay-extended'),
                'label' => __('Увімкнути', 'mrkv-liqpay-extended'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Заголовок', 'mrkv-liqpay-extended'),
                'type' => 'text',
                'description' => __('Maorkva LiqPay - Моментальні платежі по всьому світу', 'mrkv-liqpay-extended'),
                'default' => '',
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Опис', 'mrkv-liqpay-extended'),
                'type' => 'textarea',
                'description' => __('Платіжний сервіс, який дає змогу здійснювати миттєві платежі в інтернеті та платіжних карт Visa, MasterCard у всьому світі.', 'mrkv-liqpay-extended'),
                'default' => '',
                'desc_tip' => true,
            ),
            'instructions' => array(
                'title' => __('Інструкція', 'mrkv-liqpay-extended'),
                'type' => 'textarea',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
            ),
            'public_key' => array(
                'title' => __('API public_key', 'mrkv-liqpay-extended'),
                'type' => 'text',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),
            'private_key' => array(
                'title' => __('API private_key', 'mrkv-liqpay-extended'),
                'type' => 'text',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),
            'test_enabled' => array(
                'title' => __('Тестовий режим', 'mrkv-liqpay-extended'),
                'label' => __('Увімкнути', 'mrkv-liqpay-extended'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'test_enabled_admin' => array(
                'title' => __('Тестовий режим для адміністратора', 'mrkv-liqpay-extended'),
                'label' => __('Увімкнути', 'mrkv-liqpay-extended'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'test_public_key' => array(
                'title' => __('Тестовий API public_key', 'mrkv-liqpay-extended'),
                'type' => 'text',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),
            'test_private_key' => array(
                'title' => __('Тестовий API private_key', 'mrkv-liqpay-extended'),
                'type' => 'text',
                'description' => '',
                'default' => '',
                'desc_tip' => true,
                'placeholder' => '',
            ),
        );
    }

    /**
     * Return description of order
     * 
     * @param integer $order_id Order id in Woo
     * @return string
     */
    private function getDescription($order_id)
    {
        # Create description
        $description = __('Оплата замовлення № ', 'mrkv-liqpay-extended') . $order_id;

        # Return description
        return $description;
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id Order ID
     * @return array
     */
    public function process_payment($order_id)
    {
        # Get order data by id
        $order = wc_get_order($order_id);

        # Check order total 
        if ($order->get_total() > 0) 
        {
            # Send email notification
            $this->pending_new_order_notification($order->get_id());
        } 

        # Remove cart data
        WC()->cart->empty_cart();

        # Include Api Morkva liqpay
        require_once(__DIR__ . '/classes/MorkvaLiqPay.php');

        # Check test mode
        if($this->get_option( 'test_enabled_admin' ) == 'yes' && ( current_user_can('editor') || current_user_can('administrator') ))
        {
            # Use test keys
            $morkva_liqPay = new MorkvaLiqPay($this->get_option('test_public_key'), $this->get_option('test_private_key'));
        }
        elseif($this->get_option( 'test_enabled' ) == 'yes'  && $this->get_option( 'test_enabled_admin' ) != 'yes')
        {
            # Use test keys
            $morkva_liqPay = new MorkvaLiqPay($this->get_option('test_public_key'), $this->get_option('test_private_key'));
        }
        else
        {   
            # Use main keys
            $morkva_liqPay = new MorkvaLiqPay($this->get_option('public_key'), $this->get_option('private_key'));
        }
        
        # Create argument of query
        $arrayData = array(
            'version' => '3',
            'action' => 'pay',
            'amount' => $order->get_total(),
            'currency' => $order->get_currency(),
            'description' => $this->getDescription($order->get_id()),
            'order_id' => $order->get_id(),
            'result_url' => $this->get_return_url($order),
            'language' => 'uk',
            'server_url' => WC()->api_request_url( 'WC_Gateway_Morkva_Liqpay' )
        );

        # Create result link
        $url = $morkva_liqPay->cnb_link($arrayData);

        # Return result 
        return array( 
            'result' => 'success',
            'redirect' => $url,
        );
    }

    /**
     * Output for the order received page
     */
    public function thankyou_page()
    {
        # Stop job to five seconds
        sleep(5);

        # Get order data 
        $order = wc_get_order($order_id);

        # Check order status
        if (!$order->has_status($this->status) && $this->cancel_pay) 
        {
            # Show info by payment
            echo wp_kses_post(wpautop(wptexturize($this->cancel_pay)));
        }

        # Show Instruction for user 
        if ($this->instructions) 
        {
            # Show info by payment
            echo wp_kses_post(wpautop(wptexturize($this->instructions)));
        }
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order Order object.
     * @param bool $sent_to_admin Sent to admin.
     * @param bool $plain_text Email format: plain text or HTML.
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false)
    {
        # Check email instruction
        if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method()) 
        {
            # Show info
            echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
        }
    }

    /**
     * New order notification function
     * 
     * @param $order_id Order id
     */
    private function pending_new_order_notification($order_id)
    {
        # Get order data 
        $order = wc_get_order($order_id);

        # Only for "pending" order status
        if (!$order->has_status('pending')) return;

        # Get an instance of the WC_Email_New_Order object
        $wc_email = WC()->mailer()->get_emails()['WC_Email_New_Order'];

        # Create email data
        $wc_email->settings['subject'] = '{site_title} - ' . __('Нове замовлення', 'mrkv-liqpay-extended') . ' ({order_number}) - {order_date}';
        $wc_email->settings['heading'] = __('Нове замовлення', 'mrkv-liqpay-extended');

        # Send email
        $wc_email->trigger($order_id);
    }

    /**
     * Check response from LiqPay
     * 
     * @param $inputData All data
     * @return mixed|string|void
     */
    public function check_response($inputData)
    {
        # Get Woo global data
        global $woocommerce;

        # Check data response 
        $success = isset($_POST['data']) && isset($_POST['signature']);

        # If payment success
        if ($success) 
        {
            $data = '';
            $received_signature = '';

            # Get response data
            if(isset($_POST['data'])){
                $data = sanitize_text_field($_POST['data']);
            }
            # Get response signature
            if(isset($_POST['signature'])){
                $received_signature = sanitize_text_field($_POST['signature']); 
            }

            # Parse JSON data
            $parsed_data = json_decode(base64_decode($data)); 
            
            # Save main data response
            $received_public_key = $parsed_data->public_key;
            $order_id = $parsed_data->order_id; 
            $status = $parsed_data->status; 
            $sender_phone = $parsed_data->sender_phone;
            $amount = $parsed_data->amount;
            $currency = $parsed_data->currency;
            $transaction_id = $parsed_data->transaction_id;

            file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . ' Status: ' .  print_r($status, 1), FILE_APPEND); 

            # Get order data
            $order = new WC_Order($order_id);

            file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . print_r($status, 1), FILE_APPEND); 
            file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . ' order_id: ' .  print_r($order_id, 1), FILE_APPEND); 
            file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . ' order_id: ' .  print_r($parsed_data, 1), FILE_APPEND); 



            # Check status response 
            if ($status == 'success' || ($status == 'sandbox')) 
            {
                if(!$order->has_status('processing'))
                {
                    $message = '';

                    if(isset($parsed_data) && isset($parsed_data->sender_card_mask2) && !$order->get_meta('_mrkv_liqpay_sender_card_mask2'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_sender_card_mask2', $parsed_data->sender_card_mask2 );
                        update_post_meta( $order_id, '_mrkv_liqpay_sender_card_mask2', $parsed_data->sender_card_mask2 );


                        $message .= ' ' . __('sender_card_mask2:  ', 'mrkv-liqpay-extended') . $parsed_data->sender_card_mask2 . '<br>';
                    }

                    if(isset($parsed_data) && isset($parsed_data->sender_card_type) && !$order->get_meta('_mrkv_liqpay_sender_card_type'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_sender_card_type', $parsed_data->sender_card_type );
                        update_post_meta( $order_id, '_mrkv_liqpay_sender_card_type', $parsed_data->sender_card_type );

                        $message .= ' ' . __('sender_card_type:  ', 'mrkv-liqpay-extended') . $parsed_data->sender_card_type . '<br>';
                    }

                    if(isset($parsed_data) && isset($parsed_data->acq_id)  && !$order->get_meta('_mrkv_liqpay_acq_id'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_acq_id', $parsed_data->acq_id );
                        update_post_meta( $order_id, '_mrkv_liqpay_acq_id', $parsed_data->acq_id );

                        $message .= ' ' . __('acq_id:  ', 'mrkv-liqpay-extended') . $parsed_data->acq_id . '<br>';
                    }

                    if(isset($parsed_data) && isset($parsed_data->agent_commission) && !$order->get_meta('_mrkv_liqpay_agent_commission'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_agent_commission', $parsed_data->agent_commission );
                        update_post_meta( $order_id, '_mrkv_liqpay_agent_commission', $parsed_data->agent_commission );

                        $message .= ' ' . __('agent_commission:  ', 'mrkv-liqpay-extended') . $parsed_data->agent_commission . '<br>';
                    }

                    if(isset($parsed_data) && isset($parsed_data->liqpay_order_id) && !$order->get_meta('_mrkv_liqpay_liqpay_order_id'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_liqpay_order_id', $parsed_data->liqpay_order_id );
                        update_post_meta( $order_id, '_mrkv_liqpay_liqpay_order_id', $parsed_data->liqpay_order_id );

                        $message .= ' ' . __('liqpay_order_id:  ', 'mrkv-liqpay-extended') . $parsed_data->liqpay_order_id . '<br>';
                    }

                    if(isset($parsed_data) && isset($parsed_data->receiver_commission) && !$order->get_meta('_mrkv_liqpay_receiver_commission'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_receiver_commission', $parsed_data->receiver_commission );
                        update_post_meta( $order_id, '_mrkv_liqpay_receiver_commission', $parsed_data->receiver_commission );

                        $message .= ' ' . __('receiver_commission:  ', 'mrkv-liqpay-extended') . $parsed_data->receiver_commission . '<br>';
                    }
                    
                    if(isset($parsed_data) && isset($parsed_data->commission_credit) && !$order->get_meta('_mrkv_liqpay_commission_credit'))
                    {
                        $order->update_meta_data( '_mrkv_liqpay_commission_credit', $parsed_data->commission_credit );
                        update_post_meta( $order_id, '_mrkv_liqpay_commission_credit', $parsed_data->commission_credit );

                        $message .= ' ' . __('commission_credit:  ', 'mrkv-liqpay-extended') . $parsed_data->commission_credit . '<br>';
                    }

                    // Save the order.
                    $order->save();

                    # Update order status
                    $order->update_status('processing');

                    # Switch payment to complete
                    $order->payment_complete();

                    $order->save();

                    # Add to order note payment status
                    $order->add_order_note(__('Платіж LiqPay виконано успішно.<br/>Ідентифікатор платежу LiqPay:  ', 'mrkv-liqpay-extended') . $parsed_data->liqpay_order_id ); 

                    if(isset($parsed_data) && isset($parsed_data->amount_debit) && isset($order_id)){
                        file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . 'testestestetse', FILE_APPEND);      
                        file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . print_r($parsed_data->amount_debit , 1), FILE_APPEND);      
                        file_put_contents(__DIR__.'/log/debug.log', date('d-m-Y H:i:s') . PHP_EOL . 'testestestetse', FILE_APPEND);                 

                        $order->update_meta_data( '_mrkv_liqpay_total_amount', $parsed_data->amount_debit );
                        // Save amount uah
                        update_post_meta( $order_id, '_mrkv_liqpay_total_amount', $parsed_data->amount_debit );

                        // Save the order.
                        $order->save();
                    }
                }
            } 
            else 
            {
                # Update status to failed
                $order->update_status('failed', __('Помилка під час оплати', 'mrkv-liqpay-extended'));

                # Stop server work
                exit;
            }
        } 
        else 
        {
            # Stop Wordpress job
            wp_die('IPN Request Failure');
        }
    }   
}
