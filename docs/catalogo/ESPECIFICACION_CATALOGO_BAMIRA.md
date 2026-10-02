# Especificación del Catálogo BAMIRA — v1

> Versión: 1.1
> Fecha: 2026-10-02 (v1.0) / actualización CATÁLOGO-2B+3
> Alcance: estructura global del catálogo WooCommerce (categorías, atributos globales y términos) + 8 productos base en borrador. Las variaciones físicas se generan en una fase posterior.
> Scripts asociados: `tools/catalog/seed-bamira-catalog.php` (estructura, CATÁLOGO-2) y `tools/catalog/seed-bamira-products.php` (término Naranja / Gris + 8 productos base, CATÁLOGO-2B+3). Ambos idempotentes, no ejecutados en esta tarea.

---

## A) Rubros

Estructura jerárquica de 2 rubros (categorías padre de WooCommerce, taxonomía `product_cat`) con sus tipos de prenda como subcategorías.

### 1. Indumentaria deportiva

| Tipo de prenda       | Código | Categoría padre          |
|----------------------|--------|--------------------------|
| Malla deportiva      | MDEP   | Indumentaria deportiva   |
| Short deportivo      | SHO    | Indumentaria deportiva   |
| Remera deportiva     | REM    | Indumentaria deportiva   |
| Musculosa deportiva  | MUS    | Indumentaria deportiva   |
| Top deportivo        | TOP    | Indumentaria deportiva   |
| Calza corta          | CALC   | Indumentaria deportiva   |
| Calza larga          | CALL   | Indumentaria deportiva   |

### 2. Urbano

| Tipo de prenda  | Código | Categoría padre |
|-----------------|--------|-----------------|
| Malla de baño   | MBA    | Urbano          |

### Extensibilidad

- La jerarquía es **abierta**: para agregar un nuevo tipo de prenda se crea una nueva subcategoría bajo el rubro correspondiente, sin modificar las existentes.
- Ejemplo futuro: `Pollera deportiva` → nueva subcategoría de `Indumentaria deportiva` con su propio código de tipo (p. ej. `POL`).
- Ejemplo futuro: `Remera urbana` → nueva subcategoría de `Urbano`.
- El script semilla (`tools/catalog/seed-bamira-catalog.php`) está diseñado de forma **data-driven**: agregar una entrada al arreglo de categorías es suficiente y su re-ejecución crea solo lo faltante (idempotente).
- Los códigos de tipo deben mantenerse únicos y estables en el tiempo (ver sección B).

---

## B) Códigos de tipo

Tabla normativa. El código forma parte del SKU (segmento `TIPO`).

| Código | Tipo de prenda       |
|--------|----------------------|
| MDEP   | Malla deportiva      |
| SHO    | Short deportivo      |
| REM    | Remera deportiva     |
| MUS    | Musculosa deportiva  |
| TOP    | Top deportivo        |
| CALC   | Calza corta          |
| CALL   | Calza larga          |
| MBA    | Malla de baño        |

Reglas:

1. Código en mayúsculas, sin espacios, longitud 2–4 caracteres.
2. Unívoco por tipo de prenda en todo el catálogo (no reutilizar códigos entre rubros).
3. Estable: una vez asignado a un tipo de prenda, no se recicla ni se reasigna aunque el tipo se discontinúe.
4. Los futuros tipos de prenda reciben un código nuevo siguiendo el mismo criterio.

---

## C) Atributos globales

Atributos globales de WooCommerce (taxonomías `pa_*`). Definición v1 cerrada pero extensible.

### C.1. Talle (`pa_talle`)

Valores iniciales:

- XS
- S
- M
- L

Notas:

- Se pueden agregar talles a futuro (p. ej. `XL`, `XXL`) como nuevos términos del mismo atributo, sin recrear el atributo.
- Es atributo **de variación** (ver sección D).

### C.2. Color / Diseño (`pa_color-diseno`)

Etiqueta visible para el cliente: **`Color / Diseño`** (atributo único).

