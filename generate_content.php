<?php
require_once('wp-load.php');

// Configuration
$NEW_PRODUCT_COUNT = 200;

// Data Source
$brands = ['Acana', 'Brit', 'Carnilove', 'Royal Canin', 'Purina', 'Pedigree', 'Orijen', 'Taste of the Wild'];
$ages = ['Puppy', 'Adult', 'Senior'];
$sizes = ['Small Breed', 'Medium Breed', 'Large Breed', 'All Breeds'];
$flavors = ['Chicken', 'Beef', 'Lamb', 'Salmon', 'Duck', 'Turkey', 'Venison'];
$types = ['Granule', 'Konzerva', 'Pamlsky', 'Kapsička'];

// Helper to get random item
function get_random($array) {
    return $array[array_rand($array)];
}

// Helper to set attribute
function set_product_attribute($product, $taxonomy, $term_name) {
    // Ensure term exists
    if (!term_exists($term_name, $taxonomy)) {
        wp_insert_term($term_name, $taxonomy);
    }
    
    // Set term
    wp_set_object_terms($product->get_id(), $term_name, $taxonomy);
    
    // Return attribute object for WC data
    $attribute = new WC_Product_Attribute();
    $attribute->set_id(wc_attribute_taxonomy_id_by_name($taxonomy));
    $attribute->set_name($taxonomy);
    $attribute->set_options(wp_get_post_terms($product->get_id(), $taxonomy, array('fields' => 'ids')));
    $attribute->set_position(0);
    $attribute->set_visible(true);
    $attribute->set_variation(false);
    return $attribute;
}

echo "Starting content generation...\n";

// 1. Update Existing Products
$args = array('limit' => -1, 'status' => 'publish');
$existing_products = wc_get_products($args);

echo "Updating " . count($existing_products) . " existing products...\n";

foreach ($existing_products as $product) {
    $attributes = $product->get_attributes();
    
    // Assign Brand
    $brand = get_random($brands);
    $attributes['pa_znacka'] = set_product_attribute($product, 'pa_znacka', $brand);
    
    // Assign Age
    $age = get_random($ages);
    $attributes['pa_vek'] = set_product_attribute($product, 'pa_vek', $age);
    
    // Assign Size
    $size = get_random($sizes);
    $attributes['pa_velikost'] = set_product_attribute($product, 'pa_velikost', $size);
    
    $product->set_attributes($attributes);
    $product->save();
}

// 2. Generate New Products
echo "Generating $NEW_PRODUCT_COUNT new products...\n";

for ($i = 0; $i < $NEW_PRODUCT_COUNT; $i++) {
    $brand = get_random($brands);
    $flavor = get_random($flavors);
    $type = get_random($types);
    $age = get_random($ages);
    $size = get_random($sizes);
    
    $name = "$brand $flavor $type pro $age ($size)";
    $price = rand(150, 3500);
    
    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_price($price);
    $product->set_regular_price($price);
    $product->set_description("Kvalitní $type značky $brand s příchutí $flavor. Ideální pro $age psy velikosti $size.");
    $product->set_short_description("$brand $type - $flavor");
    
    // Save to get ID
    $product->save();
    
    // Set Attributes
    $attributes = [];
    $attributes['pa_znacka'] = set_product_attribute($product, 'pa_znacka', $brand);
    $attributes['pa_vek'] = set_product_attribute($product, 'pa_vek', $age);
    $attributes['pa_velikost'] = set_product_attribute($product, 'pa_velikost', $size);
    
    $product->set_attributes($attributes);
    
    // Assign Category based on Type
    $term = term_exists($type, 'product_cat');
    if (!$term) {
        $term = wp_insert_term($type, 'product_cat');
    }
    
    if ($term && !is_wp_error($term)) {
        $cat_id = is_array($term) ? $term['term_id'] : $term;
        $product->set_category_ids([$cat_id]);
    }
    
    $product->save();
    
    if (($i + 1) % 50 == 0) echo "  Generated " . ($i + 1) . " products...\n";
}

echo "Done.\n";
