<?php
/**
 * PetSphere functions and definitions
 */

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
        }
    </style>
    <?php
}
add_action( 'wp_head', 'petsphere_customizer_css' );

// Podpora pro logo
function petsphere_setup() {
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'title-tag' );
    add_theme_support( 'woocommerce' );

    // Registrace menu
    register_nav_menus( array(
        'primary' => __( 'Hlavní menu', 'petsphere' ),
    ) );
}
add_action( 'after_setup_theme', 'petsphere_setup' );

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
?>
