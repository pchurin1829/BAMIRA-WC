<?php
defined('ABSPATH') || exit;
get_header();

$p = wc_get_product(get_queried_object_id());
?>
<main id="contenido" class="wrap content">
<?php if ($p): ?>
    <?php if ('draft' === $p->get_status()): ?>
        <p class="preview-note">Vista de revisión del producto en borrador.</p>
    <?php endif; ?>

    <p><a href="<?php echo esc_url(home_url('/')); ?>">← Volver a la colección</a></p>

    <div class="bamira-detail">
        <div class="product-art">
            <?php if ($p->get_image_id()): ?>
                <?php echo wp_get_attachment_image($p->get_image_id(), 'large'); ?>
            <?php else: ?>
                <span aria-hidden="true">B.</span>
                <small>Imagen próximamente</small>
            <?php endif; ?>
        </div>

        <div>
            <span class="eyebrow">BAMIRA · Colección en preparación</span>
            <h1><?php echo esc_html($p->get_name()); ?></h1>
            <p>Estamos preparando las imágenes y la información de este modelo.</p>

            <h2>Colores previstos</h2>
            <ul class="bamira-options">
            <?php
            $swatches = array(
                'negro' => '#202024',
                'bordo-frambuesa' => '#781e3e',
                'azul' => '#243f77',
                'petroleo' => '#235957',
                'chocolate' => '#54392e',
            );
            $colors = wc_get_product_terms($p->get_id(), 'pa_color-diseno', array('fields' => 'names'));
            foreach ($colors as $name):
                $hex = $swatches[sanitize_title($name)] ?? '#ddd';
            ?>
                <li>
                    <span class="swatch" aria-hidden="true" style="--swatch:<?php echo esc_attr($hex); ?>"></span>
                    <?php echo esc_html($name); ?>
                </li>
            <?php endforeach; ?>
            </ul>
            <p class="bamira-help">Las muestras de color son orientativas.</p>

            <h2>Talles previstos</h2>
            <ul class="bamira-options">
            <?php
            $sizes = wc_get_product_terms($p->get_id(), 'pa_talle', array('fields' => 'names'));
            natsort($sizes);
            foreach ($sizes as $size):
            ?>
                <li>Talle <?php echo esc_html($size); ?></li>
            <?php endforeach; ?>
            </ul>
            <p class="bamira-help">La guía de medidas estará disponible cuando se confirme.</p>

            <div class="bamira-pending">
                <strong>Próximamente</strong>
                <p>Precio y disponibilidad pendientes de confirmación.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No se encontró el producto.</p>
<?php endif; ?>
</main>
<?php get_footer(); ?>