Valores iniciales (nombre → sigla para SKU):

| Nombre visible  | Sigla SKU |
|-----------------|-----------|
| Blanco          | BLA       |
| Negro           | NEG       |
| Verde aceituna  | VAC       |
| Azul            | AZU       |
| Marrón          | MAR       |
| Celeste         | CEL       |
| Naranja / Gris  | NGR       |

Reglas:

1. **No separar técnicamente los estampados en otro atributo.** Diseños multicolor, estampados o combinaciones se cargan como un término más del mismo atributo `Color / Diseño`.
2. `Naranja / Gris` → sigla `NGR` es el **ejemplo inicial** de diseño multicolor (incorporado en CATÁLOGO-2B). Futuros ejemplos: `Negro / Blanco`, `Azul / Celeste`, `Multicolor`, etc., como términos nuevos sin cambiar el modelo.
3. Cada término tiene una sigla SKU única, en mayúsculas, 3 caracteres. Documentar la sigla al crear el término (no es el slug de WooCommerce; el slug sigue las reglas de `sanitize_title`, la sigla vive en la especificación y en el generador de SKU).
4. Nuevos colores/diseños se agregan como términos nuevos; la sigla debe ser única y no reutilizada.
5. **Imagen por Color/Diseño** (ver sección G): cada término de este atributo debe poder asociarse a una imagen representativa del color/diseño para el selector de variaciones.
6. Es atributo **de variación** (ver sección D).

### C.3. Línea (`pa_linea`)

Valores iniciales:

- Mujer
- Hombre
- Unisex

Notas:

- Es atributo de **clasificación/filtro**, NO genera variaciones (ver sección D).
- Visible en ficha de producto y usable en filtros de tienda / widgets de navegación por capas.

### C.4. Uso (`pa_uso`)

Valores iniciales:

- Entrenamiento
- Competición
- Baño
- Casual

Notas:

- Es atributo de **clasificación/filtro**, NO genera variaciones (ver sección D).
- Un producto puede tener uno o más valores de Uso según corresponda (p. ej. una malla deportiva: Entrenamiento + Competición).

### Resumen de atributos

| Atributo (etiqueta) | Slug WooCommerce    | ¿Variación? | Valores v1 |
|---------------------|---------------------|-------------|------------|
| Talle               | `pa_talle`          | Sí          | XS, S, M, L |
| Color / Diseño      | `pa_color-diseno`   | Sí          | Blanco, Negro, Verde aceituna, Azul, Marrón, Celeste, Naranja / Gris |
| Línea               | `pa_linea`          | No          | Mujer, Hombre, Unisex |
| Uso                 | `pa_uso`            | No          | Entrenamiento, Competición, Baño, Casual |

---

## D) Variaciones

- **Solo** los siguientes atributos generan variaciones de producto (producto variable de WooCommerce):
  - Talle
  - Color / Diseño
- **Línea** y **Uso** son atributos de clasificación/filtro:
  - Se asignan a nivel de producto padre (o se replican como atributos no-variables del producto variable).
  - **No deben marcarse como "usado para variaciones"** (`variation = false` al crear el `WC_Product_Attribute` en productos).
  - No multiplican el número de variantes.
- Consecuencia: el número de variantes por producto = `N° talles × N° colores/diseños` del modelo. Ejemplo: 4 talles × 3 colores = 12 variantes.
- Cada variante lleva su propio SKU (sección E) y su propio stock.

---

## E) SKU

### Formato

```text
BAM-TIPO-MODELO-COLOR-TALLE
```

Segmentos:

| Segmento | Origen | Ejemplo | Reglas |
|----------|--------|---------|--------|
| `BAM`    | Prefijo fijo de marca | `BAM` | Constante |
| `TIPO`   | Código de tipo (sección B) | `REM`, `TOP` | Mayúsculas, 2–4 chars, estable |
| `MODELO` | Número de modelo dentro del tipo | `001` | 3 dígitos con ceros a la izquierda, estable por modelo |
| `COLOR`  | Sigla de Color/Diseño (sección C.2) | `NEG`, `VAC`, `NGR`, `BLA` | Mayúsculas, 3 chars, única |
| `TALLE`  | Talle de la variante | `XS`, `S`, `M`, `L` | Tal cual el término |

