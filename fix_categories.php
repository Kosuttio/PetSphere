<?php
require_once('wp-load.php');

echo "Fixing 'Psi' vs 'Psy' category...\n";

$wrong_cat_name = 'Psi';
$correct_cat_name = 'Psy';

$wrong_term = get_term_by('name', $wrong_cat_name, 'product_cat');
$correct_term = get_term_by('name', $correct_cat_name, 'product_cat');

if (!$wrong_term) {
    echo "Category '$wrong_cat_name' not found. Nothing to do.\n";
    exit;
}

if (!$correct_term) {
    echo "Category '$correct_cat_name' not found. Renaming '$wrong_cat_name' to '$correct_cat_name'.\n";
    wp_update_term($wrong_term->term_id, 'product_cat', ['name' => $correct_cat_name, 'slug' => sanitize_title($correct_cat_name)]);
    exit;
}

$wrong_id = $wrong_term->term_id;
$correct_id = $correct_term->term_id;

echo "Moving content from '$wrong_cat_name' (ID: $wrong_id) to '$correct_cat_name' (ID: $correct_id)...\n";

// 1. Move Subcategories
$children = get_terms([
    'taxonomy' => 'product_cat',
    'parent' => $wrong_id,
    'hide_empty' => false
]);

foreach ($children as $child) {
    echo "Moving subcategory '{$child->name}' (ID: {$child->term_id}) to parent '$correct_cat_name'...\n";
    wp_update_term($child->term_id, 'product_cat', ['parent' => $correct_id]);
}

// 2. Move Products
$products = wc_get_products(['category' => [$wrong_cat_name], 'limit' => -1]);

foreach ($products as $product) {
    $cats = $product->get_category_ids();
    
    // Remove wrong ID, add correct ID
    if (($key = array_search($wrong_id, $cats)) !== false) {
        unset($cats[$key]);
    }
    if (!in_array($correct_id, $cats)) {
        $cats[] = $correct_id;
    }
    
    $product->set_category_ids($cats);
    $product->save();
}

echo "Moved " . count($products) . " products.\n";

// 3. Delete Wrong Category
echo "Deleting '$wrong_cat_name'...\n";
wp_delete_term($wrong_id, 'product_cat');

echo "Done.\n";
