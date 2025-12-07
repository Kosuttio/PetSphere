<?php
require_once('wp-load.php');

echo "Splitting 'Věk / velikost' into 'Věk' and 'Velikost'...\n";

// 1. Define New Attributes
$new_attributes = [
    'pa_vek' => [
        'name' => 'Věk',
        'terms' => ['Adult', 'Puppy', 'Senior']
    ],
    'pa_velikost' => [
        'name' => 'Velikost',
        'terms' => ['Large Breed', 'Small Breed', 'Medium Breed']
    ]
];

// 2. Create New Attributes
foreach ($new_attributes as $slug => $data) {
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
    }
    
    // Create Terms
    foreach ($data['terms'] as $term_name) {
        if (!term_exists($term_name, $slug)) {
            wp_insert_term($term_name, $slug);
        }
    }
}

// 3. Migrate Products
$args = array('limit' => -1, 'status' => 'publish');
$products = wc_get_products($args);

foreach ($products as $product) {
    $product_id = $product->get_id();
    $changed = false;
    
    // Get current terms for the old attribute
    $old_terms = wp_get_post_terms($product_id, 'pa_vek-velikost', array('fields' => 'names'));
    
    if (!empty($old_terms) && !is_wp_error($old_terms)) {
        $new_attrs_data = [];
        
        foreach ($old_terms as $term_name) {
            // Map to Age
            if (in_array($term_name, $new_attributes['pa_vek']['terms'])) {
                wp_set_object_terms($product_id, $term_name, 'pa_vek', true);
                $changed = true;
            }
            // Map to Size
            if (in_array($term_name, $new_attributes['pa_velikost']['terms'])) {
                wp_set_object_terms($product_id, $term_name, 'pa_velikost', true);
                $changed = true;
            }
        }
        
        // Also assign random if missing (since we are mocking data)
        if (!$changed) {
             // Assign random age
             $rand_age = $new_attributes['pa_vek']['terms'][array_rand($new_attributes['pa_vek']['terms'])];
             wp_set_object_terms($product_id, $rand_age, 'pa_vek', true);
             
             // Assign random size
             $rand_size = $new_attributes['pa_velikost']['terms'][array_rand($new_attributes['pa_velikost']['terms'])];
             wp_set_object_terms($product_id, $rand_size, 'pa_velikost', true);
             $changed = true;
        }
    } else {
         // Assign random if no old terms
         $rand_age = $new_attributes['pa_vek']['terms'][array_rand($new_attributes['pa_vek']['terms'])];
         wp_set_object_terms($product_id, $rand_age, 'pa_vek', true);
         
         $rand_size = $new_attributes['pa_velikost']['terms'][array_rand($new_attributes['pa_velikost']['terms'])];
         wp_set_object_terms($product_id, $rand_size, 'pa_velikost', true);
         $changed = true;
    }

    if ($changed) {
        // Re-save attributes metadata
        $attributes = $product->get_attributes();
        
        // Add new attributes objects
        foreach (['pa_vek', 'pa_velikost'] as $slug) {
            $term_ids = wp_get_post_terms($product_id, $slug, array('fields' => 'ids'));
            if (!empty($term_ids)) {
                $attribute = new WC_Product_Attribute();
                $attribute->set_id(wc_attribute_taxonomy_id_by_name($slug));
                $attribute->set_name($slug);
                $attribute->set_options($term_ids);
                $attribute->set_position(0);
                $attribute->set_visible(true);
                $attribute->set_variation(false);
                $attributes[$slug] = $attribute;
            }
        }
        
        // Remove old attribute
        unset($attributes['pa_vek-velikost']);
        
        $product->set_attributes($attributes);
        $product->save();
        echo "Updated Product $product_id\n";
    }
}

// 4. Update Sidebar Widgets
$sidebar_id = 'shop-sidebar';
$widgets = get_option('sidebars_widgets');
$existing_widgets = isset($widgets[$sidebar_id]) ? $widgets[$sidebar_id] : [];

// Remove old 'Věk / velikost' widget if possible (hard to identify by ID without parsing, but we can just append new ones and user can clean up, or we rebuild)
// Let's rebuild the list to be clean: Price, Brand, Age, Size, Rating.

$new_sidebar_order = [];

// Helper to find or create widget
function get_or_create_widget($id_base, $settings) {
    $instances = get_option('widget_' . $id_base);
    if (!is_array($instances)) $instances = [];
    
    // Check if identical instance exists
    foreach ($instances as $key => $instance) {
        if (!is_numeric($key)) continue;
        if ($instance == $settings) {
            return $id_base . '-' . $key;
        }
    }
    
    // Create new
    $keys = array_keys($instances);
    $numeric_keys = array_filter($keys, 'is_numeric');
    $next_index = empty($numeric_keys) ? 1 : max($numeric_keys) + 1;
    
    $instances[$next_index] = $settings;
    $instances['_multiwidget'] = 1;
    update_option('widget_' . $id_base, $instances);
    
    return $id_base . '-' . $next_index;
}

// 1. Price Filter
$new_sidebar_order[] = get_or_create_widget('woocommerce_price_filter', ['title' => 'Filtrovat podle ceny']);

// 2. Brand (Značka)
$new_sidebar_order[] = get_or_create_widget('woocommerce_layered_nav', [
    'title' => 'Značka', 
    'attribute' => 'znacka', 
    'query_type' => 'or', 
    'display_type' => 'list'
]);

// 3. Age (Věk)
$new_sidebar_order[] = get_or_create_widget('woocommerce_layered_nav', [
    'title' => 'Věk', 
    'attribute' => 'vek', 
    'query_type' => 'or', 
    'display_type' => 'list'
]);

// 4. Size (Velikost)
$new_sidebar_order[] = get_or_create_widget('woocommerce_layered_nav', [
    'title' => 'Velikost', 
    'attribute' => 'velikost', 
    'query_type' => 'or', 
    'display_type' => 'list'
]);

// 5. Rating
$new_sidebar_order[] = get_or_create_widget('woocommerce_rating_filter', ['title' => 'Hodnocení']);

$widgets[$sidebar_id] = $new_sidebar_order;
update_option('sidebars_widgets', $widgets);

echo "Sidebar updated with separated widgets.\n";