### Ejemplos normativos

```text
BAM-REM-001-NEG-XS
BAM-REM-001-VAC-M
BAM-REM-001-NGR-L
BAM-TOP-001-BLA-S
```

### Reglas

1. El **número de modelo es estable**: identifica el corte/diseño base dentro de un tipo (p. ej. `REM-001` = primera remera deportiva). No se reutiliza para otro diseño.
2. El **código base** del producto padre es `BAM-TIPO-MODELO` (p. ej. `BAM-REM-001`); cada variante agrega `-COLOR-TALLE`.
3. **No incluir en el SKU**: precio, colección/temporada, stock, promoción ni Línea.
4. El SKU de variante debe ser único en toda la instalación (WooCommerce lo exige).
5. La sigla de color multicolor sigue la misma regla (p. ej. `NGR` para `Naranja / Gris`).

---

## F) Visibilidad

Tres estados contemplados explícitamente. Mapeo a WooCommerce:

| Estado BAMIRA            | `post_status` | `stock_status` / comprabilidad | Visible en tienda | Comprable |
|--------------------------|---------------|--------------------------------|-------------------|-----------|
| Borrador                 | `draft`       | n/a (no publicado)             | No                | No        |
| Publicado                | `publish`     | `instock`                      | Sí                | Sí        |
| Publicado sin stock      | `publish`     | `outofstock` / `onbackorder` según política | Sí                | No (o solo reserva, según configuración) |

Detalles:

1. **Borrador**: producto cargado administrativamente pero NO visible en la tienda. Uso: carga anticipada de modelos que todavía no se fabrican, carga de fotos/descripciones pendientes, revisión interna. Se implementa con estado `draft` (o `private` si se requiere vista previa interna).
2. **Publicado**: visible y comprable. Requiere `publish` + stock disponible (`instock`) y precio definido.
3. **Publicado sin stock**: visible pero no comprable según configuración de stock. Implementación: `publish` + `stock_status = outofstock` (botón de compra deshabilitado, muestra "Sin stock"), o `onbackorder` si se habilita preventa/reserva. Depende de `woocommerce_manage_stock` y de la opción "Visibilidad de productos sin stock" (`woocommerce_hide_out_of_stock_items`: debe estar en `no` para que sean visibles).
4. Flujo previsto: cargar el producto en **Borrador** → completar datos/fotos → cuando se fabrica y hay stock, cambiar a **Publicado** sin recrear el producto (se conserva ID, SKU, URL/slug e historial).
5. La configuración de stock (gestionar stock a nivel de variante, permitir o no pedidos pendientes) se define fuera de esta especificación v1 pero debe respetar que "publicado sin stock" siga visible.

---

## G) Datos previstos por producto

Campos mínimos por producto (padre variable). Entre paréntesis, origen/implementación prevista en WooCommerce:

