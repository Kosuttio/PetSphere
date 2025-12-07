<?php
// Load WordPress environment
require_once('wp-load.php');

echo "Starting attribute and product update...\n";

// 1. Define Attributes and Terms
$attributes = [
    'pa_znacka' => [
        'name' => 'Značka',
        'terms' => ['Acana', 'Brit', 'Carnilove', 'Royal Canin', 'Purina', 'Pedigree']
    ],
    'pa_vek-velikost' => [
        'name' => 'Věk / velikost',
        'terms' => ['Adult', 'Puppy', 'Senior', 'Large Breed', 'Small Breed']
    ]
];

// 2. Create Attributes if they don't exist
foreach ($attributes as $slug => $data) {
    $attribute_id = wc_attribute_taxonomy_id_by_name($slug);
    
    if (!$attribute_id) {
        $args = array(
            'name'         => $data['name'],
            'slug'         => str_replace('pa_', '', $slug),
            'type'         => 'select',
            'order_by'     => 'menu_order',
            'has_archives' => true,
        );
        $attribute_id = wc_create_attribute($args);
        echo "Created attribute: {$data['name']} (ID: $attribute_id)\n";
        register_taxonomy($slug, apply_filters('woocommerce_taxonomy_objects_' . $slug, array('product')), array());
    } else {
        echo "Attribute {$data['name']} already exists.\n";
    }

    // 3. Create Terms
    foreach ($data['terms'] as $term_name) {
        if (!term_exists($term_name, $slug)) {
            wp_insert_term($term_name, $slug);
            echo "  Added term: $term_name\n";
        }
    }
}

// 4. Assign Attributes to Products
$args = array(
    'limit' => -1,
    'status' => 'publish',
);
$products = wc_get_products($args);

echo "Updating " . count($products) . " products...\n";

foreach ($products as $product) {
    $product_id = $product->get_id();
    $attributes_data = [];

    foreach ($attributes as $slug => $data) {
        // Pick a random term
        $terms = get_terms(array('taxonomy' => $slug, 'hide_empty' => false));
        if (!empty($terms) && !is_wp_error($terms)) {
            $random_term = $terms[array_rand($terms)];
            
            // Set the term for the product
            wp_set_object_terms($product_id, $random_term->term_id, $slug);

            // Prepare attribute data for WooCommerce
            $attribute = new WC_Product_Attribute();
            $attribute->set_id(wc_attribute_taxonomy_id_by_name($slug));
            $attribute->set_name($slug);
            $attribute->set_options(array($random_term->term_id));
            $attribute->set_position(0);
            $attribute->set_visible(true);
            $attribute->set_variation(false);
            
            $attributes_data[] = $attribute;
        }
    }

    // Preserve existing attributes (like color if any)
    $existing_attributes = $product->get_attributes();
    $final_attributes = array_merge($existing_attributes, $attributes_data);
    
    $product->set_attributes($final_attributes);
    $product->save();
    echo "  Updated product ID: $product_id\n";
}

// 5. Update Sidebar Widgets
$sidebar_id = 'shop-sidebar';
$widgets = get_option('sidebars_widgets');
$existing_widgets = isset($widgets[$sidebar_id]) ? $widgets[$sidebar_id] : [];

// Clear existing widgets to rebuild cleanly (optional, but safer for order)
// $existing_widgets = []; 

// Define widgets to add
$new_widgets_config = [
    'woocommerce_price_filter' => [],
    'woocommerce_layered_nav' => [
        [
            'title' => 'Značka',
            'attribute' => 'znacka',
            'query_type' => 'or',
            'display_type' => 'list'
        ],
        [
            'title' => 'Věk / velikost',
            'attribute' => 'vek-velikost',
            'query_type' => 'or',
            'display_type' => 'list'
        ]
    ],
    'woocommerce_rating_filter' => [
        ['title' => 'Hodnocení']
    ]
];

// Helper to add widget
function add_widget_to_sidebar($id_base, $instance_settings, &$widgets_array, $sidebar_id) {
    $widget_instances = get_option('widget_' . $id_base);
    if (!is_array($widget_instances)) $widget_instances = [];
    
    // Find next index
    $keys = array_keys($widget_instances);
    // Filter only numeric keys
    $numeric_keys = array_filter($keys, 'is_numeric');
    $next_index = empty($numeric_keys) ? 1 : max($numeric_keys) + 1;
    
    // Add instance
    $widget_instances[$next_index] = $instance_settings;
    $widget_instances['_multiwidget'] = 1;
    update_option('widget_' . $id_base, $widget_instances);
    
    // Add to sidebar
    $widget_id = $id_base . '-' . $next_index;
    $widgets_array[] = $widget_id;
    echo "  Added widget: $widget_id\n";
}

// Re-add widgets (clearing old ones of these types to avoid duplicates would be better, but appending is safer for now)
// Actually, let's just append the new attribute filters if they aren't there.
// But the user wants "Značka" and "Věk".

// Let's just add them.
add_widget_to_sidebar('woocommerce_layered_nav', ['title' => 'Značka', 'attribute' => 'znacka', 'query_type' => 'or', 'display_type' => 'list'], $existing_widgets, $sidebar_id);
add_widget_to_sidebar('woocommerce_layered_nav', ['title' => 'Věk / velikost', 'attribute' => 'vek-velikost', 'query_type' => 'or', 'display_type' => 'list'], $existing_widgets, $sidebar_id);

$widgets[$sidebar_id] = $existing_widgets;
update_option('sidebars_widgets', $widgets);

echo "Widgets updated.\n";
echo "Done.\n";
