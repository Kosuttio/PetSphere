<?php
require_once('wp-load.php');

echo "Starting category and attribute localization...\n";

// 1. Define Categories Structure (Parent -> Children)
$categories_structure = [
    'Psi' => ['Granule', 'Konzervy', 'Pamlsky', 'Hračky', 'Vodítka a obojky', 'Pelíšky'],
    'Kočky' => ['Granule', 'Konzervy', 'Kapsičky', 'Pamlsky', 'Hračky', 'Škrabadla', 'Toalety'],
    'Ptáci' => ['Krmivo', 'Klec', 'Hračky', 'Vitamíny'],
    'Akvaristika' => ['Krmivo', 'Technika', 'Chemie', 'Dekorace'],
    'Hlodavci a malá zvířata' => ['Krmivo', 'Klec', 'Podestýlky', 'Hračky']
];

// Helper to create category hierarchy
$cat_map = []; // Name -> ID

foreach ($categories_structure as $parent_name => $subcats) {
    // Create Parent
    $parent_term = term_exists($parent_name, 'product_cat');
    if (!$parent_term) {
        $parent_term = wp_insert_term($parent_name, 'product_cat');
    }
    $parent_id = is_array($parent_term) ? $parent_term['term_id'] : $parent_term;
    $cat_map[$parent_name] = $parent_id;
    
    echo "Category: $parent_name (ID: $parent_id)\n";

    // Create Children
    foreach ($subcats as $sub_name) {
        $child_term = term_exists($sub_name, 'product_cat', $parent_id); // Check if exists under parent
        if (!$child_term) {
            // Check if exists globally (might be shared name like 'Krmivo') - actually Woo allows same name different slug/parent
            // But wp_insert_term might fail if slug exists. Let's append parent slug if needed or rely on WP handling.
            $child_term = wp_insert_term($sub_name, 'product_cat', ['parent' => $parent_id]);
        }
        
        if (!is_wp_error($child_term)) {
            $child_id = is_array($child_term) ? $child_term['term_id'] : $child_term;
            // Store as "Parent > Child" key for easy lookup
            $cat_map["$parent_name > $sub_name"] = $child_id;
        }
    }
}

// 2. Translate Attributes (Rename Terms)
$attribute_translations = [
    'pa_vek' => [
        'Puppy' => 'Štěně',
        'Adult' => 'Dospělý',
        'Senior' => 'Senior'
    ],
    'pa_velikost' => [
        'Small Breed' => 'Malá plemena',
        'Medium Breed' => 'Střední plemena',
        'Large Breed' => 'Velká plemena',
        'All Breeds' => 'Všechna plemena'
    ]
];

foreach ($attribute_translations as $taxonomy => $trans_map) {
    foreach ($trans_map as $english => $czech) {
        $term = get_term_by('name', $english, $taxonomy);
        if ($term && !is_wp_error($term)) {
            wp_update_term($term->term_id, $taxonomy, [
                'name' => $czech,
                'slug' => sanitize_title($czech)
            ]);
            echo "Renamed attribute term: $english -> $czech\n";
        }
    }
}

// 3. Fix 'Uncategorized' and Assign Categories to Products
$uncat_term = get_term_by('name', 'Uncategorized', 'product_cat');
$uncat_id = $uncat_term ? $uncat_term->term_id : 0;

// Get all products
$products = wc_get_products(['limit' => -1]);

echo "Processing " . count($products) . " products...\n";

foreach ($products as $product) {
    $p_id = $product->get_id();
    $p_name = $product->get_name();
    $cats = $product->get_category_ids();
    
    $needs_save = false;
    $new_cats = [];

    // Determine Category based on Name/Description keywords
    // Default to 'Psi' if unknown for now, or random logic
    
    $target_parent = 'Psi'; // Default
    $target_sub = 'Granule'; // Default

    // Simple keyword matching
    if (stripos($p_name, 'kočk') !== false || stripos($p_name, 'cat') !== false) {
        $target_parent = 'Kočky';
    } elseif (stripos($p_name, 'pták') !== false || stripos($p_name, 'bird') !== false) {
        $target_parent = 'Ptáci';
    } elseif (stripos($p_name, 'ryb') !== false || stripos($p_name, 'fish') !== false || stripos($p_name, 'akva') !== false) {
        $target_parent = 'Akvaristika';
    } elseif (stripos($p_name, 'hlod') !== false || stripos($p_name, 'králík') !== false || stripos($p_name, 'křeček') !== false) {
        $target_parent = 'Hlodavci a malá zvířata';
    }

    // Subcategory matching
    if (stripos($p_name, 'konzerv') !== false) $target_sub = 'Konzervy';
    elseif (stripos($p_name, 'paml') !== false) $target_sub = 'Pamlsky';
    elseif (stripos($p_name, 'hrač') !== false) $target_sub = 'Hračky';
    elseif (stripos($p_name, 'kaps') !== false) $target_sub = 'Kapsičky';
    
    // If product has Uncategorized or no category, assign new one
    if (empty($cats) || in_array($uncat_id, $cats)) {
        // Find ID
        $key = "$target_parent > $target_sub";
        if (isset($cat_map[$key])) {
            $new_cats[] = $cat_map[$key];
            // Also add parent? Woo usually handles child implies parent, but good to be explicit or just child.
            // Let's just add child.
        } else {
            // Fallback to parent
            $new_cats[] = $cat_map[$target_parent];
        }
        
        $product->set_category_ids($new_cats);
        $needs_save = true;
    } else {
        // Product has categories. Check if they are the old English ones (Granule, Konzerva generated previously were top level)
        // We should move them to proper hierarchy.
        // The previous script created categories like 'Granule', 'Konzerva' at top level.
        // Let's move them or reassign.
        
        // Actually, let's just re-assign everyone to the new structure to be clean.
        // Randomly distribute among the new structure if the name doesn't specify.
        
        // For the sake of this task, let's re-run the logic for ALL products to ensure they are in the new tree.
        $key = "$target_parent > $target_sub";
        if (isset($cat_map[$key])) {
            $product->set_category_ids([$cat_map[$key]]);
            $needs_save = true;
        }
    }

    if ($needs_save) {
        $product->save();
        // echo "Updated category for product $p_id ($p_name)\n";
    }
}

// Rename Uncategorized to something Czech just in case
if ($uncat_term) {
    wp_update_term($uncat_id, 'product_cat', ['name' => 'Nezařazeno', 'slug' => 'nezarazeno']);
}

echo "Done.\n";