- **Rubro** — categoría padre (`product_cat`). Ej.: `Indumentaria deportiva`.
- **Tipo de prenda** — subcategoría (`product_cat`). Ej.: `Remera deportiva`.
- **Modelo** — número de modelo dentro del tipo (atributo interno / parte del código base). Ej.: `001`.
- **Nombre comercial** — título del producto (`post_title`). Ej.: `Remera deportiva Esencial`.
- **Código base** — `BAM-TIPO-MODELO` (SKU del padre o meta `_sku_base`). Ej.: `BAM-REM-001`.
- **Línea** — atributo global `pa_linea` (no variable). Ej.: `Mujer`.
- **Uso** — atributo global `pa_uso` (no variable, multivalor). Ej.: `Entrenamiento, Competición`.
- **Talles** — términos de `pa_talle` usados para variaciones. Ej.: `XS, S, M, L`.
- **Colores/Diseños** — términos de `pa_color-diseno` usados para variaciones. Ej.: `Negro, Verde aceituna`.
- **Precio** — precio regular (y de oferta si aplica) a nivel de variante (`_regular_price`, `_sale_price`, `_price`). El padre variable no lleva precio propio.
- **Costo** — **administrativo, no visible al comprador**. Meta privada (p. ej. `_cost` / `_bamira_cost`). Base para futura rentabilidad.
- **Stock por variante** — cantidad a nivel de cada variación (`_stock`, `_manage_stock = yes`). No hay stock a nivel padre.
- **Estado** — Borrador / Publicado / Publicado sin stock (sección F; `post_status` + `stock_status`).
- **Descripción** — descripción larga (`post_content`) + descripción corta (`post_excerpt`) para ficha de producto.
- **Imágenes generales** — galería del producto padre (look, detalle, tabla de talles).
- **Imagen por Color/Diseño** — imagen asociada a cada término/valor de `Color / Diseño` para el selector de variación (implementación prevista: plugin de swatches/imagen por variación o meta por término; a definir fuera de v1, pero el modelo de datos debe reservarlo).
- **Colección/temporada** — dato informativo (taxonomía o meta a definir, p. ej. `Invierno 2026`). **No forma parte del SKU.**
- **Proveedor** — **administrativo, no visible al comprador** (previsto para futura gestión; meta privada o entidad relacionada). Ej.: taller/proveedor que confecciona el modelo.

> **Privacidad de datos:** `Costo`, `Proveedor` y la futura `rentabilidad` calculada son datos administrativos internos. No deben exponerse en la tienda, API pública, ni en los atributos visibles de la ficha de producto. Implementación prevista: metadatos con prefijo `_` (no visibles por defecto) y exclusión de la API Store / plantillas.

---

## H) Productos base (CATÁLOGO-2B + CATÁLOGO-3)

Los productos base representan **familias/modelos iniciales**: un producto padre variable por cada tipo de prenda, con los atributos configurados pero **sin variaciones físicas creadas**.

### H.1. Listado

| # | Nombre base            | SKU padre      | Categoría                                          | Línea  | Uso           |
|---|------------------------|----------------|----------------------------------------------------|--------|---------------|
| 1 | Malla deportiva base   | BAM-MDEP-BASE  | Indumentaria deportiva > Malla deportiva           | Mujer  | Entrenamiento |
| 2 | Short deportivo base   | BAM-SHO-BASE   | Indumentaria deportiva > Short deportivo           | Unisex | Entrenamiento |
| 3 | Remera deportiva base  | BAM-REM-BASE   | Indumentaria deportiva > Remera deportiva          | Unisex | Entrenamiento |
| 4 | Musculosa deportiva base | BAM-MUS-BASE | Indumentaria deportiva > Musculosa deportiva       | Unisex | Entrenamiento |
| 5 | Top deportivo base     | BAM-TOP-BASE   | Indumentaria deportiva > Top deportivo             | Mujer  | Entrenamiento |
| 6 | Calza corta base       | BAM-CALC-BASE  | Indumentaria deportiva > Calza corta               | Mujer  | Entrenamiento |
| 7 | Calza larga base       | BAM-CALL-BASE  | Indumentaria deportiva > Calza larga               | Mujer  | Entrenamiento |
| 8 | Malla de baño base     | BAM-MBA-BASE   | Urbano > Malla de baño                             | Mujer  | Baño          |

### H.2. Reglas

