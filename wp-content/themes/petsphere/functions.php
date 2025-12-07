<?php
/**
 * PetSphere functions and definitions
 */

// Enqueue custom styles for filters
add_action( 'wp_enqueue_scripts', 'petsphere_enqueue_filter_styles' );
function petsphere_enqueue_filter_styles() {
    wp_enqueue_style( 'petsphere-filters', get_template_directory_uri() . '/style-filters.css', array(), '1.0' );
    wp_enqueue_style( 'petsphere-login', get_template_directory_uri() . '/style-login.css', array(), '1.0' );
    if ( is_cart() ) {
        wp_enqueue_style( 'petsphere-cart', get_template_directory_uri() . '/style-cart.css', array(), '1.0' );
    }
    wp_enqueue_script( 'petsphere-filters-js', get_template_directory_uri() . '/js/filters.js', array(), '1.0', true );
}

// Disable custom filter JS: use WooCommerce default behavior
add_action( 'wp_enqueue_scripts', function() {}, 20 );

function petsphere_customize_register( $wp_customize ) {
    // 1. Sekce pro barvy (rozšíření existující nebo nová)
    $wp_customize->add_setting( 'petsphere_primary_color', array(
        'default'   => '#0078d4',
        'transport' => 'refresh',
    ) );

    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_primary_color', array(
        'label'      => __( 'Hlavní barva (Brand Color)', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_primary_color',
    ) ) );

    // Barva tlačítek (Pozadí)
    $wp_customize->add_setting( 'petsphere_button_bg_color', array(
        'default'   => '#0078d4',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_button_bg_color', array(
        'label'      => __( 'Barva tlačítek (Pozadí)', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_button_bg_color',
    ) ) );

    // Barva tlačítek (Text)
    $wp_customize->add_setting( 'petsphere_button_text_color', array(
        'default'   => '#ffffff',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_button_text_color', array(
        'label'      => __( 'Barva tlačítek (Text)', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_button_text_color',
    ) ) );

    // Sekundární barva (např. pro ceny, akce)
    $wp_customize->add_setting( 'petsphere_secondary_color', array(
        'default'   => '#ffb900',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_secondary_color', array(
        'label'      => __( 'Sekundární barva (Ceny, Akce)', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_secondary_color',
    ) ) );

    // Hero Sekce - Pozadí
    $wp_customize->add_setting( 'petsphere_hero_bg_color', array(
        'default'   => '#1a5d48',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_hero_bg_color', array(
        'label'      => __( 'Hero Sekce - Pozadí', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_hero_bg_color',
    ) ) );

    // Hero Sekce - Kruh
    $wp_customize->add_setting( 'petsphere_hero_circle_color', array(
        'default'   => '#81c784',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_hero_circle_color', array(
        'label'      => __( 'Hero Sekce - Kruh pod psem', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_hero_circle_color',
    ) ) );

    // Mobilní Menu - Barva ikony
    $wp_customize->add_setting( 'petsphere_mobile_menu_color', array(
        'default'   => '#333333',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_mobile_menu_color', array(
        'label'      => __( 'Mobilní Menu - Barva ikony', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_mobile_menu_color',
    ) ) );

    // Tlačítko Množství Plus (+)
    $wp_customize->add_setting( 'petsphere_qty_plus_bg', array(
        'default'   => '#28a745',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_qty_plus_bg', array(
        'label'      => __( 'Tlačítko Množství Plus (+)', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_qty_plus_bg',
    ) ) );

    // Tlačítko Množství Mínus (-)
    $wp_customize->add_setting( 'petsphere_qty_minus_bg', array(
        'default'   => '#dc3545',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_qty_minus_bg', array(
        'label'      => __( 'Tlačítko Množství Mínus (-)', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_qty_minus_bg',
    ) ) );

    // Tlačítko Množství Plus (+) - Text
    $wp_customize->add_setting( 'petsphere_qty_plus_text_color', array(
        'default'   => '#ffffff',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_qty_plus_text_color', array(
        'label'      => __( 'Tlačítko Množství Plus (+) - Text', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_qty_plus_text_color',
    ) ) );

    // Tlačítko Množství Mínus (-) - Text
    $wp_customize->add_setting( 'petsphere_qty_minus_text_color', array(
        'default'   => '#ffffff',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'petsphere_qty_minus_text_color', array(
        'label'      => __( 'Tlačítko Množství Mínus (-) - Text', 'petsphere' ),
        'section'    => 'colors',
        'settings'   => 'petsphere_qty_minus_text_color',
    ) ) );

    // 2. Sekce pro Texty o technologiích (AI, Azure, atd.)
    $wp_customize->add_section( 'petsphere_tech_section', array(
        'title'      => __( 'PetSphere Technologie & Info', 'petsphere' ),
        'priority'   => 30,
    ) );

    // Nastavení: Popis infrastruktury
    $wp_customize->add_setting( 'petsphere_infra_text', array(
        'default'   => 'Platforma PetSphere je provozována v cloudové infrastruktuře Microsoft Azure, kde se nachází klíčové komponenty jako databázové servery, API rozhraní a AI moduly.',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( 'petsphere_infra_text', array(
        'label'    => __( 'Popis infrastruktury (Azure)', 'petsphere' ),
        'section'  => 'petsphere_tech_section',
        'type'     => 'textarea',
    ) );

    // Nastavení: Popis týmu
    $wp_customize->add_setting( 'petsphere_team_text', array(
        'default'   => 'Společnost má více než třicet zaměstnanců pracujících ve vedení firmy, produktovém a provozním oddělení, marketingu, financích a IT.',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( 'petsphere_team_text', array(
        'label'    => __( 'Popis týmu', 'petsphere' ),
        'section'  => 'petsphere_tech_section',
        'type'     => 'textarea',
    ) );

    // Nastavení: Popis AI
    $wp_customize->add_setting( 'petsphere_ai_text', array(
        'default'   => 'Využíváme AI moduly pro personalizovaná doporučení a kybernetickou bezpečnost.',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( 'petsphere_ai_text', array(
        'label'    => __( 'Popis AI funkcí', 'petsphere' ),
        'section'  => 'petsphere_tech_section',
        'type'     => 'textarea',
    ) );
}
add_action( 'customize_register', 'petsphere_customize_register' );

// Výpis CSS proměnných do hlavičky
function petsphere_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo get_theme_mod( 'petsphere_primary_color', '#0078d4' ); ?>;
            --btn-bg: <?php echo get_theme_mod( 'petsphere_button_bg_color', '#0078d4' ); ?>;
            --btn-text: <?php echo get_theme_mod( 'petsphere_button_text_color', '#ffffff' ); ?>;
            --secondary-color: <?php echo get_theme_mod( 'petsphere_secondary_color', '#ffb900' ); ?>;
            --hero-bg: <?php echo get_theme_mod( 'petsphere_hero_bg_color', '#1a5d48' ); ?>;
            --hero-circle-bg: <?php echo get_theme_mod( 'petsphere_hero_circle_color', '#81c784' ); ?>;
            --mobile-menu-color: <?php echo get_theme_mod( 'petsphere_mobile_menu_color', '#333333' ); ?>;
            --qty-plus-bg: <?php echo get_theme_mod( 'petsphere_qty_plus_bg', '#28a745' ); ?>;
            --qty-minus-bg: <?php echo get_theme_mod( 'petsphere_qty_minus_bg', '#dc3545' ); ?>;
            --qty-plus-text: <?php echo get_theme_mod( 'petsphere_qty_plus_text_color', '#ffffff' ); ?>;
            --qty-minus-text: <?php echo get_theme_mod( 'petsphere_qty_minus_text_color', '#ffffff' ); ?>;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'petsphere_customizer_css' );

// Načtení skriptů
function petsphere_scripts() {
    wp_enqueue_script( 'petsphere-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0', true );
    
    // Custom Shop Scripts (Toast, UI tweaks)
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_script( 'petsphere-custom-shop', get_template_directory_uri() . '/js/custom-shop.js', array( 'jquery' ), '1.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'petsphere_scripts' );

// Podpora pro logo
function petsphere_setup() {
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'title-tag' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'woocommerce' );

    // Registrace menu
    register_nav_menus( array(
        'primary' => __( 'Hlavní menu', 'petsphere' ),
        'mobile'  => __( 'Mobilní menu', 'petsphere' ),
    ) );
}
add_action( 'after_setup_theme', 'petsphere_setup' );

// Registrace Sidebaru pro Shop
function petsphere_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Shop Sidebar (Filtry)', 'petsphere' ),
        'id'            => 'shop-sidebar',
        'description'   => __( 'Sem vložte widgety pro filtrování (Cena, Atributy, atd.)', 'petsphere' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'petsphere_widgets_init' );

// --- Oddělení Kategorií a Produktů ---
add_action( 'init', 'petsphere_separate_categories_init' );
function petsphere_separate_categories_init() {
    // Odstranit kategorie z hlavního produktového loopu
    remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' );
    
    // Přidat kategorie PŘED loop (do vlastního kontejneru)
    add_action( 'woocommerce_before_shop_loop', 'petsphere_render_custom_categories', 10 );
}

function petsphere_render_custom_categories() {
    // Zobrazit jen na shopu nebo v kategorii
    if ( ! is_shop() && ! is_product_category() ) {
        return;
    }

    // Získat kategorie v pevném pořadí
    $cat_args = array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => is_product_category() ? get_queried_object_id() : 0,
        'meta_key'   => 'order',
        'orderby'    => 'meta_value_num',
        'order'      => 'ASC',
    );
    $categories = get_terms( $cat_args );

    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        echo '<section class="petsphere-categories-section">';
        // echo '<h2 class="section-title">' . __( 'Kategorie', 'petsphere' ) . '</h2>'; // Removed title
        
        // Použijeme standardní Woo funkci pro výpis, ale s vlastními wrapper třídami
        woocommerce_output_product_categories( array(
            'before'    => '<ul class="petsphere-categories-grid">',
            'after'     => '</ul>',
            'parent_id' => is_product_category() ? get_queried_object_id() : 0,
        ) );
        
        echo '</section>';
        echo '<hr class="shop-divider">';
        // echo '<h2 class="section-title">' . __( 'Produkty', 'petsphere' ) . '</h2>'; // Removed title
    }
}

// --- Skrýt nadpis stránky na Shopu ---
add_filter( 'woocommerce_show_page_title', 'petsphere_hide_shop_title' );
function petsphere_hide_shop_title( $title ) {
    if ( is_shop() ) return false;
    return $title;
}

// --- Text pod hlavičkou na stránce obchodu ---
add_action( 'woocommerce_before_main_content', 'petsphere_shop_description', 20 );
function petsphere_shop_description() {
    if ( is_shop() ) {
        echo '<div class="petsphere-shop-header-text" style="margin-bottom: 30px;">';
        echo '<h1 style="font-size: 2.5rem; color: var(--primary-color); margin-bottom: 10px;">' . __( 'Vše pro vaše mazlíčky', 'petsphere' ) . '</h1>';
        echo '<p style="font-size: 1.1rem; color: #666;">' . __( 'To nejlepší pro vaše chlupaté přátele', 'petsphere' ) . '</p>';
        echo '</div>';
    }
}

// --- Řazení podle popularity na hlavní stránce obchodu ---
// add_action( 'woocommerce_product_query', 'petsphere_shop_best_sellers_query' );
function petsphere_shop_best_sellers_query( $q ) {
    if ( ! is_admin() && $q->is_main_query() && $q->is_shop() ) {
        // $q->set( 'orderby', 'popularity' );
        // $q->set( 'order', 'DESC' );
    }
}

// --- Vlastní kontaktní formulář (Shortcode: [petsphere_contact_form]) ---
function petsphere_contact_form_shortcode() {
    // Zpracování odeslání formuláře
    if ( isset( $_POST['petsphere_contact_submit'] ) ) {
        $name    = sanitize_text_field( $_POST['contact_name'] );
        $email   = sanitize_email( $_POST['contact_email'] );
        $message = sanitize_textarea_field( $_POST['contact_message'] );
        $to      = get_option( 'admin_email' ); // Email admina
        $subject = 'Nová zpráva z PetSphere: ' . $name;
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        $body    = "Jméno: $name<br>Email: $email<br><br>Zpráva:<br>$message";

        // Odeslání emailu
        wp_mail( $to, $subject, $body, $headers );
        
        echo '<div class="petsphere-alert success">Děkujeme za vaši zprávu. Brzy se vám ozveme.</div>';
    }

    // HTML formuláře
    ob_start();
    ?>
    <form method="post" action="" class="petsphere-contact-form">
        <div class="form-group">
            <label for="contact_name">Jméno:</label>
            <input type="text" name="contact_name" id="contact_name" required>
        </div>
        <div class="form-group">
            <label for="contact_email">Email:</label>
            <input type="email" name="contact_email" id="contact_email" required>
        </div>
        <div class="form-group">
            <label for="contact_message">Zpráva:</label>
            <textarea name="contact_message" id="contact_message" rows="5" required></textarea>
        </div>
        <button type="submit" name="petsphere_contact_submit" class="btn-primary">Odeslat zprávu</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode( 'petsphere_contact_form', 'petsphere_contact_form_shortcode' );

// --- Custom Add to Cart Button with Quantity (Loop) ---
add_filter( 'woocommerce_loop_add_to_cart_link', 'petsphere_custom_add_to_cart_button', 10, 3 );
function petsphere_custom_add_to_cart_button( $button, $product, $args ) {
    // Check if product is in cart and get quantity
    $in_cart_qty = 0;
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            if ( $cart_item['product_id'] == $product->get_id() ) {
                $in_cart_qty += $cart_item['quantity'];
            }
        }
    }

    $class_hidden_btn = ($in_cart_qty > 0) ? 'style="display:none;"' : '';
    $class_hidden_qty = ($in_cart_qty > 0) ? '' : 'style="display:none;"';
    $qty_val = ($in_cart_qty > 0) ? $in_cart_qty : 1;

    // Modify the standard button to include our class for targeting
    $button = str_replace( 'class="', 'class="petsphere-add-btn ', $button );
    $button = str_replace( '<a ', '<a ' . $class_hidden_btn . ' ', $button );

    // Build the +/- control
    $qty_control = sprintf(
        '<div class="petsphere-qty-control" %s data-product_id="%s">
            <button type="button" class="qty-btn minus">-</button>
            <span class="qty-val">%s</span>
            <button type="button" class="qty-btn plus">+</button>
        </div>',
        $class_hidden_qty,
        $product->get_id(),
        $qty_val
    );

    return $button . $qty_control;
}

// --- AJAX Handler for Quantity Update ---
add_action( 'wp_ajax_petsphere_update_qty', 'petsphere_update_qty' );
add_action( 'wp_ajax_nopriv_petsphere_update_qty', 'petsphere_update_qty' );

function petsphere_update_qty() {
    $product_id = intval( $_POST['product_id'] );
    $change = intval( $_POST['change'] ); // +1 or -1

    if ( ! $product_id ) wp_send_json_error();

    $cart = WC()->cart;
    $cart_id = $cart->generate_cart_id( $product_id );
    $cart_item_key = $cart->find_product_in_cart( $cart_id );

    if ( $cart_item_key ) {
        $current_qty = $cart->get_cart_item( $cart_item_key )['quantity'];
        $new_qty = $current_qty + $change;

        if ( $new_qty <= 0 ) {
            $cart->remove_cart_item( $cart_item_key );
            wp_send_json_success( array( 
                'new_qty' => 0,
                'cart_count' => $cart->get_cart_contents_count()
            ) );
        } else {
            $cart->set_quantity( $cart_item_key, $new_qty );
            wp_send_json_success( array( 
                'new_qty' => $new_qty,
                'cart_count' => $cart->get_cart_contents_count()
            ) );
        }
    } else if ( $change > 0 ) {
        // Item not in cart, add it
        $cart->add_to_cart( $product_id, 1 );
        wp_send_json_success( array( 
            'new_qty' => 1,
            'cart_count' => $cart->get_cart_contents_count()
        ) );
    }

    wp_send_json_error();
}

// --- Cart Fragments (AJAX Update) ---
add_filter( 'woocommerce_add_to_cart_fragments', 'petsphere_cart_count_fragments', 10, 1 );
function petsphere_cart_count_fragments( $fragments ) {
    ob_start();
    ?>
    <span class="cart-count-badge"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.cart-count-badge'] = ob_get_clean();
    return $fragments;
}

// --- Search Autocomplete ---
add_action( 'wp_ajax_petsphere_search_autocomplete', 'petsphere_search_autocomplete' );
add_action( 'wp_ajax_nopriv_petsphere_search_autocomplete', 'petsphere_search_autocomplete' );

function petsphere_search_autocomplete() {
    $term = sanitize_text_field( $_GET['term'] );
    
    if ( empty( $term ) ) {
        wp_send_json_error();
    }

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        's'              => $term,
    );

    $query = new WP_Query( $args );
    $results = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
            $results[] = array(
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'price' => $product->get_price_html(),
                'image' => get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success( $results );
}

// Pass AJAX URL to JS
function petsphere_localize_script() {
    wp_localize_script( 'petsphere-custom-shop', 'petsphere_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    
    // Enqueue Search Autocomplete JS
    wp_enqueue_script( 'petsphere-search-autocomplete', get_template_directory_uri() . '/js/search-autocomplete.js', array('jquery'), '1.0', true );
    wp_localize_script( 'petsphere-search-autocomplete', 'petsphere_search', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'search_nonce' )
    ) );
}
add_action( 'wp_enqueue_scripts', 'petsphere_localize_script' );

// --- Mobile Menu Filters ---
add_filter( 'wp_nav_menu_items', 'petsphere_mobile_menu_extras', 10, 2 );
function petsphere_mobile_menu_extras( $items, $args ) {
    if ( $args->theme_location == 'mobile' ) {
        // 1. Add Cart Count to "Košík" item
        if ( class_exists( 'WooCommerce' ) ) {
            $count = WC()->cart->get_cart_contents_count();
            // Use regex to be safer or just simple replace if we are sure about the text
            $items = str_replace( '>Košík<', '>Košík (' . $count . ')<', $items );
        }

        // 2. Change "Můj účet" to "Přihlášení / Registrace" if not logged in
        if ( ! is_user_logged_in() ) {
            $items = str_replace( '>Můj účet<', '>Přihlášení / Registrace<', $items );
        }
    }
    return $items;
}

// add_action( 'wp_enqueue_scripts', 'petsphere_localize_script' ); // Already added above

// --- Oprava zobrazení produktů ---
// Vynutit zobrazení produktů i kategorií, aby WooCommerce načetl produkty do loopu.
// Protože jsme odstranili 'woocommerce_maybe_show_product_subcategories', defaultní kategorie se nezobrazí,
// ale produkty ano. Naše vlastní kategorie se zobrazí přes 'woocommerce_before_shop_loop'.
add_filter( 'option_woocommerce_shop_page_display', 'petsphere_force_shop_display' );
function petsphere_force_shop_display( $value ) {
    return 'both'; 
}
