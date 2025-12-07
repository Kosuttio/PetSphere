<?php
// Add Rating and Stock Filters to Sidebar
$sidebar_id = 'shop-sidebar';

// Get existing widgets
$sidebars_widgets = get_option( 'sidebars_widgets' );
if ( ! isset( $sidebars_widgets[$sidebar_id] ) ) {
    $sidebars_widgets[$sidebar_id] = array();
}

// Define new widgets
$new_widgets = array(
    'woocommerce_status' => array( // Stock status
        'title' => 'Dostupnost',
    ),
    'woocommerce_rating_filter' => array( // Rating
        'title' => 'Hodnocení',
    ),
);

foreach ( $new_widgets as $widget_name => $widget_options ) {
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
    
    // Add to sidebar (append)
    $sidebars_widgets[$sidebar_id][] = $widget_name . '-' . $new_instance_id;
}

update_option( 'sidebars_widgets', $sidebars_widgets );

echo "Filters added successfully.\n";
?>
