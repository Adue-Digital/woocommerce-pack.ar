<?php

require_once __DIR__ . '/../inc/Http.php';
require_once __DIR__ . '/../inc/AdueShippingMethod.php';

class WC_Adue_Correo_Argentino_Domicilio extends AdueShippingMethod
{

    public function __construct($instance_id = 0)
    {
        $this->id = 'adue_correo_argentino_domicilio';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('Correo Argentino a domicilio', 'woocommerce');

        $this->supports = [
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        ];

        $this->init();

        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->shipping_type = 'envio-a-domicilio';
    }

    public function calculate_shipping($package = [])
    {
        $this->add_rate([
            'id' => $this->id,
            'label' => $this->title,
            'cost' => $this->priceResponse->price
        ]);
    }

}
