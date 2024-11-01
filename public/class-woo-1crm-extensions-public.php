<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.visual4.de/
 * @since      1.0.0
 *
 * @package    Woo_1crm_Extensions
 * @subpackage Woo_1crm_Extensions/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_1crm_Extensions
 * @subpackage Woo_1crm_Extensions/public
 * @author     visual4 GmbH <info@visual4.de>
 */
class Woo_1crm_Extensions_Public
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
     * The plugin helper.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $helper;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->helper = new Woo_1crm_Extensions_Public_Helper();

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

//        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-1crm-extensions-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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

//        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-1crm-extensions-public.js', array('jquery'), $this->version, false);

    }

    /**
     * Display CustomFields in Checkout
     *
     * @param $fields
     * @return mixed
     */
    public function display_custom_fields_in_checkout($fields)
    {
        $billingcustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['billing']['htmlid']));
        $htmlbillingfields = $this->helper->createHtmlFormFields($billingcustomfields);
        $fields = $this->helper->addFieldsToSectionAfter($fields, 'billing', $htmlbillingfields);

        $shippingcustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['shipping']['htmlid']));
        $htmlshippingfields = $this->helper->createHtmlFormFields($shippingcustomfields);
        $fields = $this->helper->addFieldsToSectionAfter($fields, 'shipping', $htmlshippingfields);

        $accountcustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['account']['htmlid']));
        $htmlaccountfields = $this->helper->createHtmlFormFields($accountcustomfields);
        $fields = $this->helper->addFieldsToSectionAfter($fields, 'account', $htmlaccountfields);

        $ordercustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['order']['htmlid']));
        $htmlorderfields = $this->helper->createHtmlFormFields($ordercustomfields);
        $fields = $this->helper->addFieldsToSectionAfter($fields, 'order', $htmlorderfields);

        //Add Salutation
        $htmlsalutation = $this->helper->createSalutationHtml();
        $fields = $this->helper->addFieldsToSectionBefore($fields, 'billing', $htmlsalutation);

        return $fields;
    }

    /**
     * Add all FieldValues to CRM Webhook
     *
     * @param $http_args
     * @return mixed
     */
    public function add_http_args_to_woocommerce_webhook($http_args)
    {
        $body = json_decode($http_args['body']);

        if (isset($body->order)) {
            $body = $this->helper->addProductUrlToWebhookBody($body);
            $body = $this->helper->addCustomFieldsToWebhookBody($body);
            $body = $this->helper->addPayPalMetaToWebhookBody($body);

            $http_args['body'] = json_encode($body);
        }

        return $http_args;
    }

    /**
     * Add Meta Fields to Order
     *
     * @param $order_id
     */
    public function custom_order_field_update($order_id)
    {
        $billingcustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['billing']['htmlid']));
        $shippingcustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['shipping']['htmlid']));
        $accountcustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['account']['htmlid']));
        $ordercustomfields = $this->helper->getAllFieldsFromOptions(get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['order']['htmlid']));
        $customfields = array_merge($billingcustomfields, $shippingcustomfields, $accountcustomfields, $ordercustomfields);

        //Iterate and create Formfield
        foreach ($customfields as $customfield) {
            if (!empty($_POST[$customfield['fieldid']])) {
                $filtered_var = filter_var(esc_textarea($_POST[$customfield['fieldid']]), FILTER_SANITIZE_STRING);
                update_post_meta($order_id, $customfield['fieldid'], $filtered_var);
            }
        }
    }

}
