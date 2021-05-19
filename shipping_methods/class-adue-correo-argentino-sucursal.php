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

            if($this->isFreeShipping($package)) {
                $title .= ' GRATIS';
                $cost = 0;
            } else {
                $additionalFee = $this->getAditionalFeeShipping();
                if($additionalFee['aditional_fee_amount']) {
                    if($additionalFee['aditional_fee_type'] == 'percent') {
                        $cost = ($this->priceResponse->price * $additionalFee['aditional_fee_amount']) / 100 + $this->priceResponse->price;
                    } else {
                        $cost = $this->priceResponse->price + $additionalFee['aditional_fee_amount'];
                    }
                } else {
                    $cost = $this->priceResponse->price;
                }

            }

            $this->add_rate([
                'id' => $this->id.'_'.$code,
                'label' => $title,
                'cost' => $cost,
            ]);
            $x++;
        }

    }

}
