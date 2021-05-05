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

<hr>

<h3>Exportación de órdenes</h3>

<?php if(isset($errorMessage)) : ?>
    <div class="alert alert-danger">
        <?php echo $errorMessage; ?>
    </div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="exportar" value="1" />
    <p>
        <label><strong>Fecha desde</strong></label>
        <input type="date" name="export_data[date_from]" value="" />
    </p>
    <p>
        <label><strong>Fecha hasta</strong></label>
        <input type="date" name="export_data[date_to]" value="" />
    </p>
    <p>
        <button type="submit">Exportar</button>
    </p>
</form>