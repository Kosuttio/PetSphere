<?php
/**
 * The template for displaying all WooCommerce pages.
 *
 * This template overrides the default WooCommerce template to implement
 * a custom layout with a sidebar on the left and content on the right.
 */

get_header();
?>

<div class="shop-layout-container container">
    
    <?php if ( is_active_sidebar( 'shop-sidebar' ) && !is_product() ) : ?>
        <aside class="shop-sidebar">
            <div class="sidebar-inner">
                <button id="petsphere-filter-submit" class="button alt" style="width: 100%; margin-bottom: 20px; display: none;">Filtrovat</button>
                <?php dynamic_sidebar( 'shop-sidebar' ); ?>
            </div>
        </aside>
    <?php endif; ?>

    <main class="shop-main-content <?php echo ( is_active_sidebar( 'shop-sidebar' ) && !is_product() ) ? 'has-sidebar' : 'no-sidebar'; ?>">
        <?php 
        if ( is_singular( 'product' ) ) {
            woocommerce_content();
        } else {
            // Custom Loop for Shop/Archive
            if ( have_posts() ) {
                
                // Hook: woocommerce_before_shop_loop (Breadcrumb, Catalog Ordering, etc.)
                // Note: Our custom categories are also hooked here in functions.php
                do_action( 'woocommerce_before_shop_loop' );

                echo '<div class="products-grid-wrapper">'; // Wrapper for styling
                woocommerce_product_loop_start();

                while ( have_posts() ) {
                    the_post();
                    
                    // Debug comment to verify loop is running
                    echo '<!-- Product: ' . get_the_title() . ' -->';
                    
                    wc_get_template_part( 'content', 'product' );
                }

                woocommerce_product_loop_end();
                echo '</div>';

                // Hook: woocommerce_after_shop_loop (Pagination)
                do_action( 'woocommerce_after_shop_loop' );

            } else {
                // No posts found
                do_action( 'woocommerce_no_products_found' );
            }
        }
        ?>
    </main>

</div>

<?php
get_footer();
?>
