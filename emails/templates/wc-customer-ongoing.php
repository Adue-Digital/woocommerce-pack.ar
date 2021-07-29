<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$emailContent = get_option('adue_woo_ca_conf', [
    'ongoing_email_content' => 'El código de seguimiento de tu pedido es [tracking_code] y podés ver el estado del envío <a href="https://www.correoargentino.com.ar/formularios/e-commerce?id=[tracking_code]" target="_blank">haciendo click acá</a>'
])['ongoing_email_content'];

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email );

$trackingCode = !empty($order->get_meta('_ca_tracking_code')) ? $order->get_meta('_ca_tracking_code') : $_POST['ca_tracking_code'];

?>

    <p><?php echo str_replace('[tracking_code]', $trackingCode, html_entity_decode(stripslashes($emailContent)) ); ?></p>
    <p><?php echo __( 'Aprovechamos para recordarte los detalles de tu compra:', 'woocommerce' ); ?></p>

<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

if ( $additional_content ) {
    echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );