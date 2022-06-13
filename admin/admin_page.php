<h3>Configuración de plugin para envíos con Correo Argentino</h3>

<?php if(isset($viewData['response']['success'])) : ?>
    <div class="alert <?php echo $viewData['response']['success'] ? 'updated' : 'error'; ?>">
        <?php echo $viewData['response']['message']; ?>
    </div>
<?php endif; ?>

<div>
    Si no sabés cómo conseguir tus credenciales, podés seguir <a href="https://www.adue.digital/como-integro-mi-plugin-de-correo-argentino-a-mi-tienda-de-woocommerce/" target="_blank">el tutorial en nuestro blog</a>
</div>

<form method="post">
    <input type="hidden" name="guardar" value="1" />
    <p>
        <label><strong>Adue API Key</strong></label>
        <input type="text" name="adue_woo_ca_conf[adue_api_key]" value="<?php echo $viewData['sentData']['adue_woo_ca_conf']['adue_api_key']; ?>"/>
    </p>
    <p>
        <label><strong>Activation ID</strong></label>
        <input type="text" name="adue_woo_ca_conf[activation_id]" value="<?php echo $viewData['sentData']['adue_woo_ca_conf']['activation_id']; ?>"/>
    </p>
    <p>
        <label><strong>Tipo de envío</strong></label>
        <select name="adue_woo_ca_conf[shipping_method_category]" autocomplete="off">
            <option value="monotributista-consumidor-final" <?php if($viewData['sentData']['adue_woo_ca_conf']['shipping_method_category'] == "monotributista-consumidor-final") echo "selected"; ?>>Monotributista / Consumidor final</option>
            <option value="responsable-inscripto" <?php if($viewData['sentData']['adue_woo_ca_conf']['shipping_method_category'] == "responsable-inscripto") echo "selected"; ?>>Responsable inscripto</option>
        </select>
    </p>

    <hr>

    <h3>Texto de email de estado "En camino"</h3>

    <p>
        Ingresá en el siguiente cuadro el contenido del email que se enviará cuando la orden pase a estado "En camino". Usá la etiqueta [tracking_code] para determinar el lugar en donde se va a mostrar el código de seguimiento.<br>
        Si necesitás configurar alguna opción más (como el título o el asunto del correo), lo podés hacer directamente desde <a href="<?php echo get_site_url(); ?>/wp-admin/admin.php?page=wc-settings&tab=email&section=wc_ongoing" target="_blank">la opción de WooCommerce</a>.
        <?php
            wp_editor( html_entity_decode(stripslashes($viewData['sentData']['adue_woo_ca_conf']['ongoing_email_content'])) , 'ongoing_email_content', array(
                'wpautop'       => true,
                'media_buttons' => false,
                'textarea_name' => 'adue_woo_ca_conf[ongoing_email_content]',
                'editor_class'  => 'my_custom_class',
                'textarea_rows' => 10
            ) );
        ?>
    </p>

    <hr>

    <h3>Ajustes adicionales</h3>

    <p>
        <label><strong>Envío gratuito con mínimo de orden (dejar en 0 para no aplicar)</strong></label><br>
        Establecer un monto mínimo de orden desde el cual se ofrece el envío de forma gratuita.<br>
        $ <input name="adue_woo_ca_conf[min_free_shipping_sucursal]" type="number" min="0" step="0.01"  value="<?php echo isset($viewData['sentData']['adue_woo_ca_conf']['min_free_shipping_sucursal']) ? $viewData['sentData']['adue_woo_ca_conf']['min_free_shipping_sucursal'] : 0; ?>" /> a sucursal<br><br>
        $ <input name="adue_woo_ca_conf[min_free_shipping_domicilio]" type="number" min="0" step="0.01"  value="<?php echo isset($viewData['sentData']['adue_woo_ca_conf']['min_free_shipping_domicilio']) ? $viewData['sentData']['adue_woo_ca_conf']['min_free_shipping_domicilio'] : 0; ?>" /> a domicilio
    </p>

    <p>
        <label><strong>Agregar monto adicional al precio de envío (dejar en 0 para no aplicar)</strong></label><br>
        Establecer un cargo adicional al precio del envío.<br>
        <input name="adue_woo_ca_conf[aditional_fee_amount]" type="number" min="0" step="0.01"  value="<?php echo isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_amount']) ? $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_amount'] : 0; ?>" />
        <select name="adue_woo_ca_conf[aditional_fee_type]">
            <option value="percent" <?php if(isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type']) && $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type'] == 'percent') echo "selected"; ?>>Porcentaje</option>
            <option value="fixed" <?php if(isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type']) && $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type'] == 'fixed') echo "selected"; ?>>Fijo</option>
        </select>
    </p>

    <p>
        <label><input name="adue_woo_ca_conf[separate_address_fields]" type="checkbox" <?php echo isset($viewData['sentData']['adue_woo_ca_conf']['separate_address_fields']) && $viewData['sentData']['adue_woo_ca_conf']['separate_address_fields'] ? 'checked' : ''; ?> value="1" /> <strong>Separar campos de dirección en dos campos distintos</strong></label><br>
        Los campos de facturación y envío "Dirección uno" y "Dirección dos", serán modificados para que el cliente pueda agregar nombre de la calle, número de la casa, piso y departamento, con el objetivo de realizar la exportación de manera más sencilla.<br>
        El plugin intentará guardar los valores en los campos originales de WooCommerce para no afectar el funcionamiento de otros plugins, pero no garantiza que así sea.

    </p>

    <p>
        <button type="submit">Guardar</button>
    </p>
</form>