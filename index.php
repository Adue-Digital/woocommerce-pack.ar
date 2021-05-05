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

        $viewData['statuses'] = wc_get_order_statuses();

        if(isset($_POST['guardar']) && $_POST['guardar']) {
            $viewData['sentData'] = $_POST;
            $viewData['response'] = save_data($_POST);
        }

        if(isset($_POST['exportar']) && $_POST['exportar']) {
            $data = $_POST['export_data'];
            export($data);
        }

        require_once __DIR__.'/admin/admin_page.php';
    }
    add_action('admin_menu', 'register_admin_submenu_page');


    function save_branch_office_code( $order, $data ) {
        $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
        $chosen_shipping = $chosen_methods[0];
        $shippingMethods = $order->get_shipping_methods();
        $shippingMethodId = @array_shift($shippingMethods)['method_id'];
        if($shippingMethodId == 'adue_correo_argentino_sucursal')
            $order->update_meta_data( 'branch_office_code', str_replace('adue_correo_argentino_sucursal_', '', $chosen_shipping ));
    }
    add_action('woocommerce_checkout_create_order', 'save_branch_office_code', 20, 2);

    function save_data($data)
    {
        if(!isset($data['adue_woo_ca_conf']['adue_api_key'])) {
            return [
                'success' => false,
                'message' => 'La API Key es obligatoria'
            ];
        }

        if(!isset($data['adue_woo_ca_conf']['activation_id'])) {
            return [
                'success' => false,
                'message' => 'El ID de activación es obligatorio'
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

    function export($data) {

        $headers = [
            "tipo_producto(obligatorio)",
            "largo(obligatorio en CM)",
            "ancho(obligatorio en CM)",
            "altura(obligatorio en CM)",
            "peso(obligatorio en KG)",
            "valor_del_contenido(obligatorio en pesos argentinos)",
            "provincia_destino(obligatorio)",
            "sucursal_destino(obligatorio solo en caso de no ingresar localidad de destino)",
            "localidad_destino(obligatorio solo en caso de no ingresar sucursal de destino)",
            "calle_destino(obligatorio solo en caso de no ingresar sucursal de destino)",
            "altura_destino(obligatorio solo en caso de no ingresar sucursal de destino)",
            "piso(opcional solo en caso de no ingresar sucursal de destino)",
            "dpto(opcional solo en caso de no ingresar sucursal de destino)",
            "codpostal_destino(obligatorio solo en caso de no ingresar sucursal de destino)",
            "destino_nombre(obligatorio)",
            "destino_email(obligatorio, debe ser un email valido)",
            "cod_area_tel(opcional)",
            "tel(opcional)",
            "cod_area_cel(obligatorio)",
            "cel(obligatorio)"
        ];


        $orders = wc_get_orders([
            'status' => ['wc-completed'],
            'limit' => -1,
            'date_created' => $data['date_from'].'...'.$data['date_to']
        ]);


        if(count($orders)) {

            $shippingRecords = [];

            foreach ($orders as $order) {

                $shippingMethods = $order->get_shipping_methods();
                $shippingMethodId = @array_shift($shippingMethods)['method_id'];

                if (in_array($shippingMethodId, ['adue_correo_argentino_sucursal', 'adue_correo_argentino_domicilio'])) {

                    preg_match_all('!\d+!', $order->get_shipping_address_1(), $numbers);
                    $houseNumber = $numbers[count($numbers) - 1];
                    $streetName = trim(str_replace($houseNumber, '', $order->get_shipping_address_1()));

                    preg_match_all('!\d+!', $order->get_billing_phone(), $phones);
                    $phone = implode('', $phones[0]);

                    $shippingRecord = [
                        'tipo_producto' => 'CP',
                        'largo' => 83,
                        'ancho' => 83,
                        'altura' => 83,
                        'peso' => 0,
                        'valor_del_contenido' => 0,
                        'provincia_destino' => normalizeString($order->get_shipping_state()),
                        'sucursal_destino' => '',
                        'localidad_destino' => normalizeString($order->get_shipping_city()),
                        'calle_destino' => normalizeString($streetName),
                        'altura_destino' => $houseNumber[0],
                        'piso' => normalizeString($order->get_shipping_address_2()),
                        'dpto' => '',
                        'codpostal_destino' => $order->get_shipping_postcode(),
                        'destino_nombre' => normalizeString($order->get_formatted_shipping_full_name()),
                        'destino_email' => normalizeString($order->get_billing_email()),
                        'cod_area_tel' => '54',
                        'tel' => $phone,
                        'cod_area_cel' => '549',
                        'cel' => $phone,
                    ];

                    foreach ($order->get_items() as $productData) {
                        $product = wc_get_product($productData['product_id']);
                        $shippingRecord['peso'] += (float)$product->get_weight();
                        $shippingRecord['valor_del_contenido'] += (float)$product->get_price();
                    }

                    if ($shippingMethodId == 'adue_correo_argentino_sucursal') {
                        $branchOfficeCode = $order->get_meta('branch_office_code');
                        $shippingRecord['sucursal_destino'] = $branchOfficeCode ? $branchOfficeCode : 'IBL'; // TODO change for API Call
                    }


                    $shippingRecords[] = $shippingRecord;

                }

            }

            if (!count($shippingRecords)) {

                $errorMessage = 'No se han encontrado órdenes con los filtros utilizados';
                require_once __DIR__ . '/admin/admin_page.php';
                die();

            }

            $fileName = 'export-' . date('YmdHis') . '.csv';
            $filePath = __DIR__ . '/tmp/' . $fileName;

            $fp = fopen($filePath, 'w+');

            fputcsv($fp, $headers, ';');

            foreach ($shippingRecords as $shippingRecord) {
                fputcsv($fp, $shippingRecord, ';', ' ');
            }
            fclose($fp);

            header('Location: ' . plugins_url('/tmp/' . $fileName, __FILE__));

            die();

        }

        $errorMessage = 'No se han encontrado órdenes con los filtros utilizados';
        require_once __DIR__ . '/admin/admin_page.php';

        die();
    }

    function normalizeString($string) {
        $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
            'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
            'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r', ';' => ','
        );

        return trim(strtr($string, $table));
    }
}
