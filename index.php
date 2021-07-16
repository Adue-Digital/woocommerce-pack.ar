<?php
/**
* Plugin Name: Adue WooCommerce - Correo Argentino
* Plugin URI: https://adue.digital
* Description: Integración de precios de envío de Correo Argentino con Woocommerce
* Version: 1.2.13
* Author: Adue
* Author URI: https://adue.digital
* WC tested up to: 4.5.2
* Text Domain: adue-woo-correo-argentino
* Domain Path: /languages/
*
* @author adue.digital
* @package Adue - Correo Argentino
* @version 1.2.13
*/

if ( ! defined( 'ABSPATH' ) )  exit;

define('PLUGIN_BASE_URL', plugin_dir_url(__FILE__));
define('PLUGIN_VERSION', '1.2.13');
define('API_URL', 'https://woo-ca-api.adue.digital/');

$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

if ( in_array( 'woocommerce/woocommerce.php',  $active_plugins) ) {

    function check_updates() {
        require 'plugin-update-checker/plugin-update-checker.php';
        $myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
            'https://github.com/Adue-Digital/woocommerce-pack.ar',
            __FILE__,
            'adue-correo-argentino-update-checker'
        );
        $myUpdateChecker->setBranch('main');
        $myUpdateChecker->getVcsApi()->enableReleaseAssets();
    }
    add_action('plugins_loaded', 'check_updates');

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

    /** Adding shipping code */
    function ca_add_meta_boxes() {
        add_meta_box( 'ca_tracking_code',
            __('Código de seguimiento Correo Argentino', 'woocommerce'),
            'ca_add_tracking_code_to_order',
            'shop_order',
            'side',
            'core'
        );
    }
    add_action( 'add_meta_boxes', 'ca_add_meta_boxes' );

    function ca_add_tracking_code_to_order() {
        global $post;

        $meta_field_data = get_post_meta( $post->ID, '_ca_tracking_code', true ) ? get_post_meta( $post->ID, '_ca_tracking_code', true ) : '';

        echo '<input type="hidden" name="ca_tracking_code_field_nonce" value="' . wp_create_nonce() . '">
        <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
        <input type="text" style="width:250px;" name="ca_tracking_code" placeholder="' . $meta_field_data . '" value="' . $meta_field_data . '"></p>';

    }
    function ca_save_wc_order_tracking_code( $post_id ) {

        // We need to verify this with the proper authorization (security stuff).

        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'ca_tracking_code_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'ca_tracking_code_field_nonce' ];

        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }

        update_post_meta( $post_id, '_ca_tracking_code', $_POST[ 'ca_tracking_code' ] );
    }
    add_action( 'save_post', 'ca_save_wc_order_tracking_code', 10, 1 );
    /** End adding shipping code */

    /** Adding Ongoing and Delivered status to order */
    function register_custom_statuses() {
        register_post_status( 'wc-ca-ongoing', array(
            'label'                     => _x( 'En camino', 'Order status', 'woocommerce' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'En camino <span class="count">(%s)</span>', 'En camino <span class="count">(%s)</span>', 'woocommerce' )
        ) );

        register_post_status( 'wc-ca-delivered', array(
            'label'                     => _x( 'En destino', 'Order status', 'woocommerce' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'En destino <span class="count">(%s)</span>', 'En destino <span class="count">(%s)</span>', 'woocommerce' )
        ) );
    }
    add_action( 'init', 'register_custom_statuses' );

    function add_ongoing_to_order_statuses( $order_statuses ) {
        $new_order_statuses = array();
        // add new order status after processing
        foreach ( $order_statuses as $key => $status ) {
            $new_order_statuses[ $key ] = $status;
            if ( 'wc-completed' === $key ) {
                $new_order_statuses['wc-ca-ongoing'] = 'En camino';
                $new_order_statuses['wc-ca-delivered'] = 'En destino';
            }
        }
        return $new_order_statuses;
    }
    add_filter( 'wc_order_statuses', 'add_ongoing_to_order_statuses' );
    /** End adding Ongoing and Delivered status to order */

    /** Register Ongoing email */
    function register_ca_ongoing_email( $emails ) {
        require_once 'emails/class-wc-ongoing.php';
        $emails['WC_Ongoing'] = new WC_Ongoing();
        return $emails;
    }
    add_filter( 'woocommerce_email_classes', 'register_ca_ongoing_email', 90, 1 );
    /** End register Ongoing email */

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

        if(isset($_GET['action']) && $_GET['action'] == 'delete_exported_file') {
            if(isset($_GET['file_name'])) {
                deleteExportedFile($_GET['file_name']);
            }
        }

        $config = get_option('adue_woo_ca_conf');
        if(!get_option('adue_woo_ca_conf')) {
            $viewData['sentData']['adue_woo_ca_conf'] = [
                'adue_api_key' => '',
                'shipping_method_category' => '',
                'min_free_shipping' => 0,
                'aditional_fee_amount' => 0,
                'ongoing_email_content' => 'El código de seguimiento de tu pedido es [tracking_code] y podés ver el estado del envío <a href="https://www.correoargentino.com.ar/formularios/e-commerce?id=[tracking_code]" target="_blank">haciendo click acá</a>'
            ];
        } else {
            $viewData['sentData']['adue_woo_ca_conf'] = get_option('adue_woo_ca_conf');

            if(!isset($viewData['sentData']['adue_woo_ca_conf']['adue_api_key']))
                $viewData['sentData']['adue_woo_ca_conf']['adue_api_key'] = '';

            if(!isset($viewData['sentData']['adue_woo_ca_conf']['shipping_method_category']))
                $viewData['sentData']['adue_woo_ca_conf']['shipping_method_category'] = '';

            if(!isset($viewData['sentData']['adue_woo_ca_conf']['min_free_shipping']))
                $viewData['sentData']['adue_woo_ca_conf']['min_free_shipping'] = 0;

            if(!isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_amount']))
                $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_amount'] = 0;

            if(!isset($viewData['sentData']['adue_woo_ca_conf']['ongoing_email_content']))
                $viewData['sentData']['adue_woo_ca_conf']['ongoing_email_content'] = 'El código de seguimiento de tu pedido es [tracking_code] y podés ver el estado del envío <a href="https://www.correoargentino.com.ar/formularios/e-commerce?id=[tracking_code]" target="_blank">haciendo click acá</a>';
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

        ?>
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

            <h2>Adue - Correo Argentino</h2>
            <?php settings_errors(); ?>

            <?php $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'config'; ?>

            <?php
                if ($active_tab == 'export') {
                    $files = getExportFiles();

                    if(isset($_GET['not-included'])) {
                        $errorMessage = 'Las siguientes órdenes no se han podido exportar ya que no se encontró la sucursal correspondiente, sin embargo vas a poder descargar el archivo en la lista de abajo<br>';
                        $errorMessage .= '<ul>';
                        foreach (explode('-', $_GET['not-included']) as $orderNumber) {
                            $errorMessage .= '<li>Orden#'.$orderNumber.'</li>';
                        }
                        $errorMessage .= '</ul>';
                    }
                }
            ?>

            <h2 class="nav-tab-wrapper">
                <a href="?page=adue-correo-argentino&tab=config" class="nav-tab <?php echo $active_tab == 'config' ? 'nav-tab-active' : ''; ?>">Configuración</a>
                <a href="?page=adue-correo-argentino&tab=export" class="nav-tab <?php echo $active_tab == 'export' ? 'nav-tab-active' : ''; ?>">Exportar órdenes</a>
            </h2>

            <?php
                if( $active_tab == 'config' ) {
                    require_once __DIR__.'/admin/admin_page.php';
                } else {
                    require_once __DIR__.'/admin/export.php';
                } // end if/else
            ?>

        </div><!-- /.wrap -->
        <?php

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

        $data['adue_woo_ca_conf']['ongoing_email_content'] = htmlentities(stripslashes($data['adue_woo_ca_conf']['ongoing_email_content']));

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

    function getExportFiles() {
        $dir = __DIR__ . '/tmp';
        $files = array();
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != "index.php") {
                    $files[filemtime($dir . '/' .$file)] = $file;
                }
            }
            closedir($handle);

            // sort
            rsort($files);

            return $files;
        }
    }

    function deleteExportedFile($file) {
        $dir = __DIR__ . '/tmp/';
        if(file_exists($dir . $file))
            unlink($dir . $file);
    }

    function export($data) {
        $dir = __DIR__ . '/tmp';
        $files = getExportFiles();
        if(count($files) >= 10) {
            for ($x = 10; $x < count($files) ; $x++) {
                unlink($dir.'/'.$files[$x]);
            }
        }

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
            'status' => ['wc-completed', 'wc-processing'],
            'limit' => -1,
            'date_created' => $data['date_from'].'...'.$data['date_to']
        ]);

        if(count($orders)) {

            $shippingRecords = [];
            $notAddedShippingRecords = [];

            foreach ($orders as $order) {

                $addShippingRecord = true;

                $shippingMethods = $order->get_shipping_methods();
                $shippingMethod = @array_shift($shippingMethods);
                $shippingMethodId = $shippingMethod['method_id'];

                if (in_array($shippingMethodId, ['adue_correo_argentino_sucursal', 'adue_correo_argentino_domicilio'])) {

                    if($_POST['export_data']['process_address']) {
                        preg_match_all('!\d+!', $order->get_shipping_address_1(), $numbers);
                        $houseNumber = $numbers[count($numbers) - 1];
                        $streetName = trim(str_replace($houseNumber, '', $order->get_shipping_address_1()));
                    } else {
                        $streetName = $order->get_shipping_address_1();
                        $houseNumber[0] = 0;
                    }

                    preg_match_all('!\d+!', $order->get_billing_phone(), $phones);
                    $phone = implode('', $phones[0]);
                    $phone = ltrim($phone, '549');
                    $phone = ltrim($phone, '54');

                    if(strlen($phone) > 10) {
                        $phone = substr($phone, -10);
                    }

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
                        'destino_email' => $order->get_billing_email(),
                        'cod_area_tel' => '54',
                        'tel' => $phone,
                        'cod_area_cel' => '549',
                        'cel' => $phone,
                    ];

                    foreach ($order->get_items() as $productData) {
                        $productId = isset($productData['variation_id']) && $productData['variation_id'] ? $productData['variation_id'] : $productData['product_id'];
                        $product = wc_get_product($productId);
                        if(!$product)
                            continue;
                        $shippingRecord['peso'] += (float) $product->get_weight();
                        $shippingRecord['valor_del_contenido'] += (float)$product->get_price();
                    }

                    if ($shippingMethodId == 'adue_correo_argentino_sucursal') {
                        $branchOfficeCode = $order->get_meta('branch_office_code') ?
                            $order->get_meta('branch_office_code') : getBranchOfficeCode($order->get_shipping_postcode(), $shippingMethod);
                        if(!$branchOfficeCode) {
                            $addShippingRecord = false;
                        }
                        $shippingRecord['sucursal_destino'] = $branchOfficeCode;
                    }

                    if($addShippingRecord) {
                        $shippingRecords[] = $shippingRecord;
                    } else {
                        $notAddedShippingRecords[] = $order->get_order_number();
                    }

                }

            }

            if (count($shippingRecords)) {
                $fileName = 'export-' . date('YmdHis') . '.csv';
                $filePath = __DIR__ . '/tmp/' . $fileName;

                $fp = fopen($filePath, 'w+');

                fputcsv($fp, $headers, ';');

                foreach ($shippingRecords as $shippingRecord) {
                    fputcsv($fp, $shippingRecord, ';', ' ');
                }
                fclose($fp);
            }

            if (count($notAddedShippingRecords)) {
                header('Location: ' . site_url() . '/wp-admin/admin.php?page=adue-correo-argentino&tab=export&not-included=' . implode('-', $notAddedShippingRecords));
                die();
            }

            if (!count($shippingRecords)) {
                header('Location: ' . site_url() . '/wp-admin/admin.php?page=adue-correo-argentino&tab=export&not-founded=true');
                die();
            }

            header('Location: ' . PLUGIN_BASE_URL . 'tmp/' . $fileName);

            die();

        }

        header('Location: ' . site_url() . '/wp-admin/admin.php?page=adue-correo-argentino&tab=export&not-founded=true');
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

        $string =  trim(strtr($string, $table));

        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        $string = str_replace('-', ' ', $string);

        return $string;
    }

    function getBranchOfficeCode($postCode, $shippingMethod) {

        $address = str_replace('Correo Argentino a sucursal. ', '', $shippingMethod['name']);
        $address = explode(',', $address)[0];

        include_once 'inc/Http.php';

        $http = new Http();
        $http->setUrl(API_URL.'/branch_office_code');
        $response = $http
            ->setIsPost(false)
            ->setPostFields([
                'postal_code' => $postCode,
                'address' => $address
            ])
            ->send();

        return isset(json_decode($response)->branch_office_code) ? json_decode($response)->branch_office_code : false;
    }
}
