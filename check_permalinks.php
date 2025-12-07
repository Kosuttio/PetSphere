<?php
require_once('wp-load.php');

$shop_page_id = wc_get_page_id( 'shop' );
$shop_page = get_post( $shop_page_id );

echo "Shop Page ID: " . $shop_page_id . "\n";
if ($shop_page) {
    echo "Shop Page Title: " . $shop_page->post_title . "\n";
    echo "Shop Page Slug (post_name): " . $shop_page->post_name . "\n";
    echo "Shop Page Guid: " . $shop_page->guid . "\n";
    echo "Shop Page Permalink: " . get_permalink($shop_page_id) . "\n";
} else {
    echo "Shop page not found.\n";
}

echo "\n--- Permalink Structure ---\n";
echo "Permalink Structure: " . get_option('permalink_structure') . "\n";

echo "\n--- WooCommerce Permalinks ---\n";
$wc_permalinks = get_option( 'woocommerce_permalinks' );
print_r($wc_permalinks);

?>
