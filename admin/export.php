<h3>Exportación de órdenes</h3>

<?php if(isset($errorMessage)) : ?>
    <div class="alert alert-danger">
        <?php echo $errorMessage; ?>
    </div>
<?php endif; ?>

<p>¡ATENCIÓN! te recomendamos que no realices exportaciones con un rango de fechas muy grande ya que podría afectar en el funcionamiento del servidor.</p>

<form method="post" action="/wp-admin/admin.php?page=adue-correo-argentino&tab=export">
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

<hr>

<h3>Últimas exportaciones</h3>

<p>Revisá tus exportaciones anteriores para ni cargar dos veces el mismo envío</p>

<?php foreach ($files as $file) : ?>
    <p>
        Exportación fecha
        <?php
            $fileName = str_replace('export-', '', $file);
            $fileName = str_replace('.csv', '', $fileName);
            echo substr($fileName, 6, 2) . '/' .
                substr($fileName, 4, 2) . '/' .
                substr($fileName, 0, 4) . ' - ' .
                substr($fileName, 8, 2) . ':' .
                substr($fileName, 10, 2) . ':' .
                substr($fileName, 12);
        ?>
        <a href="<?php echo PLUGIN_BASE_URL . 'tmp/' . $file; ?>">Descargar</a>
        <a href="/wp-admin/admin.php?page=adue-correo-argentino&tab=export&action=delete_exported_file&file_name=<?php echo $file; ?>" style="color: red;">Eliminar</a>
    </p>
<?php endforeach; ?>
