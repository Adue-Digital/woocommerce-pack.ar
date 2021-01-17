<h3>Configuración de plugin para envíos con Correo Argentino</h3>

<?php if(isset($viewData['response']['success'])) : ?>
    <div class="alert <?php echo $viewData['response']['success'] ? 'updated' : 'error'; ?>">
        <?php echo $viewData['response']['message']; ?>
    </div>
<?php endif; ?>

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
    <p>
        <button type="submit">Guardar</button>
    </p>
</form>