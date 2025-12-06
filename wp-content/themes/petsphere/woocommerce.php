<?php
/**
 * The template for displaying all WooCommerce pages
 *
 * This template replaces page.php for WooCommerce content.
 */
get_header();
?>

<main class="container page-container woocommerce-container">
    <?php
    if ( have_posts() ) :
        woocommerce_content();
    endif;
    ?>
</main>

<?php
get_footer();