1. **Estado inicial: `draft` (Borrador).** Ningún producto base es visible en la tienda pública. Un producto en borrador puede estar completamente configurado administrativamente (categoría, atributos de variación declarados, Línea/Uso) sin aparecer en la tienda.
2. **Tipo WooCommerce: `variable`.** Atributos para variación: `pa_talle` (XS, S, M, L) y `pa_color-diseno` (7 términos, incluido `Naranja / Gris`). Atributos NO usados para variación: `pa_linea`, `pa_uso`.
3. **Sin combinaciones físicas Talle × Color todavía.** Las variaciones reales (hijos con SKU `BAM-TIPO-MODELO-COLOR-TALLE`, precio y stock) se generarán posteriormente según la disponibilidad efectiva de talles y colores de cada modelo.
4. **Sin asignar todavía:** precio, costo, stock, imágenes, proveedor, promociones.
5. **SKU padre `BAM-<TIPO>-BASE`** (p. ej. `BAM-REM-BASE`): es el identificador de idempotencia. Si ya existe un producto con ese SKU, el seed informa `EXISTS` y no lo duplica ni lo modifica destructivamente.
6. Script: `tools/catalog/seed-bamira-products.php` (idempotente; crea como única estructura el término `Naranja / Gris` si falta; aborta con `ERROR` ante cualquier otra estructura faltante sin crearla silenciosamente).

---

## Procedimiento de ejecución

> El script `tools/catalog/seed-bamira-catalog.php` **NO fue ejecutado** en esta tarea. El procedimiento siguiente es para ejecutarlo de forma segura en una instalación WordPress/WooCommerce real (staging o producción) cuando se autorice.

### 1. Requisitos previos

- Instalación WordPress funcional con plugin **WooCommerce activo**.
- Acceso al servidor/contenedor donde corre WordPress (SSH, `docker exec`, o panel con terminal) **o** acceso WP-CLI.
- Usuario con capacidad de `manage_woocommerce` / administrador.
- **Backup** de la base de datos antes de ejecutar (aunque el script no borra nada, es buena práctica).

### 2. Copiar el script a la instalación

El script vive en el repositorio (`tools/catalog/seed-bamira-catalog.php`) y **no** debe quedar expuesto públicamente dentro de `wp-content` de forma permanente. Opciones recomendadas (elegir una):

**Opción A — WP-CLI (recomendada):**

```bash
# 1. Copiar el archivo al servidor (ejemplo con scp o volumen docker)
# 2. Ejecutar con WP-CLI usando el contexto de WordPress:
wp eval-file tools/catalog/seed-bamira-catalog.php
```

Si el archivo está fuera del docroot, pasar la ruta absoluta:

```bash
wp eval-file /ruta/al/repo/tools/catalog/seed-bamira-catalog.php
```

**Opción B — MU-plugin temporal:**

```bash
cp tools/catalog/seed-bamira-catalog.php /ruta/a/wordpress/wp-content/mu-plugins/zz-seed-bamira-catalog.php
# Cargar cualquier página del admin una vez (ej. /wp-admin/) para que se ejecute,
# revisar la salida en el log, y luego ELIMINAR el archivo del mu-plugins.
rm /ruta/a/wordpress/wp-content/mu-plugins/zz-seed-bamira-catalog.php
```

> Nota: la versión actual del script está escrita para `wp eval-file` / inclusión con contexto WP cargado. Si se usa como MU-plugin, ejecutarlo una sola vez y retirarlo para evitar ejecuciones repetidas en cada request (aunque es idempotente, no debe quedar activo).

**No recomendado:** subirlo como plantilla de tema ni dejarlo accesible por URL pública.

### 3. Ejecución

```bash
wp eval-file tools/catalog/seed-bamira-catalog.php
```

Salida esperada por ítem (una línea por categoría, atributo o término):

```text
[CATEGORY] Indumentaria deportiva ... CREATED (id=12)
[CATEGORY] Remera deportiva (padre: Indumentaria deportiva) ... EXISTS (id=15)
[ATTRIBUTE] Talle (pa_talle) ... EXISTS
[TERM] pa_talle:X S ... CREATED
[TERM] pa_color-diseno: Blanco ... EXISTS
...
DONE. created=3 exists=24 errors=0
```

