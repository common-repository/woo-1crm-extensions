<?php

class Woo_1crm_Extensions_Public_Customfield
{

    public $fieldname;
    public $fieldid;
    public $fieldtype;
    public $fieldvalues;

    public function __construct($fieldid,$fieldname,$fieldtype,$fieldvalues) {
        $this->fieldid     = $fieldid;
        $this->fieldname   = $fieldname;
        $this->fieldtype   = $fieldtype;
        $this->fieldvalues = $fieldvalues;
    }

}