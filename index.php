<?php
/**
* Plugin Name: Adue WooCommerce - Correo Argentino
* Plugin URI: https://adue.digital
* Description: Integración de precios de envío de Correo Argentino con Woocommerce
* Version: 1.0.0
* Author: Adue
* Author URI: https://adue.digital
* WC tested up to: 4.5.2
* Text Domain: adue-woo-correo-argentino
* Domain Path: /languages/
*
* @author adue.digital
* @package Adue - Correo Argentino
* @version 1.0
*/

if ( ! defined( 'ABSPATH' ) )  exit;

$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

if ( in_array( 'woocommerce/woocommerce.php',  $active_plugins) ) {

    function adue_shipping_methods( $methods )
    {

        $methods['adue_correo_argentino_sucursal'] = 'WC_Adue_Correo_Argentino_Sucursal';
        $methods['adue_correo_argentino_domicilio'] = 'WC_Adue_Correo_Argentino_Domicilio';

        return $methods;
    }
    add_filter( 'woocommerce_shipping_methods', 'adue_shipping_methods' );

    function adue_shipping_methods_init()
    {

        require_once plugin_dir_path(__FILE__) . 'shipping_methods/class-adue-correo-argentino-sucursal.php';
        require_once plugin_dir_path(__FILE__) . 'shipping_methods/class-adue-correo-argentino-domicilio.php';

    }
    add_action( 'woocommerce_shipping_init', 'adue_shipping_methods_init' );


    function register_admin_submenu_page()
    {
        add_submenu_page( 'woocommerce',
            'Adue - Correo Argentino',
            'Adue - Correo Argentino',
            'manage_options', 'adue-correo-argentino',
            'admin_submenu_page',
            9999 );
    }
    function admin_submenu_page()
    {
        $viewData = [];

        if(!get_option('adue_woo_ca_conf')) {
            $viewData['sentData']['adue_woo_ca_conf'] = [
                'adue_api_key' => '',
                'shipping_method_category' => ''
            ];
        } else {
            $viewData['sentData']['adue_woo_ca_conf'] = get_option('adue_woo_ca_conf');
        }

        if(isset($_POST['guardar']) && $_POST['guardar']) {
            $viewData['sentData'] = $_POST;
            $viewData['response'] = save_data($_POST);
        }

        require_once __DIR__.'/admin/admin_page.php';
    }
    add_action('admin_menu', 'register_admin_submenu_page');

    function save_data($data)
    {
        if(!isset($data['adue_woo_ca_conf']['adue_api_key'])) {
            return [
                'success' => false,
                'message' => 'La API Key es obligatoria'
            ];
        }

        if(get_option('adue_woo_ca_conf')) {
            $res = update_option('adue_woo_ca_conf', $data['adue_woo_ca_conf'], true);
        } else {
            $res = add_option('adue_woo_ca_conf', $data['adue_woo_ca_conf'], '', true);
        }

        if($res)
            return [
                'success' => true,
                'message' => 'Datos guardados correctamente'
            ];

        return [
            'success' => false,
            'message' => 'Hubo un error al cargar la información'
        ];
    }

}
