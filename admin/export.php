<h3>Exportación de órdenes</h3>

<p>
    ¡ATENCIÓN! te recomendamos que no realices exportaciones con un rango de fechas muy grande ya que podría afectar en el funcionamiento del servidor.<br>
    Si bien, hicimos todo lo posible para mantener la compatibilidad entre las versiones del plugin, es posible que algunos envíos a sucursal no puedan ser cargados por no encontrar la sucursal correspondiente. Si es así, vas a poder ver cuáles son acá abajo.
</p>

<?php if(isset($errorMessage)) : ?>
    <div class="error notice">
        <?php echo $errorMessage; ?>
    </div>
<?php endif; ?>

<form method="post" action="<?php echo site_url(); ?>/wp-admin/admin.php?page=adue-correo-argentino&tab=export">
    <input type="hidden" name="exportar" value="1" />
    <p>
        <label>
            <input type="checkbox" name="export_data[process_address]" value="1" checked />
            Dividir el campo dirección en dos columnas
        </label><br>
        <small>Al seleccionar este campo el plugin intentará tomar el campo de dirección de la orden y dividirlo en dos partes para agregarlos a la columna de "Nombre de calle" y "Número de casa". Esto puede generar problemas con las calles cuyo nombre es un número. Si se deselecciona esta casilla, la dirección irá completamente al campo "Nombre de calle" y "Número de calle" quedará vacío.</small>
    </p>
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

<hr>

<h3>Últimas exportaciones</h3>

<p>Revisá tus exportaciones anteriores para no cargar dos veces el mismo envío</p>

<?php foreach ($files as $file) : ?>
    <p>
        <?php
            $extension = substr(strrchr($file, "."), 1);

            if($extension != 'csv')
                continue;

            $fileName = @str_replace('export-', '', $file);
            $fileName = @str_replace('.csv', '', $fileName);
            echo 'Exportación fecha ' .
                @substr($fileName, 6, 2) . '/' .
                @substr($fileName, 4, 2) . '/' .
                @substr($fileName, 0, 4) . ' - ' .
                @substr($fileName, 8, 2) . ':' .
                @substr($fileName, 10, 2) . ':' .
                @substr($fileName, 12);
        ?>
        <a href="<?php echo PLUGIN_BASE_URL . 'tmp/' . $file; ?>">Descargar</a>
        <a href="/wp-admin/admin.php?page=adue-correo-argentino&tab=export&action=delete_exported_file&file_name=<?php echo $file; ?>" style="color: red;">Eliminar</a>
    </p>
<?php endforeach; ?>
