<?php

class Class_Woo_1crm_Extensions_Webhookhandler
{
    public function __construct()
    {
        add_filter('woocommerce_valid_webhook_resources', array($this, 'custom_woocommerce_valid_webhook_resources'), 10, 1);
        add_filter('woocommerce_valid_webhook_events', array($this, 'custom_woocommerce_valid_webhook_events'), 10, 1);
        add_filter('woocommerce_webhook_topic_hooks', array($this, 'custom_woocommerce_webhook_topic_hooks'), 10, 1);
        add_filter('woocommerce_webhook_topics', array($this, 'custom_woocommerce_webhook_topics'), 10, 1);
        add_filter('woocommerce_webhook_payload', array($this, 'custom_woocommerce_webhook_payload'), 10, 3);

        /*
         * new
         * Webhooks
         */
        // frontend customer update
        add_action('woocommerce_customer_save_address', array($this, 'custom_woocommerce_customer_save_address'), 999, 2);
        // product category save
        add_action('created_term', array($this, 'custom_product_category'), 999, 3);
        add_action('edited_term', array($this, 'custom_product_category'), 999, 3);
        // comment
        add_action('comment_post', array($this, 'custom_comment_post'), 999, 3);

        /*
         * edit
         * Webhooks
         */
        // add custom product_category json to product
        add_filter('woocommerce_webhook_http_args', array($this, 'add_custom_product_category'), 10, 3);
    }

    // add valid webhook resources
    public static function custom_woocommerce_valid_webhook_resources($resources)
    {
        $resources[] = 'product_category';
        $resources[] = 'product_comment';
        return $resources;
    }

    // add valid webhook event
    public static function custom_woocommerce_valid_webhook_events($events)
    {
        $events[] = 'updated_fe';
        $events[] = 'created_updated';
        return $events;
    }

    // add webhook hooks
    public function custom_woocommerce_webhook_topic_hooks($topic)
    {
        $topic['customer.updated_fe'] = array(
            'custom_save_address_v4webhookaction'
        );
        $topic['product_category.created_updated'] = array(
            'custom_product_category_v4webhookaction'
        );
        $topic['product_comment.created_updated'] = array(
            'custom_comment_post_v4webhookaction'
        );
        return $topic;
    }

    // add topic to dropdown in BE (WooCommerce/API/Webhooks)
    public function custom_woocommerce_webhook_topics($topics)
    {
        $topics['customer.updated_fe'] = __('Customer Updated Frontend', 'woocommerce');
        $topics['product_category.created_updated'] = __('Product Category', 'woocommerce');
        $topics['product_comment.created_updated'] = __('Product Comment', 'woocommerce');
        return $topics;
    }

    // fill payload with custom data
    public function custom_woocommerce_webhook_payload($payload, $resource, $resource_data)
    {
        $allowed_resources = [
            'product_comment',
            'product_category'
        ];
        // check if custom resource is allowed
        if (in_array($resource, $allowed_resources)) {
            $payload = array($resource => $resource_data);
        }
        return $payload;
    }

    // add webhook topic (on frontend customer update)
    public function custom_woocommerce_customer_save_address($user_id, $load_address)
    {
        do_action('custom_save_address_v4webhookaction', $user_id, $load_address);
    }

    // add webhook topic (product category save)
    public function custom_product_category($term_id, $tt_id, $taxonomy)
    {
        global $sitepress;
        if(!empty($sitepress))
            $default_lang = $sitepress->get_default_language();

        if ($taxonomy != 'product_cat' || !empty($default_lang) && $default_lang != ICL_LANGUAGE_CODE)
            return;

        // send data
        $data = self::recursive_product_category($term_id, $taxonomy);
        do_action('custom_product_category_v4webhookaction', $data);
    }

    private function recursive_product_category($term_id, $taxonomy, $array = [])
    {
        $term = get_term($term_id, $taxonomy);
        $array = [
            'id' => $term->term_id,
            'slug' => $term->slug,
            'name' => $term->name
        ];
        if (!empty($term->parent)) {
            $array['parent'] = self::recursive_product_category($term->parent, $taxonomy, $array);
        }
        return $array;
    }

    // add webhook topic (after comment save)
    public function custom_comment_post($comment_id, $comment_approved, $comment_data)
    {
        // TODO check if comment is approved
        // TODO check if comment is on Product
        // add WooCommerce rating
        $rating = get_comment_meta($comment_id, 'rating', true);
        $comment_data['comment_rating'] = $rating;
        // get parent
        $post = get_post($comment_data['comment_post_ID']);
        $comment_data['comment_post_type'] = $post->post_type;
        // send data
        $data = array(
            'id' => $comment_id,
            'data' => $comment_data
        );
        do_action('custom_comment_post_v4webhookaction', $data);
    }

    // add custom product_category to webhooks
    public function add_custom_product_category($http_args, $arg, $current_id){
        $body = json_decode($http_args['body']);
        $taxonomy = 'product_cat';

        if (isset($body->product)) {
            // wpml support
            $product_id = function_exists('wpml_object_id_filter') ? wpml_object_id_filter($body->product->id, 'product', true) : $body->product->id;

            global $sitepress;
            // remove WPML term filters
            remove_filter('get_terms_args', array($sitepress, 'get_terms_args_filter'));
            remove_filter('get_term', array($sitepress,'get_term_adjust_id'));
            remove_filter('terms_clauses', array($sitepress,'terms_clauses'));
            // get terms
            $terms = get_the_terms( $product_id, $taxonomy);
            // restore WPML term filters
            add_filter('terms_clauses', array($sitepress,'terms_clauses'));
            add_filter('get_term', array($sitepress,'get_term_adjust_id'));
            add_filter('get_terms_args', array($sitepress, 'get_terms_args_filter'));

            $categories_advanced = [];
            foreach ($terms as $term){
                $categories_advanced[] = self::recursive_product_category($term->term_id, $taxonomy);
            }
            $body->product->categories_advanced = $categories_advanced;

            $http_args['body'] = json_encode($body);
        }

        return $http_args;
    }

}