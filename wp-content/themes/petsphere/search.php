<?php
/**
 * The template for displaying search results pages
 */

get_header();
?>

<main class="container page-container">
    <header class="entry-header">
        <h1 class="entry-title">
            <?php
            /* translators: %s: search query. */
            printf( esc_html__( 'Výsledky hledání pro: %s', 'petsphere' ), '<span>' . get_search_query() . '</span>' );
            ?>
        </h1>
    </header>

    <?php if ( have_posts() ) : ?>
        <div class="search-results-grid">
            <?php
            while ( have_posts() ) :
                the_post();
                
                // Check if it's a product or post
                $is_product = get_post_type() === 'product';
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('search-result-item'); ?>>
                    <a href="<?php the_permalink(); ?>" class="search-result-link">
                        <div class="search-result-thumbnail">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'woocommerce_thumbnail' ); ?>
                            <?php else : ?>
                                <div class="placeholder-image"></div>
                            <?php endif; ?>
                        </div>
                        <div class="search-result-content">
                            <h2 class="search-result-title"><?php the_title(); ?></h2>
                            <?php if ( $is_product ) : 
                                global $product;
                                ?>
                                <div class="search-result-price"><?php echo $product->get_price_html(); ?></div>
                            <?php else : ?>
                                <div class="search-result-excerpt"><?php the_excerpt(); ?></div>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php
            the_posts_pagination( array(
                'prev_text' => '&laquo; Předchozí',
                'next_text' => 'Další &raquo;',
            ) );
            ?>
        </div>

    <?php else : ?>
        <div class="no-results-content">
            <p><?php esc_html_e( 'Bohužel jsme nic nenašli. Zkuste hledat něco jiného.', 'petsphere' ); ?></p>
            <?php get_search_form(); ?>
        </div>
    <?php endif; ?>
</main>

<?php
get_footer();
