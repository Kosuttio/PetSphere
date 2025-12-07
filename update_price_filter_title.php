<?php
require_once 'wp-load.php';

$widget_name = 'woocommerce_price_filter';
$widget_instances = get_option( 'widget_' . $widget_name );

if ( is_array( $widget_instances ) ) {
    foreach ( $widget_instances as $key => $instance ) {
        if ( is_array( $instance ) && isset( $instance['title'] ) ) {
            if ( $instance['title'] === 'Filtrovat podle ceny' ) {
                $widget_instances[$key]['title'] = 'Cena';
                echo "Updated widget $widget_name-$key title to 'Cena'.\n";
            }
        }
    }
    update_option( 'widget_' . $widget_name, $widget_instances );
    echo "Widget options saved.\n";
} else {
    echo "No instances of $widget_name found.\n";
}
