<?php


class AdueShippingMethod extends WC_Shipping_Method
{

    protected $priceResponse;

    public function init()
    {
        // Load the settings API
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, [$this, 'process_admin_options']);
    }

    public function is_available($package)
    {

        if ($this->getTotalWheight($package) > 25)
            return false;

        $response = $this->getShippingPrice($package);

        if(isset($response->success)) {
            if($response->success) {
                $this->priceResponse = $response;
                return true;
            }
        }

        return false;
    }

    public function init_form_fields()
    {
        $this->instance_form_fields = [
            'enabled' => [
                'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
                'type' 			=> 'checkbox',
                'label' 		=> __( 'Habilitar este método de envío', 'woocommerce' ),
                'default' 		=> 'yes'
            ],
            'title' => [
                'title' => __('Method Title', 'woocommerce'),
                'type' => 'text',
                'description' => __('Nombre con el cuál el usuario verá el envío.', 'woocommerce'),
                'default' => __($this->method_title, 'woocommerce')
            ]
        ];
    }

    protected function getTotalWheight($package)
    {
        $weight = 0;

        foreach ( $package['contents'] as $id => $values ) {

            $weight = $this->getWeight($values);
            $volumetricWeight = $this->volumetricWeight($values);

            if ($volumetricWeight > $weight ) { $weight = $volumetricWeight; }

        }

        return $weight;
    }

    protected function getWeight($values)
    {
        if ($values['data']->get_weight())
            return $values['data']->get_weight() * $values['quantity'];

        return 0;
    }

    protected function volumetricWeight($values)
    {
        if ($values['data']->get_length() &&
            $values['data']->get_width() &&
            $values['data']->get_height())
            return ((($values['data']->get_length() / 10) * $values['quantity']) * ($values['data']->get_width() / 10) * ($values['data']->get_height() / 10)) / 6000;

        return 0;
    }

    protected function getShippingPrice($package = [])
    {
        $option = get_option('adue_woo_ca_conf');

        $weight = $this->getTotalWheight($package);
        $cp_from = WC()->countries->get_base_postcode();
        $cp_to = $package['destination']['postcode'];
        $province_from = WC()->countries->get_base_state();
        $province_to = $package['destination']['state'];
        $category = $option['shipping_method_category'];
        $type = $this->shipping_type;

        $data = compact('weight', 'cp_from', 'cp_to', 'province_from', 'province_to', 'category', 'type');

        $http = new Http();

        $response = $http->setIsPost(true)
            ->setPostFields($data)
            ->send();

        return json_decode($response);
    }


}