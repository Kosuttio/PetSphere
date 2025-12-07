<?php
// Load WordPress environment
require_once('wp-load.php');

echo "Starting store content generation...\n";

// 1. Define Attributes and Terms
$attributes_config = [
    'pa_znacka' => [
        'name' => 'Značka',
        'terms' => ['Acana', 'Brit', 'Carnilove', 'Royal Canin', 'Purina', 'Tetra', 'Versele-Laga', 'JBL', 'Trixie']
    ],
    'pa_barva' => [
        'name' => 'Barva',
        'terms' => ['Červená', 'Modrá', 'Zelená', 'Černá', 'Bílá', 'Hnědá', 'Šedá', 'Oranžová']
    ],
    'pa_vek' => [
        'name' => 'Věk',
        'terms' => ['Junior', 'Dospělý', 'Senior']
    ],
    'pa_velikost' => [
        'name' => 'Velikost',
        'terms' => ['Malá', 'Střední', 'Velká', 'Univerzální']
    ],
    'pa_plemeno' => [
        'name' => 'Plemeno',
        'terms' => ['Německý ovčák', 'Zlatý retrívr', 'Jorkšír', 'Perská kočka', 'Mainská mývalí', 'Andulka', 'Žako', 'Křeček', 'Morče', 'Neonka', 'Betta', 'Mix']
    ]
];

// 2. Create/Verify Attributes and Terms
foreach ($attributes_config as $slug => $data) {
    $attribute_id = wc_attribute_taxonomy_id_by_name($data['name']); // Try by name first
    
    if (!$attribute_id) {
        // Try by slug if name failed (remove pa_)
        $slug_clean = str_replace('pa_', '', $slug);
        $attribute_id = wc_attribute_taxonomy_id_by_name($slug_clean);
    }

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
    } else {
        // echo "Attribute {$data['name']} already exists.\n";
    }
    
    // Register taxonomy so we can add terms immediately
    register_taxonomy($slug, apply_filters('woocommerce_taxonomy_objects_' . $slug, array('product')), array());

    // Create Terms
    foreach ($data['terms'] as $term_name) {
        if (!term_exists($term_name, $slug)) {
            wp_insert_term($term_name, $slug);
            // echo "  Added term: $term_name\n";
        }
    }
}

// 3. Define Categories to populate
$categories_slugs = [
    'psy', 
    'kocky', 
    'akvaristika', 
    'hlodavci-a-mala-zvirata', 
    'ptaci'
];

// Ensure categories exist
$category_ids = [];
foreach ($categories_slugs as $slug) {
    $term = get_term_by('slug', $slug, 'product_cat');
    if ($term) {
        $category_ids[$slug] = $term->term_id;
    } else {
        // Create if missing (though user said they exist or asked to create them)
        $new_term = wp_insert_term(ucfirst(str_replace('-', ' ', $slug)), 'product_cat', ['slug' => $slug]);
        if (!is_wp_error($new_term)) {
            $category_ids[$slug] = $new_term['term_id'];
            echo "Created category: $slug\n";
        }
    }
}

// 4. Generate 200 New Products
$products_to_create = 200;
$product_types = ['Krmivo', 'Hračka', 'Příslušenství', 'Vitamíny', 'Pelíšek', 'Klec/Akvárium'];

echo "Generating $products_to_create new products...\n";

for ($i = 0; $i < $products_to_create; $i++) {
    $cat_slug = $categories_slugs[array_rand($categories_slugs)];
    $cat_id = $category_ids[$cat_slug];
    
    $brand = $attributes_config['pa_znacka']['terms'][array_rand($attributes_config['pa_znacka']['terms'])];
    $type = $product_types[array_rand($product_types)];
    
    $title = "$brand $type pro " . ucfirst(str_replace('-', ' ', $cat_slug)) . " " . rand(100, 999);
    
    $product = new WC_Product_Simple();
    $product->set_name($title);
    $product->set_regular_price(rand(50, 2000));
    $product->set_category_ids([$cat_id]);
    $product->set_status('publish');
    $product->set_short_description("Lorem ipsum dolor sit amet, consectetur adipiscing elit.");
    
    // Assign Attributes
    $attributes_data = [];
    
    foreach ($attributes_config as $slug => $data) {
        // 70% chance to have an attribute
        if (rand(0, 100) > 30) {
            $term_name = $data['terms'][array_rand($data['terms'])];
            
            $attribute = new WC_Product_Attribute();
            $attribute->set_id(wc_attribute_taxonomy_id_by_name($slug));
            $attribute->set_name($slug);
            $attribute->set_options([$term_name]);
            $attribute->set_position(0);
            $attribute->set_visible(true);
            $attribute->set_variation(false);
            
            $attributes_data[] = $attribute;
            
            // We also need to set the term on the object
            wp_set_object_terms($product->get_id(), $term_name, $slug, true);
        }
    }
    
    $product->set_attributes($attributes_data);
    $product->save();
    
    // Need to set terms again after save because ID is needed? 
    // Actually set_attributes handles the meta, but wp_set_object_terms handles the taxonomy relationship.
    // Let's do it after save to be sure.
    foreach ($attributes_config as $slug => $data) {
        // Re-roll or reuse? Let's reuse logic if we stored it, but for simplicity let's just pick random again or skip.
        // Better: Just rely on the fact we need to set terms for filtering.
        $term_name = $data['terms'][array_rand($data['terms'])];
        wp_set_object_terms($product->get_id(), $term_name, $slug, true);
    }
    
    if ($i % 20 == 0) echo ".";
}
echo "\nNew products generated.\n";

// 5. Update Existing Products (Assign attributes if missing)
echo "Updating existing products...\n";
$args = array(
    'limit' => -1,
    'status' => 'publish',
    'return' => 'ids',
);
$existing_products = wc_get_products($args);

foreach ($existing_products as $product_id) {
    $product = wc_get_product($product_id);
    $changed = false;
    
    $current_attributes = $product->get_attributes();
    
    foreach ($attributes_config as $slug => $data) {
        // If attribute missing, add it
        if (!isset($current_attributes[$slug])) {
            $term_name = $data['terms'][array_rand($data['terms'])];
            wp_set_object_terms($product_id, $term_name, $slug, true);
            
            // Note: To properly save as attribute object on product, we should construct WC_Product_Attribute
            // But for filtering, the taxonomy term is the most important part.
            // However, to show in "Additional Information" tab, we need the attribute object.
            
            // Let's do it properly for a few key ones
            $attribute = new WC_Product_Attribute();
            $attribute->set_id(wc_attribute_taxonomy_id_by_name($slug));
            $attribute->set_name($slug);
            $attribute->set_options([$term_name]);
            $attribute->set_visible(true);
            $attribute->set_variation(false);
            
            $current_attributes[$slug] = $attribute;
            $changed = true;
        }
    }
    
    if ($changed) {
        $product->set_attributes($current_attributes);
        $product->save();
    }
}

echo "All done.\n";
