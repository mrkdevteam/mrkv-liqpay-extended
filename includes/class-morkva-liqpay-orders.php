<?php
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

# Check if class exist
if (!class_exists('MRKV_LIQPAY_ORDERS'))
{
	/**
	 * Class for setup woo settings
	 */
	class MRKV_LIQPAY_ORDERS
	{
		/**
		 * Constructor for woo settings
		 * */
		function __construct()
		{
			# Add metabox to order edit
			add_action('add_meta_boxes', array( $this, 'mrkv_liqpay_add_meta_boxes' ));

			add_action( 'wp_ajax_mrkv_liqpay_cancel_payment_hold', array( $this, 'mrkv_liqpay_cancel_payment_hold_func' ) );
			add_action( 'wp_ajax_nopriv_mrkv_liqpay_cancel_payment_hold', array( $this, 'mrkv_liqpay_cancel_payment_hold_func' ) );

			add_action( 'wp_ajax_mrkv_liqpay_final_payment_hold', array( $this, 'mrkv_liqpay_final_payment_hold_func' ) );
			add_action( 'wp_ajax_nopriv_mrkv_liqpay_final_payment_hold', array( $this, 'mrkv_liqpay_final_payment_hold_func' ) );

			add_action('mrkv_liqpay_settings_sidebar', [$this, 'mrkv_liqpay_settings_sidebar_func']);
		}

		public function mrkv_liqpay_settings_sidebar_func()
		{
			?>
				<div class="morkva-settings-sidebar" style="flex: 1; min-width: 250px;">
					<div class="morkva-settings-sidebar_inner" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;margin-bottom:15px;">
						<h3 style="margin-top: 0;"><?php echo __( 'Like this plugin?', 'mrkv-liqpay-extended' ); ?></h3>
						<p>
							<?php echo __( 'Support our efforts with a', 'mrkv-liqpay-extended' ) . ' '; ?>
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<?php echo __( 'review at', 'mrkv-liqpay-extended' ) . ' <a href="https://wordpress.org/plugins/mrkv-liqpay-extended/" target="blanc">WordPress.org</a>'; ?>
						</p>
						<a class="button button-primary" href="https://wordpress.org/plugins/mrkv-liqpay-extended/" target="blanc">
							<?php echo __( 'Leave', 'mrkv-liqpay-extended' ) . ' '; ?>
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">
						</a>
						<p>
							<?php echo __( 'Isn’t good enough for a 5', 'mrkv-liqpay-extended' ) . ' '; ?>
							<img src="<?php echo plugins_url( '../img/star.svg', __FILE__ ); ?>" alt="Star" alt="Star">? 
							<?php echo __( 'Contact us via the widget on our website, or check out', 'mrkv-liqpay-extended' ) . ' <a href="https://docs.morkva.co.ua/uk?utm_source=plugin&utm_medium=sidebar&utm_campaign=liqpay_free" target="blanc">' . __( 'documantation', 'mrkv-liqpay-extended' ) . '</a>'; ?>
						</p>
						<div class="mrkv-btns-line-sidebar" style="display: flex;gap: 4px;">
							<a class="button button-primary" href="https://morkva.co.ua/?utm_source=plugin&utm_medium=sidebar&utm_campaign=liqpay_free" target="blanc">
								<?php echo __( 'Go to the website', 'mrkv-liqpay-extended' ); ?>
							</a>
							<a class="button" href="https://docs.morkva.co.ua/uk?utm_source=plugin&utm_medium=sidebar&utm_campaign=liqpay_free" target="blanc">
								<?php echo __( 'Documantation', 'mrkv-liqpay-extended' ); ?>
							</a>
						</div>
					</div>
					<div class="morkva-settings-sidebar_inner" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;margin-bottom:15px;">
						<h3 style="margin-top: 0;"><?php echo __( 'Check out pro-version', 'mrkv-liqpay-extended' ); ?></h3>
						<ul>
							<li>
								<img src="<?php echo plugins_url( '../img/check.svg', __FILE__ ); ?>" alt="Check" alt="Check">
								<?php echo __( 'Pay by Parts', 'mrkv-liqpay-extended' ); ?>
							</li>
							<li>
								<img src="<?php echo plugins_url( '../img/check.svg', __FILE__ ); ?>" alt="Check" alt="Check">
								<?php echo __( 'Payment status validation', 'mrkv-liqpay-extended' ); ?>
							</li>
							<li>
								<img src="<?php echo plugins_url( '../img/check.svg', __FILE__ ); ?>" alt="Check" alt="Check">
								<?php echo __( 'Prepay', 'mrkv-liqpay-extended' ); ?>
							</li>
							<li><?php echo __( 'and more', 'mrkv-liqpay-extended' ); ?></li>
						</ul>
						<a class="button button-primary" href="https://morkva.co.ua/shop/woocommerce-liqpay-extended-pro/?utm_source=plugin&utm_medium=sidebar&utm_campaign=liqpay_free" target="blanc">
							<?php echo __( 'Buy Pro-version', 'mrkv-liqpay-extended' ); ?>
						</a>
					</div>
					<div class="morkva-settings-sidebar_inner" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;margin-bottom:15px;">
						<h3 style="margin-top: 0;"><?php echo __( 'Other free plugins', 'mrkv-liqpay-extended' ); ?></h3>
						<?php
							$response = wp_remote_get( 'https://morkva.co.ua/wp-json/pluginManagement/v2', array(
								'headers' => array(
								),
								'timeout' => 30,
								'redirection' => 5,
								'httpversion' => '1.1',
								'sslverify' => true
							));

							$mrkv_mono_response_data = $response['body'] ? json_decode( $response['body'], true ) : null;
							$mrkv_mono_plugins = $mrkv_mono_response_data['plugins'] ?? [];

							if(!empty($mrkv_mono_plugins))
							{
								?>
									<ul style="list-style: disc;padding-left: 17px;">
										<?php
											foreach($mrkv_mono_plugins as $plugin_slug => $plugin_data)
											{
												if($plugin_slug == 'mrkv-liqpay-extended'){ continue; }
												?>
													<li><a style="display:block; margin-bottom:5px;" href="<?php echo $plugin_data['url'] ?? ''; ?>" target="blanc" class="plugin_line"><?php echo $plugin_data['label'] ?? ''; ?></a></li>
												<?php
											}
										?>
									</ul>
								<?php
							}
						?>
					</div>
				</div>
			<?php
		}

		/**
	     * Generating meta boxes
	     *
	     * @since 1.0.0
	     */
	    public function mrkv_liqpay_add_meta_boxes()
	    {
	    	# Check hpos
	        if(class_exists( CustomOrdersTableController::class )){
	            $screen = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
	            ? wc_get_page_screen_id( 'shop-order' )
	            : 'shop_order';
	        }
	        else{
	            $screen = 'shop_order';
	        }

	        # Check order id
	    	if (isset($_GET["post"]) || isset($_GET["id"])) 
	    	{
	    		# Set order id
	    		$order_id = '';

	    		# Check get data
	            if(isset($_GET["post"]))
	            {
	            	# Set order id
	                $order_id = $_GET["post"];    
	            }
	            else
	            {
	            	# Set order id
	                $order_id = $_GET["id"];
	            }

	            # Get order by id
	            $order = wc_get_order($order_id);

            	if($order)
            	{
            		# Get payment method
		            $payment_method = $order->get_payment_method();

		            # Check liqpaypay method
		            if('morkva-liqpay' == $payment_method && $order->get_meta('mrkv_liqpay_accuiring_action') == 'hold')
		            {
		            	# Add metabox
		         		add_meta_box('mrkv_liqpay_hold', __('Liqpay Hold', 'mrkv-liqpay-extended'), array( $this, 'mrkv_liqpay_add_plugin_meta_box' ), $screen, 'side', 'core');   
		            }
            	}
	    	}
	    }

	    public function mrkv_liqpay_add_plugin_meta_box()
	    {
	    	# Check order id
	    	if (isset($_GET["post"]) || isset($_GET["id"])) 
	    	{
	    		# Set order id
	    		$order_id = '';

	    		# Check get data
	            if(isset($_GET["post"]))
	            {
	            	# Set order id
	                $order_id = $_GET["post"];    
	            }
	            else
	            {
	            	# Set order id
	                $order_id = $_GET["id"];
	            }

	            # Get order by id
	            $order = wc_get_order($order_id);

	            if($order)
	            {
            		$order_status = $order->get_status();
			        if ($order_status == 'on-hold') 
			        {
			            $finalize_text = __('Finalize', 'mrkv-liqpay-extended');
				        $cancel_hold_text = __('Cancel hold', 'mrkv-liqpay-extended');
				        $enter_amount_text = __('Enter amount', 'mrkv-liqpay-extended');
				        $cancel_text = __('Cancel', 'mrkv-liqpay-extended');
				        $payment_amount = $order->get_total();
		        		?>
				            <div id="hold_form_container">
				                <label for="liqpay_amount" class="label-on-top">
				                    <?php echo $enter_amount_text; ?>
				                </label>
				                <div class="col-sm">
				                    <div class="input-group">
				                        <input type="text" id="liqpay_amount" name="finalization_amount" required="required"
				                               value="<?php echo $payment_amount; ?>"/>
				                    </div>
				                </div>
				                <br/>
				                <div class="text-left">
				                    <a class="button button-danger" onclick="jQuery.ajax({
				                            url: '<?php echo admin_url( "admin-ajax.php" ) ?>',
				                            type: 'POST',
				                            data: {
				                            	'action' : 'mrkv_liqpay_cancel_payment_hold',
				                                'order_id': '<?php echo $order_id; ?>',
				                            },
				                            success: function (response) {
				                                window.location.reload();
				                            },
				                        })"><?php echo $cancel_hold_text; ?></a>                                     
				                
				                    <a class="button button-primary" onclick="jQuery.ajax({
				                            url: '<?php echo admin_url( "admin-ajax.php" ) ?>',
				                            type: 'POST',
				                            data: {
				                            	'action' : 'mrkv_liqpay_final_payment_hold',
				                                'order_id': '<?php echo $order_id; ?>',
				                                'finalization_amount': document.getElementById('liqpay_amount').value,
				                            },
				                            success: function (response) {
				                                window.location.reload();
				                            },
				                        })"><?php echo $finalize_text; ?></a>
				                </div>
				                <div><?php echo __('Holding period is 30 days','mrkv-liqpay-extended'); ?></div>
				            </div>
		        		<?php
			        }
			        else
			        {
			        	echo __('Hold is not applied to the order.','mrkv-liqpay-extended');
			        }
	            }
	        }
	    }

	    public function mrkv_liqpay_cancel_payment_hold_func()
		{
			$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

			if (!$order_id) {
	            return;
	        }

	        $order = wc_get_order($order_id);
	        if ($order) 
	        {
	            $order_status = $order->get_status();
		        if ($order_status == 'on-hold') 
		        {
		        	# Get token by mono gateway
		    		$wc_gateways      = new WC_Payment_Gateways();
		    		$payment_gateways = $wc_gateways->get_available_payment_gateways();
		    		$liqpay_payment_gateway = $payment_gateways['morkva-liqpay'];

		    		# Include Api morkva liqpay
        			require_once(__DIR__ . '/classes/MorkvaLiqPay.php');
		    		$mrkv_liqpay_token = $liqpay_payment_gateway->get_keys_access();

		    		$mrkv_liqpay_payment = new MorkvaLiqPay($mrkv_liqpay_token['public_key'], $mrkv_liqpay_token['private_key']);

		    		$mrkv_liqpay_payment->mrkv_liqpay_hold_cancel($order_id, $order->get_total());

		    		$order->add_order_note(__('Hold canceled', 'mrkv-liqpay-extended'));
		        }
	        }

	        wp_die();
		}

		public function mrkv_liqpay_final_payment_hold_func()
		{
			$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

			if (!$order_id) {
	            return;
	        }

	        $order = wc_get_order($order_id);
	        if ($order) 
	        {
	            $order_status = $order->get_status();
		        if ($order_status == 'on-hold') 
		        {
		        	# Get token by mono gateway
		    		$wc_gateways      = new WC_Payment_Gateways();
		    		$payment_gateways = $wc_gateways->get_available_payment_gateways();
		    		$liqpay_payment_gateway = $payment_gateways['morkva-liqpay'];
		    		$mrkv_liqpay_token = $liqpay_payment_gateway->get_keys_access();

		    		# Include Api Morkva liqpay
        			require_once(__DIR__ . '/classes/MorkvaLiqPay.php');
        			$mrkv_liqpay_payment = new MorkvaLiqPay($mrkv_liqpay_token['public_key'], $mrkv_liqpay_token['private_key']);

        			$result = $mrkv_liqpay_payment->mrkv_liqpay_hold_final($order_id, $order->get_total());

        			if(isset($result['error']))
        			{
        				$order->add_order_note(__('Error: ' . print_r($result['error'], 1), 'mrkv-liqpay-extended'));
        			}
        			else
        			{
        				$order->add_order_note(__('Hold finalized', 'mrkv-liqpay-extended'));
		            	$order->payment_complete();
		            	$order->save();
        			}
		        }
	        }

	        wp_die();
		}
	}
}