<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.visual4.de/
 * @since      1.0.0
 *
 * @package    Woo_1crm_Extensions
 * @subpackage Woo_1crm_Extensions/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_1crm_Extensions
 * @subpackage Woo_1crm_Extensions/admin
 * @author     visual4 GmbH <info@visual4.de>
 */
class Woo_1crm_Extensions_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The display handler for additional fields.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $displayhandler_additionalfields;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->load_dependencies();

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->displayhandler_additionalfields = new displayhandler_additionalfields();

    }

    private function load_dependencies() {

        require_once 'partials/woo-1crm-extensions-admin-displayhandler-additionalfields.php';

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_1crm_Extensions_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_1crm_Extensions_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-1crm-extensions-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_1crm_Extensions_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_1crm_Extensions_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-1crm-extensions-admin.js', array('jquery'), $this->version, false);

    }

    /**
     * Include Endpointhandler Class
     */
    public function include_endpointhandler_class()
    {
        include_once('class-woo-1crm-extensions-endpointhandler.php');
    }

    public function add_endpointhandler_class_to_wc($classes)
    {
        $classes[] = 'Class_Woo_1crm_Extensions_Endpointhandler';
        return $classes;
    }

    /**
     * send mail if webhook fails
     */
    public function add_errorhandling_option_to_wc_settings($settings)
    {
        $settings[] = array(
            'title' => __('Error Handling', 'woocommerce'),
            'desc' => __('send an email to administrator when Webhook fails', 'woocommerce'),
            'id' => 'woocommerce_api_send_error',
            'type' => 'checkbox',
            'default' => 'no',
        );
        return $settings;
    }

    public function send_mail_if_webhook_fails($http_args, $response)
    {
        // send mail if not 200
        $res_code = $response['response']['code'];
        $send_error = get_option('woocommerce_api_send_error') == 'yes' ? true : false;
        if ($res_code && $res_code != 200 && $send_error) {
            $admin_email = get_option('admin_email');
            $home_url = rtrim(get_home_url(), "/");
            $res_msg = $response['response']['message'];
            $req_hook = $http_args['headers']['X-WC-Webhook-Topic'];
            $req_body = $http_args['body'];
            $body = json_decode($req_body, true);
            $order_id = $body['order']['id'];
            if ($order_id && $home_url)
                $link = "$home_url/wp-admin/post.php?post=$order_id&action=edit";
            else
                $link = false;
            $message =
                "Error: $res_code"
                . "\nMessage: $res_msg"
                . "\nWebhook: $req_hook"
                . "\nRequest: $req_body";
            if ($link) $message .= "\nOrder Link: $link";
            $message .= "\n\n!IMPORTANT! Webhook will be deactivated after a few errors!";
            mail($admin_email, "WooCommerce Webhook Error <$home_url>", $message);
        }
    }


    /**
     * add raw prices to order webhook
     *
     * @param $order_data
     * @param $order WC_Order
     * @param $fields
     * @param $server
     * @return mixed
     */
    public function add_raw_prices_to_order_webhook($order_data, $order, $fields, $server)
    {
        //add full price to order_data
        $order_data['total_raw'] = (double) $order->get_total();
        $order_data['subtotal_raw'] = (double) $order->get_subtotal();
        $order_data['total_tax_raw'] = (double) $order->get_total_tax();
        $order_data['total_shipping_raw'] = (double) $order->get_total_shipping();
        $order_data['cart_tax_raw'] = (double) $order->get_cart_tax();
        $order_data['shipping_tax_raw'] = (double) $order->get_shipping_tax();
        $order_data['total_discount_raw'] = (double) $order->get_total_discount();

        //add full price to line items
        $items = $order->get_items();
        for ($i = 0; $i < count($order_data['line_items']); $i++) {
            $line_item = &$order_data['line_items'][$i];
            $item = $items[$line_item['id']];

            $line_item['subtotal_raw'] = (double) $order->get_line_subtotal($item, false, false);
            $line_item['subtotal_tax_raw'] = (double) $item['line_subtotal_tax'];
            $line_item['total_raw'] = (double) $order->get_line_total($item, false, false);
            $line_item['total_tax_raw'] = (double) $item['line_tax'];
            $line_item['price_raw'] = (double) $order->get_item_total($item, false, false);
        }

        //add full price to shipping lines
        $shipping_items = $order->get_shipping_methods();
        for($i = 0; $i < count($order_data['shipping_lines']); $i++){
            $shipping_line = &$order_data['shipping_lines'][$i];
            $shipping_item = $shipping_items[$shipping_line['id']];

            $shipping_line['total_raw'] = (double) $shipping_item['cost'];
        }

        //add full price to tax lines
        $taxes = $order->get_tax_totals();
        for($i = 0; $i < count($order_data['tax_lines']); $i++){
            $tax_line = &$order_data['tax_lines'][$i];
            $tax = $taxes[$tax_line['code']];

            $tax_line['total_raw'] = (double) $tax->amount;
        }

        //add full price to fee lines
        $fee_items = $order->get_fees();
        for($i = 0; $i < count($order_data['fee_lines']); $i++){
            $fee_line = &$order_data['fee_lines'][$i];
            $fee_item = $fee_items[$fee_line['id']];

            $fee_line['total_raw'] = (double) $order->get_line_total( $fee_item, false, false );
            $fee_line['total_tax_raw'] = (double) $fee_item['line_tax'];
        }

        //add full price to coupon lines
        $coupon_items = $order->get_items( 'coupon' );
        for($i = 0; $i < count($order_data['coupon_lines']); $i++){
            $coupon_line = &$order_data['coupon_lines'][$i];
            $coupon_item = $coupon_items[$coupon_line['id']];

            $coupon_line['amount_raw'] = (double) $coupon_item['discount_amount'];
        }

        return $order_data;
    }
    
    /**
     * Add menues for the admin area.
     *
     * @since    1.0.0
     */
    public function add_menue()
    {
        // create new top-level menu
        add_menu_page('Woo 1CRM Extensions', 'Woo 1CRM Extensions', 'manage_options', 'woo1crm-extensions', array($this, 'display_pluginOverview'));
        add_submenu_page('woo1crm-extensions', 'Overview', 'Overview', 'manage_options', 'woo1crm-extensions', array($this, 'display_pluginOverview'));
        add_submenu_page('woo1crm-extensions', 'Additional Fields Settings', 'Additional Fields', 'manage_options', 'woo1crm-additional-fields', array($this, 'display_additionalFields'));

    }
    
    function register_woo1crmadditionalfields_plugin_settings()
    {
        //register our settings
        register_setting('woo1crmadditionalfields-group', Woo_1crm_Extensions_Public_Setting::$SECTIONS['billing']['htmlid']);
        register_setting('woo1crmadditionalfields-group', Woo_1crm_Extensions_Public_Setting::$SECTIONS['shipping']['htmlid']);
        register_setting('woo1crmadditionalfields-group', Woo_1crm_Extensions_Public_Setting::$SECTIONS['account']['htmlid']);
        register_setting('woo1crmadditionalfields-group', Woo_1crm_Extensions_Public_Setting::$SECTIONS['order']['htmlid']);

    }

    function display_pluginOverview(){
        $this->displayhandler_additionalfields->overview();
    }
    
    function display_additionalFields()
    {
        $this->displayhandler_additionalfields->additional_fields();
    }

}
