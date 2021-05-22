<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email' ) ) {
    return;
}

/**
 * Class WC_Customer_Cancel_Order
 */
class WC_Ongoing extends WC_Email {

    /**
     * Create an instance of the class.
     *
     * @access public
     * @return void
     */
    function __construct() {
        // Email slug we can use to filter other data.
        $this->id          = 'wc_ca_ongoing';
        $this->title       = __( '¡Tu pedido está en camino!', 'adue-woo-ca' );
        $this->description = __( 'Notificación que se le envía al cliente cuando se envía su pedido', 'adue-woo-ca' );
        // For admin area to let the user know we are sending this email to customers.
        $this->customer_email = true;
        $this->heading     = __( '¡Tu pedido está en camino!', 'adue-woo-ca' );

        $this->subject     = __( '¡Tu pedido está en camino!', 'adue-woo-ca' );

        // Template paths.
        $this->template_html  = 'wc-customer-ongoing.php';
        $this->template_plain = 'plain/wc-customer-ongoing.php';
        $this->template_base  = __DIR__ . '/templates/';

        // Action to which we hook onto to send the email.
        add_action( 'woocommerce_order_status_ca-ongoing', array( $this, 'trigger' ) );

        parent::__construct();
    }

    public function trigger( $order_id ) {

        $this->object = wc_get_order( $order_id );

        if ( version_compare( '3.0.0', WC()->version, '>' ) ) {
            $order_email = $this->object->billing_email;
        } else {
            $order_email = $this->object->get_billing_email();
        }

        $this->recipient = $order_email;

        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }
        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }

    public function get_content_html() {
        return wc_get_template_html( $this->template_html, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'			=> $this
        ), '', $this->template_base );
    }

    public function get_content_plain() {
        return wc_get_template_html( $this->template_plain, array(
            'order'         => $this->object,
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => true,
            'email'			=> $this
        ), '', $this->template_base );
    }
}