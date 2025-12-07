<?php
require_once 'wp-load.php';

$sidebar_id = 'shop-sidebar';
$sidebars_widgets = get_option( 'sidebars_widgets' );

if ( isset( $sidebars_widgets[$sidebar_id] ) ) {
    echo "Widgets in $sidebar_id:\n";
    foreach ( $sidebars_widgets[$sidebar_id] as $widget_id ) {
        echo "- $widget_id\n";
        
        // Parse widget base and ID
        if ( preg_match( '/^(.+)-(\d+)$/', $widget_id, $matches ) ) {
            $base = $matches[1];
            $id = $matches[2];
            $options = get_option( 'widget_' . $base );
            if ( isset( $options[$id] ) ) {
                print_r( $options[$id] );
            }
        }
    }
} else {
    echo "Sidebar $sidebar_id not found.\n";
}
