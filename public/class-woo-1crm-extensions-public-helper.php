<?php

class Woo_1crm_Extensions_Public_Helper
{
    public function cleanupHtmlDescription($html)
    {
        $html = str_replace(
            ['<li>', '</li>', '<br/>', '</tr>', '</td>', '</p>'],
            ['- ', "<br/>\r\n", "<br/>\r\n", "<br/>\r\n", '    ', "<br/>\r\n"],
            $html
        );
        $html = str_replace(
            ["- <br/>"],
            ["<br/>"],
            $html
        );
        $html = strip_tags($html, '<br><br/>');
        return $html;
    }


    //--------WEBHOOK------------
    public function addCustomFieldsToWebhookBody($body){

        $billingserializedoptionstring= get_option( Woo_1crm_Extensions_Public_Setting::$SECTIONS['billing']['htmlid'] );
        $billingcustomfields = $this->getAllFieldsFromOptions($billingserializedoptionstring);
        $body=$this->addFieldsToBody($billingcustomfields,$body, 'billing');


        $shippingserializedoptionstring = get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['shipping']['htmlid']);
        $shippingcustomfields = $this->getAllFieldsFromOptions($shippingserializedoptionstring);
        $body=$this->addFieldsToBody($shippingcustomfields,$body,'shipping');


        $accountserializedoptionstring = get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['account']['htmlid']);
        $accountcustomfields= $this->getAllFieldsFromOptions($accountserializedoptionstring);
        $body=$this->addFieldsToBody($accountcustomfields,$body,'account');


        $orderserializedoptionstring = get_option(Woo_1crm_Extensions_Public_Setting::$SECTIONS['order']['htmlid']);
        $ordercustomfields= $this->getAllFieldsFromOptions($orderserializedoptionstring);
        $body=$this->addFieldsToBody($ordercustomfields,$body,'order');

        return $body;
    }


    function addFieldsToBody($customfields,$body,$sectionstring) {

        $fieldgroup = new stdClass();

        //Iterate and create Formfield
        foreach($customfields as $customfield) {

            $field = get_post_meta($body->order->id, $customfield['fieldid']);

            if ($field) {
                $fieldId = $customfield['fieldid'];
                $fieldgroup->$fieldId = $field[0];
            }

        }

        $body->order->customfieldscrm->$sectionstring = $fieldgroup;

        return $body;
    }

    function getAllFieldsFromOptions($optionstring){

        //Convert String in ARRAY
        $customfieldsarray = json_decode($optionstring,true);

        //Split Fields in logic group of 3 items: name, type, value
        $customfields = $this->groupAllFields($customfieldsarray);

        return $customfields;
    }

    function groupAllFields($plaincustomfieldsarray){


        if(empty($plaincustomfieldsarray))
            return array();

        //Split Array in Groups of 4 Items
        $customfields = array();
        $customfield = array();

        $i = 1;
        foreach($plaincustomfieldsarray as $item){

            switch ($item['name']) {
                case 'fieldname':
                    $customfield['fieldname'] = $item['value'];
                    break;
                case 'fieldid':
                     $customfield['fieldid']  = $item['value'];
                    break;
                case 'fieldtype':
                    $customfield['fieldtype'] = $item['value'];
                    break;
                case 'fieldvalues':
                    $customfield['fieldvalues'] = $item['value'];
                    break;

            }


            if ($i % 4 == 0)
            {
                array_push($customfields, $customfield);
                $customfield = array();
            }
            $i++;

        }

        return $customfields;
    }


    public function addPayPalMetaToWebhookBody($body){

        //PAYPAL
        if ($body->order->payment_details->method_id == 'paypal' && $body->order->payment_details->paid) {
            $body->order->payment_details->transaction_id = get_post_meta($body->order->id, '_transaction_id');
            $body->order->payment_details->paid_date = get_post_meta($body->order->id, '_paid_date');
            $body->order->payment_details->payer_email = get_post_meta($body->order->id, 'Payer PayPal address');
        }

        return $body;
    }


    public function addProductUrlToWebhookBody($body){

        foreach ($body->order->line_items as &$line) {
            $post = get_post($line->product_id);
            $line->description = $this->cleanupHtmlDescription($post->post_content);

            // Add product URL
            $line->url = get_permalink($post->ID);

        }

        return $body;
    }


    //--------HTML Display------------

    public function createSalutationHtml(){

        $salutation['billing_salutation'] = array(
            'label' => 'Anrede',//
            'required' => false,
            'class' => array('form-row-wide'),
            'clear' => true,
            'type' => 'select',
            'options' => array('Mr.' => 'Herr', 'Ms.' => 'Frau'),
        );

        return $salutation;
    }

    public function createHtmlFormFields($customfields){

        $htmlfields = array();

        //Iterate and create HTMLFormfield
        foreach($customfields as $customfield) {

            $htmlfield = array(
                'label' => $customfield['fieldname'],
                'required' => false,
                'class' => array('form-row-wide'),
                'type' =>  Woo_1crm_Extensions_Public_Setting::$FIELD_TYPES[$customfield['fieldtype']]);

            if($customfield['fieldtype'] == 'dropdown') {

                $a = explode(';', $customfield['fieldvalues']);
                $options = array();

                foreach($a as $item){
                    $options[$item]=$item;
                }

                $htmlfield['options'] = $options;

            }

            $htmlfields[$customfield['fieldid']] = $htmlfield;

        }

        return $htmlfields;
    }

    public function addFieldsToSectionAfter($fields,$section,$htmlsfields){

        $fields[$section] = array_merge( $fields[$section],$htmlsfields);

        return $fields;
    }

    public function addFieldsToSectionBefore($fields,$section,$htmlsfields){

        $fields[$section] = array_merge($htmlsfields,$fields[$section]);

        return $fields;
    }
}