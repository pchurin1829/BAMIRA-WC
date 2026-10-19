# BAMIRA — Presentación inicial WooCommerce

## Entrega

Tema clásico independiente `wordpress/themes/bamira` y plugin separado
`wordpress/plugins/bamira-catalog-mode`. No dependen de constructores de páginas.
Logo original y paleta incluidos; muestras del color de prendas orientativas, no fotografías certificadas.
Paleta digital aproximada extraída visualmente: bordó #6b142b, frambuesa #b34d71,
rosa #f2b7c0, arena #eee2d9, crema #f7f0e6. No se inventan composición, cuidados ni equivalencias de talles.

Portada con marca, dos colecciones, ocho tarjetas procedentes de WooCommerce y tamaños adaptables.
Borradores visibles SOLO para usuarios con `manage_woocommerce`; visitantes ven solo publicados.
Mantener el modo «Próximamente» de WooCommerce mientras se revisa.
No se incluye Contacto hasta recibir un canal comercial confirmado.
El plugin bloquea compras y muestra «Próximamente», también en fichas de productos.
No elimina el carrito ni pedidos existentes. Para habilitar ventas, desactivarlo expresamente después de validar los datos.
Los borradores no son visibles en la tienda pública: no publicar automáticamente por instalar el tema.
Las fichas y archivos usan las plantillas de WooCommerce, con la paleta del tema.

## Instalación local (PowerShell, Docker iniciado)

1. Respaldar base y archivos si ya existe WordPress. Guardar el nombre del tema activo y opciones.
2. Copiar sin activar:

```powershell
docker cp .\wordpress\themes\bamira bamira_wc_wordpress:/var/www/html/wp-content/themes/
docker cp .\wordpress\plugins\bamira-catalog-mode bamira_wc_wordpress:/var/www/html/wp-content/plugins/
```

3. Activar primero el plugin en Plugins y luego BAMIRA en Apariencia → Temas.
4. Ejecutar el seed revisado desde WP-CLI SOLO en la instalación local respaldada.
5. Abrir la portada como administrador. Comprobar 8 modelos, 66 variaciones y cantidades vacías.
6. Probar sin sesión: borradores privados, compras bloqueadas incluso con carrito previo.
7. Revisar móvil 390px, tablet 768px, escritorio 1440px: menú, tarjetas, contraste, foco y enlaces.

Si falta WordPress local, el compose solo prepara los servicios: también necesita `.env`,
instalación de WordPress y WooCommerce. No levantar un sitio vacío suponiendo que contiene el VPS.
No se han ejecutado estos cambios sobre ninguna base ni sobre el VPS.

## Reversión

Activar el tema anterior restaura su presentación. Mantener el plugin de bloqueo mientras no se venda.
La corrección del inventario requiere el respaldo previo si se necesitan recuperar cantidades antiguas.

## Pendientes de validación

Ejecución real en WordPress/WooCommerce, QA visual y comprobación de Store API/checkout bloqueados.
Las validaciones estáticas no sustituyen estas pruebas. Los Excel históricos permanecen intactos.
