<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$emailContent = get_option('adue_woo_ca_conf', [
    'ongoing_email_content' => 'El código de seguimiento de tu pedido es [tracking_code] y podés ver el estado del envío <a href="https://www.correoargentino.com.ar/formularios/e-commerce?id=[tracking_code]" target="_blank">haciendo click acá</a>'
])['ongoing_email_content'];

echo "= " . $email_heading . " =\n\n";

$trackingCode = !empty($order->get_meta('_ca_tracking_code')) ? $order->get_meta('_ca_tracking_code') : $_POST['ca_tracking_code'];

echo strip_tags(str_replace('[tracking_code]', $trackingCode, $emailContent ));

echo __( 'Te recordamos los detalles de tu orden:', 'woocommerce' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( isset($additional_content) && $additional_content ) {
    echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
    echo "\n\n----------------------------------------\n\n";
}

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );