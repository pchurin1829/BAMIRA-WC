<?php get_header(); ?>
<main id="contenido">
<?php if(current_user_can('manage_woocommerce')): ?><div class="preview-note">Vista de revisión: incluye los modelos en borrador. Sin precios ni stock confirmado. Las muestras de color son orientativas.</div><?php endif; ?>
<section class="hero"><div class="wrap hero-inner"><div><span class="eyebrow">Bikinis &amp; Activewear</span><h1>Tu forma.<br>Tu movimiento.</h1><p>Descubrí los modelos que forman parte de nuestra primera colección.</p><a class="button" href="#bikinis">Explorar la colección</a></div><img class="hero-logo" src="<?php echo esc_url(get_theme_file_uri('assets/logo.jpeg')); ?>" alt="BAMIRA · Bikinis & Activewear"></div></section>
<?php
bamira_collection('Bikinis','bikinis',array('BAM-TOP-MILE','BAM-TOP-GIGI','BAM-VEDETINA','BAM-COLALESS'));
bamira_collection('Activewear','activewear',array('BAM-TOP-DEP','BAM-CALZA-DEP','BAM-SHORT-BREN','BAM-SHORT-KAI'));
?>
<section class="brand-note"><span class="eyebrow">BAMIRA</span><h2>Una colección, dos universos.</h2><p>Bikinis y prendas deportivas. Estamos preparando todos los detalles para presentártelos.</p></section>
</main><?php get_footer(); ?>
