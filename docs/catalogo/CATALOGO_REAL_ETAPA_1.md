# BAMIRA — Catálogo Real · Etapa 1

> **Versión:** 1.0  
> **Fecha:** 2026-10-13  
> **Alcance:** Carga de los 8 productos iniciales confirmados y sus 66 variaciones en WooCommerce (entorno local).  
> **Estado comercial inicial:** Borrador (`draft`), sin precios, sin imágenes, stock gestionado por variación (total: 427 unidades).  
> **Archivos generados para importación:**  
> - Script WP-CLI: `tools/catalog/seed-bamira-real-catalog.php`  
> - Archivo CSV nativo: `tools/catalog/bamira-catalogo-real-etapa1.csv`

---

## 1. Resumen Ejecutivo

| Métrica | Valor | Criterio de Aceptación |
| :--- | :---: | :--- |
| **Modelos / Productos padre** | **8** | 8 productos tipo `variable`, estado `draft` |
| **Variaciones físicas** | **66** | Combinaciones Talle × Color con SKU unívoco |
| **Talles confirmados** | **1, 2, 3** | Términos asegurados en atributo `pa_talle` |
| **Colores confirmados** | **5** | Negro, Bordo Frambuesa, Azul, Petróleo, Chocolate en `pa_color-diseno` |
| **Unidades de stock** | **427** | Stock asignado a nivel de variación con `manage_stock = yes` |
| **Precios** | **Vacíos** | Sin cero ni valores simulados (bloqueo preventivo de venta) |
| **Imágenes** | **Pendientes** | Sin adjuntos en esta etapa; se vincularán en la Etapa 2 |

---

## 2. Convención Normativa de SKU

Para garantizar la estabilidad, unicidad y compatibilidad con WooCommerce, sistemas de gestión y códigos de barras, se adopta la siguiente convención:

### Formato Producto Padre
$$\texttt{BAM-<CODIGO_MODELO>}$$
Ejemplo: `BAM-TOP-GIGI`, `BAM-VEDETINA`, `BAM-CALZA-DEP`

### Formato Variación (Hijo)
$$\texttt{BAM-<CODIGO_MODELO>-<COLOR_NORM>-T<TALLE>}$$
- **`BAM`**: Prefijo institucional.
- **`<CODIGO_MODELO>`**: Código de modelo del producto padre.
- **`<COLOR_NORM>`**: Identificador de color normalizado en mayúsculas sin caracteres diacríticos ni espacios (`NEGRO`, `BORDO-FRAMBUESA`, `AZUL`, `PETROLEO`, `CHOCOLATE`).
- **`T<TALLE>`**: Identificador de talle (`T1`, `T2`, `T3`).

> [!NOTE]
> Esta convención es 100% compatible con la planilla maestra confirmada (`docs/catalogo-real/BAMIRA_Catalogo_Real_Confirmado_v1_1.xlsx`) y normaliza caracteres como la tilde en `Petróleo` (`PETROLEO`) para prevenir inconvenientes de codificación en URLs o bases de datos.

---

## 3. Matriz Detallada de Modelos, Variaciones y Stock

