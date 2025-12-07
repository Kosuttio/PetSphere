<?php
require_once('wp-load.php');

// Order: Junior (51), Dospělý (50), Senior (52)
$vek_order = [
    51 => 1,
    50 => 2,
    52 => 3
];

foreach ($vek_order as $term_id => $order) {
    wp_update_term($term_id, 'pa_vek', array('term_order' => $order)); // WooCommerce uses menu_order usually, but let's try standard meta or direct DB update if this fails.
    // Actually, for custom taxonomies, it's often 'order' or we need to update the term_order column in wp_terms (if enabled) or term_taxonomy.
    // WooCommerce attributes usually use 'menu_order' in the term meta or a custom ordering plugin. 
    // But standard WP terms have 'term_order' in the 'wp_term_relationships' table? No, 'wp_terms' has 'term_group'. 
    // Wait, WooCommerce stores attribute order in `wp_termmeta` with key `order` usually for custom sorting?
    // Let's try updating the meta 'order_pa_vek' option? No.
    
    // The standard way for WC attributes is `update_term_meta( $term_id, 'order', $order );`
    update_term_meta($term_id, 'order', $order);
    // Also update the term itself just in case
    // wp_update_term($term_id, 'pa_vek', array('description' => '')); 
}

// Order: Malá (100), Střední (101), Velká (102), Univerzální (103)
$velikost_order = [
    100 => 1,
    101 => 2,
    102 => 3,
    103 => 4
];

foreach ($velikost_order as $term_id => $order) {
    update_term_meta($term_id, 'order', $order);
}

echo "Updated term orders.\n";
