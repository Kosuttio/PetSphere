<?php
// 1. Set Shop Page Display to 'subcategories'
update_option( 'woocommerce_shop_page_display', 'subcategories' );
update_option( 'woocommerce_category_archive_display', 'both' ); // Show products inside categories

// 2. Create Attributes (Color, Brand)
$attributes = array(
    'pa_barva' => array(
        'name' => 'Barva',
        'type' => 'select',
    ),
    'pa_znacka' => array(
        'name' => 'Značka',
        'type' => 'select',
    )
);

foreach ( $attributes as $slug => $data ) {
    $id = wc_create_attribute( array(
        'name'         => $data['name'],
        'slug'         => $slug,
        'type'         => $data['type'],
        'order_by'     => 'menu_order',
        'has_archives' => true,
    ) );
    
    // Register taxonomy if created
    if ( ! is_wp_error( $id ) ) {
        register_taxonomy( $slug, 'product' );
    }
}

// 3. Add Terms to Attributes
$colors = array( 'Červená', 'Modrá', 'Zelená', 'Černá' );
foreach ( $colors as $color ) {
    wp_insert_term( $color, 'pa_barva' );
}

$brands = array( 'Royal Canin', 'Purina', 'Trixie', 'Flexi' );
foreach ( $brands as $brand ) {
    wp_insert_term( $brand, 'pa_znacka' );
}

// 4. Add Widgets to Sidebar
$sidebar_id = 'shop-sidebar';
$widgets = array(
    'woocommerce_price_filter' => array(),
    'woocommerce_layered_nav' => array(
        'title' => 'Filtrovat podle barvy',
        'attribute' => 'barva',
        'query_type' => 'or',
    ),
    'woocommerce_layered_nav_2' => array( // Unique ID hack
        'title' => 'Filtrovat podle značky',
        'attribute' => 'znacka',
        'query_type' => 'or',
    ),
);

// Get current sidebars widgets
$sidebars_widgets = get_option( 'sidebars_widgets' );
$sidebars_widgets[$sidebar_id] = array(); // Clear existing

foreach ( $widgets as $widget_name => $widget_options ) {
    // Get existing widget instances
    $widget_instances = get_option( 'widget_' . $widget_name );
    if ( ! is_array( $widget_instances ) ) {
        $widget_instances = array();
    }
    
    // Add new instance
    $new_instance_id = count( $widget_instances ) + 1;
    $widget_instances[$new_instance_id] = $widget_options;
    
    // Save widget options
    update_option( 'widget_' . $widget_name, $widget_instances );
    
    // Add to sidebar
    $sidebars_widgets[$sidebar_id][] = $widget_name . '-' . $new_instance_id;
}

update_option( 'sidebars_widgets', $sidebars_widgets );

echo "Shop configuration complete.\n";
?>
