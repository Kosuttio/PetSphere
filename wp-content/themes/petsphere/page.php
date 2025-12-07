<?php
/**
 * The template for displaying all pages
 */
get_header();
?>

<main class="container page-container">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php
                if ( class_exists( 'WooCommerce' ) && is_cart() ) {
                    echo do_shortcode( '[woocommerce_cart]' );
                } else {
                    the_content();
                }
                ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
