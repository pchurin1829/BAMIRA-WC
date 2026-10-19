# BAMIRA — Catálogo estructural inicial

Actualizado: 18/10/2026. Sustituye las instrucciones comerciales anteriores.

## Alcance vigente

8 productos variables y 66 combinaciones, talles 1, 2 y 3. Sin precios, fotos ni cantidades confirmadas.
Las 427 unidades y los Excel de `docs/catalogo-real` son antecedentes históricos, NO inventario operativo autorizado.
No ejecutar los seeds anteriores de catálogo genérico para ampliar esta selección.

| Modelos | Colores | Variaciones por modelo |
|---|---|---|
| Top Gigi, Top Mile, Vedetina, Colaless | Negro, Bordo Frambuesa, Azul, Petróleo | 12 |
| Top deportivo, Calza deportiva | Negro, Chocolate | 6 |
| Short Bren, Short Kai | Negro | 3 |

## Importación

Preferir `seed-bamira-real-catalog.php` mediante WP-CLI, con backup previo de la base.
Crea nuevos productos en borrador, sin precios ni imágenes. Nuevas variaciones: inventario no gestionado,
cantidad NULL, no disponibles y metadato `_bamira_stock_pending=yes`. No equivale a stock cero.
Reejecución: elimina cantidades anteriores SOLO de variaciones sin precio cuyo padre siga en borrador.
Productos publicados u otros estados se omiten y producen validación incompleta; requieren revisión individual.
No elimina productos, pedidos, imágenes ni precios existentes.

El CSV mantiene los mismos SKUs; `Publicado=-1` corresponde a borrador, inventario vacío y no disponible.
Usarlo para altas nuevas. NO usar su opción de actualizar existentes para limpiar stock: los campos vacíos
pueden preservarse por el importador; usar el script revisado para esa corrección.

## Presentación

Ver `docs/PRESENTACION_BAMIRA.md`. El tema se instala por separado; Git pull no activa temas,
no importa productos y no cambia el VPS.
