=== Adue WooCommerce - Correo Argentino ===

Contributors: Marcio Fuentes <marcio@adue.digital>

Tags: woocommerce, correo argentino, pack.ar

Tested up to: 5.7.2

Stable tag: 1.2.25

Requires PHP: 7.2

* Integrá fácilmente a tu tienda WooCommerce los precios de envío de Correo Argentino, tanto a domicilio como a sucursal.

* Dale la posibilidad a los usuarios de que elijan la sucursal a la cuál desean que les envíen su pedido.

* Mantené siempre los precios actualizados sin la necesidad de actualizar tu plugin.

== Description ==

¡Lanzamos la versión 1.2 con muchas funcionalidades nuevas!

* Exportación de órdenes a un archivo .csv listo para el uso de la función "Envío masivo" de Paq.ar.

* Configuración de un monto mínimo de orden para ofrecer envío gratuito.

* Configuración de  un fee (fijo o porcentual) a los precios de envío para cubrir tus gastos.

* Integración de código de seguimiento a la orden y agregado de dos nuevos estados (En camino y En destino). Cuando la orden pasa a "En camino" se le envía un correo automático al cliente que su pedido ya fue enviado.

* Actualizaciones automáticas. No vas a tener que descargar el plugin cada vez que salga alguna nueva versión, ahora vas a poder actualizarlo directamente desde tu panel de WordPress.

Podés ver una <a href="http://woo-ca-demo.adue.digital/" target="_blank">demo del plugin acá.</a>

== Changelog ==

= 1.2.29 =
* Se corrige la versión
* Fix se corrige un parseo de cálculo de pesos

= 1.2.28 =
* Fix se agrega la columna numero_orden(opcional) en la exportación de CSV

= 1.2.27 =
* Fix warning de "Undefined variable $additional_content..." en template de email

= 1.2.26 =
* Fix en cálculo de peso volumétrico

= 1.2.25 =
* Nueva funcionalidad en exportación de csv, podés determinar si querés que las dimensiones de tus envíos se calculen automáticamente o setear las medidas de forma manual.

= 1.2.24 =
* ¡Separación de valor mínimo de la orden para los distintos métodos de envío!

= 1.2.23 =
* Cambio de nombre de función save_data a adue_save_data para evitar conflictos

= 1.2.21 =
* Fix en incompatibilidad de envío de correo para estado "En camino"
* Fix en suma de pesos para descarga de .csv

= 1.2.19 =
* Posibilidad de separar los campos de dirección para una mejor organización

= 1.2.18 =
* Corrección en exportación de datos en .csv

= 1.2.17 =
* Corrección en exportación de datos en .csv

= 1.2.15 =
* Hotfix de cálculo de dimensiones y conversiones de unidades

= 1.2.14 =
* Procesamiento de dirección en la exportación de órdenes
* Mejoras en el cálculo de dimensiones y conversiones de unidades

= 1.2.13 =
* Hotfix de checkout

= 1.2.12 =
* Hotfix de exportación de csv (vocales con tilde)

= 1.2.11 =
* Hotfix de exportación de csv

= 1.2.10 =
* Corrección en cálculo de peso volumétrico

= 1.2.9 =
* Corrección en exportación de órdenes con productos inexistentes

= 1.2.8 =
* Corrección en exportación de órdenes con productos variables
* Corrección de desaparición de órdenes cuando cambian a estados "En camino" y "En destino"

= 1.2.7 =
* Corrección de codificación de HTML en correo "En camino"
* Eliminación de caracteres especiales en todas las cadenas de exportación de .csv
* Corte de número de teléfono a 10 dígitos

= 1.2 =
* Exportación de órdenes a archivo .csv para el uso de la función "Envío masivo".
* Configuración de monto mínimo de orden para ofrecer envío gratuito.
* Configuración de fee (fijo o porcentual) a los precios de envío.
* Integración de código de seguimiento a la orden.
* Agregado de los estados "En camino" y "En destino".
* Notificación a cliente cuando la orden pasa a estado "En camino".
* Actualizaciones automáticas y manuales a través del panel de WordPress.
* Mejoras en la API para el cálculo de precios.
* Correcciones menores de funcionamiento y seguridad.

= 1.0 =
* Primera versión del plugin.