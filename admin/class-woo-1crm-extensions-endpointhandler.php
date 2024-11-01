<?php

class Class_Woo_1crm_Extensions_Endpointhandler extends WC_API_Resource
{
    protected $base = '/custom';

    public function register_routes($routes)
    {
        $routes[$this->base] = array(
            array(array($this, 'get_custom'), WC_API_Server::READABLE)
        );

        return $routes;
    }

    public function get_custom()
    {
        return array('custom' => 'ready!');
    }
}