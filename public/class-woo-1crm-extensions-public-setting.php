<?php

class Woo_1crm_Extensions_Public_Setting
{

    public static $SECTIONS = [
        'billing' => [
            'htmlid' => 'billingcustomfieldsarray',
            'headline' => 'Billing',
            'headlineD' => 'Rechnung'
        ],
        'shipping' => [
            'htmlid' => 'shippingcustomfieldsarray',
            'headline' => 'Shipping',
            'headlineD' => 'Versand'
        ],
        'account' => [
            'htmlid' => 'accountcustomfieldsarray',
            'headline' => 'Account',
            'headlineD' => 'Benutzer'
        ],
        'order' => [
            'htmlid' => 'ordercustomfieldsarray',
            'headline' => 'Order',
            'headlineD' => 'Bestellung allgemein'
        ],
    ];


public static  $FIELD_TYPES = array('input' => text,'textarea' => textarea,'dropdown' => select,'checkbox' => checkbox,);

}