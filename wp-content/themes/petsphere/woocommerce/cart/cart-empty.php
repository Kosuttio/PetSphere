<?php
/**
 * Empty cart page
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
// do_action( 'woocommerce_cart_is_empty' ); // We replace this with our own content
?>

<div class="petsphere-empty-cart">
    <div class="empty-cart-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="color: #ccc;">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
    </div>
    
    <h2 class="empty-cart-title"><?php esc_html_e( 'Váš košík je prázdný', 'petsphere' ); ?></h2>
    <p class="empty-cart-message"><?php esc_html_e( 'Vypadá to, že jste ještě nic nepřidali do košíku.', 'petsphere' ); ?></p>

    <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
        <p class="return-to-shop">
            <a class="button wc-backward" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
                <?php esc_html_e( 'Zpět do obchodu', 'petsphere' ); ?>
            </a>
        </p>
    <?php endif; ?>
</div>

<?php
// Optional: Show some products (e.g. Best Sellers or Newest)
// We use our custom grid styling by wrapping it
echo '<div class="petsphere-empty-cart-suggestions" style="margin-top: 50px;">';
echo '<h3 style="text-align: center; margin-bottom: 30px;">' . esc_html__( 'Mohlo by se vám líbit', 'petsphere' ) . '</h3>';
echo do_shortcode( '[products limit="4" columns="4" orderby="popularity"]' );
echo '</div>';
?>
