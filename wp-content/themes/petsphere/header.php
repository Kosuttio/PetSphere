<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
</head>
<body <?php body_class(); ?>>

<header>
    <div class="container header-container">
        <!-- 1. Logo (Left) -->
        <div class="site-branding">
            <div class="site-title">
                <?php
                if ( has_custom_logo() ) {
                    the_custom_logo();
                } else {
                    echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . get_bloginfo( 'name' ) . '</a>';
                }
                ?>
            </div>
        </div>

        <!-- 2. Menu (Center) -->
        <nav class="main-navigation">
            <?php
            // 1. Desktop Menu (Primary) - Visible on Desktop, hidden on Mobile via CSS
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'menu desktop-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ) );

            // 2. Mobile Menu - Hidden on Desktop via CSS, visible on Mobile
            // If 'mobile' menu is assigned, use it. Otherwise fallback to 'primary'.
            $mobile_loc = has_nav_menu( 'mobile' ) ? 'mobile' : 'primary';
            
            wp_nav_menu( array(
                'theme_location' => $mobile_loc,
                'menu_id'        => 'mobile-menu',
                'menu_class'     => 'menu mobile-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ) );
            ?>
            
            <!-- Mobile Search Container (Hidden by default, toggled by JS) -->
            <div class="mobile-search-container" style="display: none;">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Hledat...', 'placeholder', 'petsphere' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="search-submit">Hledat</button>
                </form>
            </div>
        </nav>

        <!-- 3. Actions (Right) -->
        <div class="header-actions">
            <!-- Search Toggle -->
            <div class="header-search-wrapper">
                <button class="header-search-toggle" aria-label="Hledat">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
                <div class="header-search-dropdown">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Hledat...', 'placeholder', 'petsphere' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="search-submit">Hledat</button>
                    </form>
                </div>
            </div>

            <!-- Account -->
            <div class="header-account">
                <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="account-link" aria-label="Můj účet">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </a>
            </div>

            <!-- Cart -->
            <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <div class="header-cart">
                <a href="<?php echo wc_get_cart_url(); ?>" class="cart-link" aria-label="Košík">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                    <span class="cart-count-badge"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Mobile Toggle -->
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
            <span class="hamburger-icon">☰</span>
        </button>
    </div>
</header>