- `CREATED`: se creó en esta ejecución.
- `EXISTS`: ya existía, no se duplicó.
- `ERROR`: no pudo crearse; revisar el mensaje y los permisos. El script continúa con el resto y reporta el conteo final.
- El script es **idempotente**: puede ejecutarse nuevamente después de agregar nuevos valores al código (o sin cambios) sin duplicar nada.

### 4. Verificación posterior

**En el admin de WordPress:**

1. **Categorías:** `Productos → Categorías`. Verificar que existen `Indumentaria deportiva` y `Urbano` como categorías padre, y las 8 subcategorías asignadas a su padre correcto (columna de jerarquía con guion `— Nombre`).
2. **Atributos:** `Productos → Atributos`. Verificar 4 filas: `Talle`, `Color / Diseño`, `Línea`, `Uso`.
3. **Términos:** en `Productos → Atributos`, clic en `Configurar términos` de cada atributo y verificar la lista:
   - Talle: XS, S, M, L.
   - Color / Diseño: Blanco, Negro, Verde aceituna, Azul, Marrón, Celeste, Naranja / Gris.
   - Línea: Mujer, Hombre, Unisex.
   - Uso: Entrenamiento, Competición, Baño, Casual.

**Por WP-CLI (verificación programática):**

```bash
# Categorías de producto
wp term list product_cat --fields=term_id,name,slug,parent --format=table

# Atributos globales (requiere WooCommerce)
wp wc product_attribute list --format=table

# Términos por atributo (ejemplos)
wp term list pa_talle --fields=term_id,name,slug --format=table
wp term list pa_color-diseno --fields=term_id,name,slug --format=table
wp term list pa_linea --fields=term_id,name,slug --format=table
wp term list pa_uso --fields=term_id,name,slug --format=table
```

**Verificación de idempotencia:** ejecutar el script una segunda vez; el resumen debe mostrar `created=0` y `errors=0`, con todo en `EXISTS`.

### 5. Seguridad / límites del script

- El script **solo** crea categorías, atributos y términos. **No** crea productos ni variaciones, **no** modifica los productos de prueba existentes y **no** elimina nada.
- Si WooCommerce no está activo, aborta con mensaje `ERROR: WooCommerce no disponible` sin hacer cambios.
- Ante cualquier duda, ejecutar primero en staging con backup y validar con el procedimiento de esta sección antes de tocar producción.

### 6. Seed de productos base (CATÁLOGO-2B + CATÁLOGO-3)

> Tampoco ejecutado en esta tarea. Ejecutar solo cuando se autorice, después del seed de estructura (CATÁLOGO-2).

```bash
wp eval-file tools/catalog/seed-bamira-products.php
```

Salida esperada (primera ejecución, suponiendo estructura CATÁLOGO-2 ya creada):

```text
[TERM] pa_color-diseno: Blanco ... EXISTS
[TERM] pa_color-diseno: Negro ... EXISTS
[TERM] pa_color-diseno: Verde aceituna ... EXISTS
[TERM] pa_color-diseno: Azul ... EXISTS
[TERM] pa_color-diseno: Marrón ... EXISTS
[TERM] pa_color-diseno: Celeste ... EXISTS
[TERM] pa_color-diseno: Naranja / Gris ... CREATED
[PRODUCT] Malla deportiva base [BAM-MDEP-BASE] ... CREATED (id=... status=draft type=variable)
...
DONE. created=9 exists=6 errors=0
```

Verificación posterior:

```bash
# Productos base por SKU (deben existir los 8, todos draft)
wp wc product list --sku=BAM-MDEP-BASE --fields=id,name,sku,status,type --format=table
# Repetir para BAM-SHO-BASE, BAM-REM-BASE, BAM-MUS-BASE, BAM-TOP-BASE,
# BAM-CALC-BASE, BAM-CALL-BASE, BAM-MBA-BASE.

# Término multicolor
wp term list pa_color-diseno --fields=term_id,name,slug --format=table
```

**Verificación de idempotencia:** segunda ejecución ⇒ `created=0`, `errors=0`, todo `EXISTS`, sin duplicados y sin variaciones hijas creadas.
