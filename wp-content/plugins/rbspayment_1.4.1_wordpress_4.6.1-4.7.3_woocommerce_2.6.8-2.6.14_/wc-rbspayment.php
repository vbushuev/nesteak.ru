<?php
/*
  Plugin Name: Платежный шлюз RBS
  Description: Позволяет использовать платежный шлюз RBS с плагином WooCommerce
  Version: 1.4.1
 */

session_start();
require_once 'include.php';
require_once 'text.php';

if (!defined('ABSPATH')) exit;


add_action('plugins_loaded', 'woocommerce_rbspayment', 0);

function woocommerce_rbspayment()
{
    if (!class_exists('WC_Payment_Gateway'))
        return;
    if (class_exists('WC_RBSPAYMENT'))
        return;


    class WC_RBSPAYMENT extends WC_Payment_Gateway
    {


        function rbs_logger($var)
        {
            if ($var) {
                $date = '>>>> ' . date('Y-m-d H:i:s') . "\n";
                $result = $var;
                if (is_array($var) || is_object($var)) {
                    $result = print_r($var, true);
                }
                $result .= "\n\n";
                $path = 'wp-content/plugins/rbspayment-for-woocomerce/' . LOG_FILE_NAME;
                error_log($date . $result, 3, $path);
                return true;
            }
            return false;
        }

        function callb()
        {

            if (isset($_GET['rbspayment']) AND $_GET['rbspayment'] == 'result') {
                if ($_SESSION['testmode'] == 'yes') {
                    $action_adr = TEST_URL;
                } else {
                    $action_adr = PROD_URL;
                }

                $action_adr .= 'getOrderStatusExtended.do';

                $args = array(
                    'userName' => $_SESSION['userName'],//$this->get_option('merchant'),
                    'password' => $_SESSION['password'],//$this->get_option('password'),
                    'orderId' => $_GET['orderId'],
                );
                $_SESSION['userName'] = '';
                $_SESSION['password'] = '';
                $_SESSION['testmode'] = '';

                $rbsCurl = curl_init();
                curl_setopt_array($rbsCurl, array(
                    CURLOPT_URL => $action_adr,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($args)
                ));
                $response = curl_exec($rbsCurl);
                curl_close($rbsCurl);


                if (DEBUG) {
                    $this->rbs_logger('Request: ' . $action_adr . ': ' . print_r($args, true) . 'Response: ' . $response);
                }
                $response = json_decode($response, true);

                $orderStatus = $response['orderStatus'];
                if ($orderStatus == '1' || $orderStatus == '2') {
                    $order_id = $_GET['order_id'];
                    $order = new WC_Order($order_id);
                    $order->update_status('processing', __('Платеж успешно оплачен', 'woocommerce'));
                    WC()->cart->empty_cart();
                    $order->payment_complete();
                    wp_redirect($this->get_return_url($order));
                    exit;
                } else {
                    $order_id = $_GET['order_id'];
                    $order = new WC_Order($order_id);
                    $order->update_status('failed', __('Платеж не оплачен', 'woocommerce'));
                    add_filter('woocommerce_add_to_cart_message', 'my_cart_messages', 99);
                    $order->cancel_order();

                    wc_add_notice(__('Ошибка в проведении оплаты<br/>' . $response['actionCodeDescription'], 'woocommerce'), 'error');
                    wp_redirect($order->get_cancel_order_url());
                    exit;
                }
            }
        }

        public function __construct()
        {

            if (isset($_GET['wc-callb']) AND $_GET['wc-callb'] == 'callback_function') {
                $this->callb();
                exit;
            }

            $this->id = RBSPAYMENT_ID;
            $this->has_fields = false;
            $this->liveurl = PROD_URL;
            $this->testurl = TEST_URL;

            // Load the settings
            $this->init_form_fields();
            $this->init_settings();

            // Define user set variables
            $this->title = $this->get_option('title');
            $this->merchant = $this->get_option('merchant');
            $this->password = $this->get_option('password');
            $this->testmode = $this->get_option('testmode');
            $this->stage = $this->get_option('stage');
            $this->description = $this->get_option('description');

            // Actions
            add_action('valid-rbspayment-standard-ipn-reques', array($this, 'successful_request'));
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

            // Save options
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

            if (!$this->is_valid_for_use()) {
                $this->enabled = false;
            }
            $this->callb();
        }


        /**
         * Check if this gateway is enabled and available in the user's country
         */
        function is_valid_for_use()
        {
            if (!in_array(get_option('woocommerce_currency'), array('RUB'))) {
                return false;
            }
            return true;
        }

        /**
         * Admin Panel Options
         */
        public function admin_options()
        {
            ?>
            <h3><?php _e(RBSPAYMENT_TITLE_1, 'woocommerce'); ?></h3>
            <p><?php _e(RBSPAYMENT_TITLE_2, 'woocommerce'); ?></p>

            <?php if ($this->is_valid_for_use()) : ?>

            <table class="form-table">

                <?php
                // Generate the HTML For the settings form.
                $this->generate_settings_html();
                ?>
            </table>

        <?php else : ?>
            <div class="inline error"><p>
                    <strong><?php _e('Шлюз отключен', 'woocommerce'); ?></strong>: <?php _e($this->id . ' не поддерживает валюты Вашего магазина.', 'woocommerce'); ?>
                </p></div>
            <?php
        endif;

        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Включить/Выключить', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Включен', 'woocommerce'),
                    'default' => 'yes'
                ),
                'title' => array(
                    'title' => __('Название', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Это название, которое пользователь видит во время проверки.', 'woocommerce'),
                    'default' => __(RBSPAYMENT_NAME, 'woocommerce')
                ),
                'merchant' => array(
                    'title' => __('Логин', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Пожалуйста введите Логин', 'woocommerce'),
                    'default' => ''
                ),
                'password' => array(
                    'title' => __('Пароль', 'woocommerce'),
                    'type' => 'password',
                    'description' => __('Пожалуйста введите пароль.', 'woocommerce'),
                    'default' => ''
                ),
                'stage' => array(
                    'title' => __('Одно- или дву-стадийная оплата', 'woocommerce'),
                    'type' => 'select',
                    'default' => 'one-stage',
                    'options' => array(
                        'one-stage' => __('Одностадийные платежи', 'woocommerce'),
                        'two-stage' => __('Двухстадийные платежи', 'woocommerce'),
                    ),
                ),
                'testmode' => array(
                    'title' => __('Тест режим', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Включен', 'woocommerce'),
                    'description' => __('В этом режиме плата за товар не снимается.', 'woocommerce'),
                    'default' => 'no'
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Описанием метода оплаты которое клиент будет видеть на вашем сайте.', 'woocommerce'),
                    'default' => 'Оплата с помощью ' . RBSPAYMENT_NAME
                )
            );
        }

        /**
         * Generate the dibs button link
         */
        public function generate_form($order_id)
        {

            $order = new WC_Order($order_id);

            if ($this->testmode == 'yes') {
                $action_adr = $this->testurl;
            } else {
                $action_adr = $this->liveurl;
            }

            $extra_url_param = '';//GA
            if ($this->stage == 'two-stage') {
                //GA
                $action_adr .= 'registerPreAuth.do';
            } else if ($this->stage == 'one-stage') {
                $extra_url_param = '&wc-callb=callback_function';//GA
                $action_adr .= 'register.do';
            }

            $_SESSION['userName'] = $this->merchant;
            $_SESSION['password'] = $this->password;
            $_SESSION['testmode'] = $this->testmode;

            for ($i = 0; $i++ < 30;) {
                $args = array(
                    'userName' => $this->merchant,
                    'password' => $this->password,
                    'orderNumber' => $order_id . '_' . $i,
                    'amount' => $order->order_total * 100,
                    'returnUrl' => SHOP_URL . '?wc-api=WC_RBSPAYMENT&rbspayment=result&order_id=' . $order_id . $extra_url_param,
                );
                $rbsCurl = curl_init();
                curl_setopt_array($rbsCurl, array(
                    CURLOPT_URL => $action_adr,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($args)
                ));
                $response = curl_exec($rbsCurl);
                curl_close($rbsCurl);

                if (DEBUG) {
                    //$this->rbs_logger('Request: ' . $action_adr . ': ' . print_r($args,true).'Response: ' . $response);
                }
                $response = json_decode($response, true);
                if ($response['errorCode'] != '1') break;
            }

            $errorCode = $response['errorCode'];
            if ($errorCode == 0) {
                return '<p>' . __('Спасибо за Ваш заказ, пожалуйста, нажмите кнопку ниже, чтобы заплатить.', 'woocommerce') . '</p>' .
                '<a class="button cancel" href="' . $response['formUrl'] . '">' . __('Оплатить', 'woocommerce') . '</a>' .
                '<a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __('Отказаться от оплаты & вернуться в корзину', 'woocommerce') . '</a>';
            } else {
                return '<p>' . __('Ошибка #' . $errorCode . ': ' . $response['errorMessage'], 'woocommerce') . '</p>' .
                '<a class="button cancel" href="' . $order->get_cancel_order_url() . '">' . __('Отказаться от оплаты & вернуться в корзину', 'woocommerce') . '</a>';
            }
        }

        /**
         * Process the payment and return the result
         */
        function process_payment($order_id)
        {
            $order = new WC_Order($order_id);

            return array(
                'result' => 'success',
                'redirect' => add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay'))))
            );
        }

        /**
         * Receipt page
         */
        function receipt_page($order)
        {
            echo $this->generate_form($order);
        }

        /**
         * Check response
         */

        function check_response()
        {
            global $woocommerce;

            wp_redirect($this->get_return_url($order));
            if (isset($_GET['rbspayment']) AND $_GET['rbspayment'] == 'result') {
                if (DEBUG) {
                    $action_adr = TEST_URL;
                } else {
                    $action_adr = PROD_URL;
                }

                $action_adr .= 'getOrderStatusExtended.do';

                $args = array(
                    'userName' => 'test',//$this->get_option('merchant'),
                    'password' => 'testPwd',//$this->get_option('password'),
                    'orderId' => $_GET['orderId'],
                );

                $rbsCurl = curl_init();
                curl_setopt_array($rbsCurl, array(
                    CURLOPT_URL => $action_adr,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($args)
                ));
                $response = curl_exec($rbsCurl);
                curl_close($rbsCurl);
                error_log($response);
                $response = json_decode($response, true);

                $orderStatus = $response['OrderStatus'];

                if ($orderStatus == '1' || $orderStatus == '2') {
                    $order_id = $_GET['order_id'];
                    $order = new WC_Order($order_id);
                    $order->update_status('completed', __('Платеж успешно оплачен', 'woocommerce'));
                    WC()->cart->empty_cart();
                    $order->payment_complete();
                    wp_redirect($this->get_return_url($order));
                    exit;
                } else {
                    $order_id = $_GET['order_id'];
                    $order = new WC_Order($order_id);
                    $order->update_status('failed', __('Платеж не оплачен', 'woocommerce'));
                    add_filter('woocommerce_add_to_cart_message', 'my_cart_messages', 99);
                    $order->cancel_order();
                    $woocommerce->add_error(__('Ошибка в проведении оплаты<br/>' . $response['actionCodeDescription'], 'woocommerce'));
                    wp_redirect($order->get_cancel_order_url());
                    exit;
                }
            }
        }

    }

    /**
     * Add the gateway to WooCommerce
     */
    function add_rbspayment_gateway($methods)
    {

        $methods[] = 'WC_RBSPAYMENT';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_rbspayment_gateway');
}

?>
