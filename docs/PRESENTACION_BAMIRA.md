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

Cada uno de los ocho modelos tiene ficha propia (`single-product.php` del tema) con el espacio
de imagen, los colores y los talles previstos y el bloque «Próximamente». En la portada, la
imagen (o el placeholder cuando no hay foto) y el nombre de cada tarjeta enlazan a esa ficha;
en borradores el enlace usa la previsualización de WordPress, de modo que solo abre a quien
puede revisar el modelo.

El CSS del tema se encola con la versión `filemtime()` de `style.css`: al tocar el archivo
cambia la URL y el navegador carga la hoja nueva sin necesidad de una recarga forzada.
Con una versión fija el navegador reutilizaba la hoja anterior y las correcciones no se veían.

Revisión móvil realizada sobre portada y las ocho fichas: sin desborde horizontal a 390 px
y con el espacio sin foto de la ficha limitado a 180 px de alto. También revisado a 768 px
y 1440 px, sin desborde. El catálogo se ve únicamente en sesión de revisión autorizada:
WooCommerce está en modo «Coming soon», así que el visitante anónimo recibe la pantalla de
espera y nunca la portada con los borradores.

En la base local no queda ningún producto publicado: los ocho modelos oficiales y los dos
productos heredados están en borrador.

## Productos heredados pasados a borrador

El estado anterior queda registrado en la tabla siguiente y no depende de archivos externos.
El cambio se aplicó en la base local mediante la API CRUD de WooCommerce
(`wc_get_product()` → `set_status('draft')` → `save()`). Nada se eliminó: no hay productos
en la papelera y ni las imágenes ni las variaciones se tocaron.

| ID | Nombre | SKU | Tipo | Estado anterior | Estado actual | Precio | Stock | Imagen | Variaciones |
|---|---|---|---|---|---|---|---|---|---|
| 17 | Remera entrenamiento mujer | (sin SKU) | variable | publish | draft | 19500 | sin control de stock | 31 (remera-roja.jpg) | 12 (IDs 18-29) |
| 12 | Remera mujer - Test | REM-ENTR-001 | simple | publish | draft | 22000 (regular 25000) | 10 | 30 (remera-negra.jpg) | 0 |

Los precios, el stock y la visibilidad de catálogo de esos dos productos quedan intactos:
solo se cambió el estado a borrador. Sus variaciones siguen con estado `publish`, igual que
las de los modelos oficiales, y no se modificaron.

**Este cambio vive en la base de datos local y no se transporta con Git.** Git mueve tema,
plugin, scripts y documentación; los estados, precios, stock e imágenes de WordPress
dependen de la base de datos de cada entorno. Para aplicarlo donde corresponda hay que
ejecutarlo explícitamente con WP-CLI, por ejemplo:

```php
$product = wc_get_product( 17 );
$product->set_status( 'draft' );
$product->save();
```

No se ejecutó en el VPS ni en ninguna otra instalación.

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

Para actualizar una instalación local ya existente se copian los contenidos sobre el tema
instalado, sin anidar carpetas:

```powershell
docker cp .\wordpress\themes\bamira\. bamira_wc_wordpress:/var/www/html/wp-content/themes/bamira/
docker cp .\wordpress\plugins\bamira-catalog-mode\. bamira_wc_wordpress:/var/www/html/wp-content/plugins/bamira-catalog-mode/
```

Si falta WordPress local, el compose solo prepara los servicios: también necesita `.env`,
instalación de WordPress y WooCommerce. No levantar un sitio vacío suponiendo que contiene el VPS.
No se han ejecutado estos cambios sobre ninguna base ni sobre el VPS.

## Reversión

Activar el tema anterior restaura su presentación. Mantener el plugin de bloqueo mientras no se venda.
La corrección del inventario requiere el respaldo previo si se necesitan recuperar cantidades antiguas.

## Pendientes de validación

Ejecución real en WordPress/WooCommerce, QA visual y comprobación de Store API/checkout bloqueados.
Las validaciones estáticas no sustituyen estas pruebas. Los Excel históricos permanecen intactos.

- El modo «Coming soon» de WooCommerce sigue activo a propósito: los visitantes anónimos no
  ven la portada. Desactivarlo solo cuando se decida publicar la presentación.
- Los dos productos antiguos ya están en borrador en la base local, pero el VPS y cualquier
  otra instalación conservan su estado hasta que se aplique allí el mismo cambio.
- Siguen sin fotos, precios ni cantidades de stock: esos datos no se inventaron.
- El espacio sin foto de la ficha mantiene la proporción 4/5 de las tarjetas en escritorio
  (560 × 700 px a 1440 px); el límite de 180 px aplica solo a 700 px o menos.