### 3.1. Top Gigi (`BAM-TOP-GIGI`)
- **Categoría:** Urbano > Malla de baño
- **Atributos informativos:** Línea: Mujer | Uso: Baño
- **Variaciones (12) y Stock (72 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-TOP-GIGI-NEGRO-T1` | Negro | 1 | 6 |
| `BAM-TOP-GIGI-NEGRO-T2` | Negro | 2 | 8 |
| `BAM-TOP-GIGI-NEGRO-T3` | Negro | 3 | 4 |
| `BAM-TOP-GIGI-BORDO-FRAMBUESA-T1` | Bordo Frambuesa | 1 | 6 |
| `BAM-TOP-GIGI-BORDO-FRAMBUESA-T2` | Bordo Frambuesa | 2 | 8 |
| `BAM-TOP-GIGI-BORDO-FRAMBUESA-T3` | Bordo Frambuesa | 3 | 4 |
| `BAM-TOP-GIGI-AZUL-T1` | Azul | 1 | 6 |
| `BAM-TOP-GIGI-AZUL-T2` | Azul | 2 | 8 |
| `BAM-TOP-GIGI-AZUL-T3` | Azul | 3 | 4 |
| `BAM-TOP-GIGI-PETROLEO-T1` | Petróleo | 1 | 6 |
| `BAM-TOP-GIGI-PETROLEO-T2` | Petróleo | 2 | 8 |
| `BAM-TOP-GIGI-PETROLEO-T3` | Petróleo | 3 | 4 |

---

### 3.2. Top Mile (`BAM-TOP-MILE`)
- **Categoría:** Urbano > Malla de baño
- **Atributos informativos:** Línea: Mujer | Uso: Baño
- **Variaciones (12) y Stock (72 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-TOP-MILE-NEGRO-T1` | Negro | 1 | 6 |
| `BAM-TOP-MILE-NEGRO-T2` | Negro | 2 | 8 |
| `BAM-TOP-MILE-NEGRO-T3` | Negro | 3 | 4 |
| `BAM-TOP-MILE-BORDO-FRAMBUESA-T1` | Bordo Frambuesa | 1 | 6 |
| `BAM-TOP-MILE-BORDO-FRAMBUESA-T2` | Bordo Frambuesa | 2 | 8 |
| `BAM-TOP-MILE-BORDO-FRAMBUESA-T3` | Bordo Frambuesa | 3 | 4 |
| `BAM-TOP-MILE-AZUL-T1` | Azul | 1 | 6 |
| `BAM-TOP-MILE-AZUL-T2` | Azul | 2 | 8 |
| `BAM-TOP-MILE-AZUL-T3` | Azul | 3 | 4 |
| `BAM-TOP-MILE-PETROLEO-T1` | Petróleo | 1 | 6 |
| `BAM-TOP-MILE-PETROLEO-T2` | Petróleo | 2 | 8 |
| `BAM-TOP-MILE-PETROLEO-T3` | Petróleo | 3 | 4 |

---

### 3.3. Vedetina (`BAM-VEDETINA`)
- **Categoría:** Urbano > Malla de baño
- **Atributos informativos:** Línea: Mujer | Uso: Baño
- **Variaciones (12) y Stock (78 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-VEDETINA-NEGRO-T1` | Negro | 1 | 8 |
| `BAM-VEDETINA-NEGRO-T2` | Negro | 2 | 10 |
| `BAM-VEDETINA-NEGRO-T3` | Negro | 3 | 6 |
| `BAM-VEDETINA-BORDO-FRAMBUESA-T1` | Bordo Frambuesa | 1 | 6 |
| `BAM-VEDETINA-BORDO-FRAMBUESA-T2` | Bordo Frambuesa | 2 | 8 |
| `BAM-VEDETINA-BORDO-FRAMBUESA-T3` | Bordo Frambuesa | 3 | 4 |
| `BAM-VEDETINA-AZUL-T1` | Azul | 1 | 6 |
| `BAM-VEDETINA-AZUL-T2` | Azul | 2 | 8 |
| `BAM-VEDETINA-AZUL-T3` | Azul | 3 | 4 |
| `BAM-VEDETINA-PETROLEO-T1` | Petróleo | 1 | 6 |
| `BAM-VEDETINA-PETROLEO-T2` | Petróleo | 2 | 8 |
| `BAM-VEDETINA-PETROLEO-T3` | Petróleo | 3 | 4 |

---

### 3.4. Colaless (`BAM-COLALESS`)
- **Categoría:** Urbano > Malla de baño
- **Atributos informativos:** Línea: Mujer | Uso: Baño
- **Variaciones (12) y Stock (100 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-COLALESS-NEGRO-T1` | Negro | 1 | 10 |
| `BAM-COLALESS-NEGRO-T2` | Negro | 2 | 12 |
| `BAM-COLALESS-NEGRO-T3` | Negro | 3 | 6 |
| `BAM-COLALESS-BORDO-FRAMBUESA-T1` | Bordo Frambuesa | 1 | 8 |
| `BAM-COLALESS-BORDO-FRAMBUESA-T2` | Bordo Frambuesa | 2 | 10 |
| `BAM-COLALESS-BORDO-FRAMBUESA-T3` | Bordo Frambuesa | 3 | 6 |
| `BAM-COLALESS-AZUL-T1` | Azul | 1 | 8 |
| `BAM-COLALESS-AZUL-T2` | Azul | 2 | 10 |
| `BAM-COLALESS-AZUL-T3` | Azul | 3 | 6 |
| `BAM-COLALESS-PETROLEO-T1` | Petróleo | 1 | 8 |
| `BAM-COLALESS-PETROLEO-T2` | Petróleo | 2 | 10 |
| `BAM-COLALESS-PETROLEO-T3` | Petróleo | 3 | 6 |

---

### 3.5. Top deportivo (`BAM-TOP-DEP`)
- **Categoría:** Indumentaria deportiva > Top deportivo
- **Atributos informativos:** Línea: Mujer | Uso: Entrenamiento
- **Variaciones (6) y Stock (32 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-TOP-DEP-NEGRO-T1` | Negro | 1 | 6 |
| `BAM-TOP-DEP-NEGRO-T2` | Negro | 2 | 8 |
| `BAM-TOP-DEP-NEGRO-T3` | Negro | 3 | 4 |
| `BAM-TOP-DEP-CHOCOLATE-T1` | Chocolate | 1 | 4 |
| `BAM-TOP-DEP-CHOCOLATE-T2` | Chocolate | 2 | 6 |
| `BAM-TOP-DEP-CHOCOLATE-T3` | Chocolate | 3 | 4 |

---

### 3.6. Calza deportiva (`BAM-CALZA-DEP`)
- **Categoría:** Indumentaria deportiva > Calza deportiva
- **Atributos informativos:** Línea: Mujer | Uso: Entrenamiento
- **Variaciones (6) y Stock (29 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-CALZA-DEP-NEGRO-T1` | Negro | 1 | 6 |
| `BAM-CALZA-DEP-NEGRO-T2` | Negro | 2 | 6 |
| `BAM-CALZA-DEP-NEGRO-T3` | Negro | 3 | 4 |
| `BAM-CALZA-DEP-CHOCOLATE-T1` | Chocolate | 1 | 4 |
| `BAM-CALZA-DEP-CHOCOLATE-T2` | Chocolate | 2 | 5 |
| `BAM-CALZA-DEP-CHOCOLATE-T3` | Chocolate | 3 | 4 |

---

### 3.7. Short Bren (`BAM-SHORT-BREN`)
- **Categoría:** Indumentaria deportiva > Short deportivo
- **Atributos informativos:** Línea: Mujer | Uso: Entrenamiento
- **Variaciones (3) y Stock (22 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-SHORT-BREN-NEGRO-T1` | Negro | 1 | 8 |
| `BAM-SHORT-BREN-NEGRO-T2` | Negro | 2 | 8 |
| `BAM-SHORT-BREN-NEGRO-T3` | Negro | 3 | 6 |

---

### 3.8. Short Kai (`BAM-SHORT-KAI`)
- **Categoría:** Indumentaria deportiva > Short deportivo
- **Atributos informativos:** Línea: Mujer | Uso: Entrenamiento
- **Variaciones (3) y Stock (22 unidades):**

| SKU Variación | Color | Talle | Stock |
| :--- | :--- | :---: | :---: |
| `BAM-SHORT-KAI-NEGRO-T1` | Negro | 1 | 8 |
| `BAM-SHORT-KAI-NEGRO-T2` | Negro | 2 | 8 |
| `BAM-SHORT-KAI-NEGRO-T3` | Negro | 3 | 6 |

---

## 4. Totales y Validación

$$\sum \text{Modelos} = 8 \quad | \quad \sum \text{Variaciones} = 66 \quad | \quad \sum \text{Unidades} = 427$$

- Total de unidades Top Gigi: **72**
- Total de unidades Top Mile: **72**
- Total de unidades Vedetina: **78**
- Total de unidades Colaless: **100**
- Total de unidades Top deportivo: **32**
- Total de unidades Calza deportiva: **29**
- Total de unidades Short Bren: **22**
- Total de unidades Short Kai: **22**
- **TOTAL GENERAL: 427 unidades exactas.**

---

## 5. Instrucciones de Ejecución (Cuando se Autorice)

### Opción A — Vía WP-CLI (Recomendada para Docker local)
Dado que el contenedor Docker utiliza un volumen nombrado (`bamira_wc_wordpress_data`) y no monta directamente el directorio del host, la ejecución se realiza pasando el script por entrada estándar (STDIN) o copiándolo previamente:

**En PowerShell (Windows):**
```powershell
Get-Content tools/catalog/seed-bamira-real-catalog.php -Raw | docker compose exec -T wordpress wp eval-file - --allow-root
```

**Alternativa con copia temporal:**
```bash
docker cp tools/catalog/seed-bamira-real-catalog.php bamira_wc_wordpress:/tmp/seed-bamira-real-catalog.php
docker compose exec wordpress wp eval-file /tmp/seed-bamira-real-catalog.php --allow-root
docker compose exec wordpress rm /tmp/seed-bamira-real-catalog.php
```

> *Nota:* Si el contenedor base `wordpress:php8.3-apache` no tiene WP-CLI instalado, se puede instalar ejecutando:
> `docker compose exec wordpress bash -c "curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp"`

### Opción B — Vía Panel WooCommerce (CSV sin necesidad de terminal)
1. Ingresar al administrador de WordPress local: `http://localhost:8088/wp-admin` > `Productos` > `Importar`.
2. Seleccionar el archivo: `tools/catalog/bamira-catalogo-real-etapa1.csv`.
3. **Importante:** Marcar la casilla *"Actualizar los productos existentes que coincidan por ID o SKU"*.
4. Ejecutar el importador. Todos los productos ingresarán en estado **Borrador**, con su stock respectivo y sin precios asignados.
