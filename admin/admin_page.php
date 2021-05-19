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

    <p>
        <label><strong>Envío gratuito con mínimo de orden (dejar en 0 para no aplicar)</strong></label><br>
        <input name="adue_woo_ca_conf[min_free_shipping]" type="number" min="0" step="0.01"  value="<?php echo isset($viewData['sentData']['adue_woo_ca_conf']['min_free_shipping']) ? $viewData['sentData']['adue_woo_ca_conf']['min_free_shipping'] : 0; ?>" />
    </p>

    <p>
        <label><strong>Agregar monto adicional al precio de envío (dejar en 0 para no aplicar)</strong></label><br>
        <input name="adue_woo_ca_conf[aditional_fee_amount]" type="number" min="0" step="0.01"  value="<?php echo isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_amount']) ? $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_amount'] : 0; ?>" />
        <select name="adue_woo_ca_conf[aditional_fee_type]">
            <option value="percent" <?php if(isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type']) && $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type'] == 'percent') echo "selected"; ?>>Porcentaje</option>
            <option value="fixed" <?php if(isset($viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type']) && $viewData['sentData']['adue_woo_ca_conf']['aditional_fee_type'] == 'fixed') echo "selected"; ?>>Fijo</option>
        </select>
    </p>

    <p>
        <button type="submit">Guardar</button>
    </p>
</form>