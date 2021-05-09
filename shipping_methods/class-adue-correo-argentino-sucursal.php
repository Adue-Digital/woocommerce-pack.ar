<?php

require_once __DIR__ . '/../inc/Http.php';
require_once __DIR__ . '/../inc/AdueShippingMethod.php';

class WC_Adue_Correo_Argentino_Sucursal extends AdueShippingMethod
{

    public function __construct($instance_id = 0)
    {
        $this->id = 'adue_correo_argentino_sucursal';
        $this->instance_id = absint($instance_id);
        $this->method_title = __('Correo Argentino a sucursal', 'woocommerce');

        $this->supports = [
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        ];

        $this->init();

        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->shipping_type = 'envio-a-sucursal';
    }

    public function calculate_shipping($package = [])
    {
        $x = 1;
        foreach ($this->priceResponse->branch_office as $code => $branchOfficeAddress) {
            $title = $this->title.'. '.$branchOfficeAddress;
            $title .= $this->isFreeShipping($package) ? ' GRATIS' : '';
            $this->add_rate([
                'id' => $this->id.'_'.$code,
                'label' => $title,
                'cost' => $this->isFreeShipping($package) ? 0 : $this->priceResponse->price,
            ]);
            $x++;
        }

    }

}
