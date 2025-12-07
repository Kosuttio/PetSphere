<?php
require_once('wp-load.php');

$menu_name = 'Hamburger menu';
$menu_location = 'mobile';

// 1. Check if menu exists
$menu_exists = wp_get_nav_menu_object( $menu_name );

// If it doesn't exist, create it
if ( ! $menu_exists ) {
    $menu_id = wp_create_nav_menu( $menu_name );
    echo "Created menu '$menu_name' with ID: $menu_id\n";
} else {
    $menu_id = $menu_exists->term_id;
    echo "Menu '$menu_name' already exists (ID: $menu_id). Updating items...\n";
    // Optional: Clear existing items to ensure clean state?
    // $items = wp_get_nav_menu_items($menu_id);
    // foreach($items as $item) wp_delete_post($item->ID);
}

// 2. Add Items

// Hledat (Custom Link)
wp_update_nav_menu_item( $menu_id, 0, array(
    'menu-item-title'   => 'Hledat',
    'menu-item-url'     => '#',
    'menu-item-classes' => 'mobile-search-trigger',
    'menu-item-status'  => 'publish'
) );

// Můj účet (Page)
$account_page_id = get_option('woocommerce_myaccount_page_id');
if ( $account_page_id ) {
    wp_update_nav_menu_item( $menu_id, 0, array(
        'menu-item-title'  => 'Můj účet',
        'menu-item-object' => 'page',
        'menu-item-object-id' => $account_page_id,
        'menu-item-type'   => 'post_type',
        'menu-item-status' => 'publish'
    ) );
}

// Košík (Page)
$cart_page_id = wc_get_page_id('cart');
if ( $cart_page_id ) {
    wp_update_nav_menu_item( $menu_id, 0, array(
        'menu-item-title'  => 'Košík', // Will be filtered dynamically
        'menu-item-object' => 'page',
        'menu-item-object-id' => $cart_page_id,
        'menu-item-type'   => 'post_type',
        'menu-item-classes' => 'mobile-cart-item',
        'menu-item-status' => 'publish'
    ) );
}

// 3. Assign to Location
$locations = get_theme_mod( 'nav_menu_locations' );
$locations[$menu_location] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

echo "Menu assigned to location '$menu_location'.\n";
?>
