<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Edumodo
 */
    $post_id = edumodo_get_id();
    // Global Options
    global $edumodo_options;
    // Prefix
    $prefix = '_edumodo_';

    // logo option
    $edumodo_logo   = edumodo_array_get($edumodo_options, 'edumodo_logo') ? $edumodo_options['edumodo_logo']['url'] : '';
     // Transparant Logo
    $edumodo_logo_2   = edumodo_array_get($edumodo_options, 'edumodo_logo_2') ? $edumodo_options['edumodo_logo_2']['url'] : '';
    // Header
    $enable_sticky_active   = edumodo_array_get($edumodo_options, 'enable_sticky_active') ? $edumodo_options['enable_sticky_active'] : '';
    $enable_transparent_active   = edumodo_array_get($edumodo_options, 'enable_transparent_active') ? $edumodo_options['enable_transparent_active'] : '';
//    $enable_mega_menu   = edumodo_array_get($edumodo_options, 'enable_mega_menu') ? $edumodo_options['enable_mega_menu'] : '';

?>

    <header class="edumodo-header-1 <?php if ($enable_sticky_active == true) : echo 'sticky-active'; endif; ?> <?php if ($enable_transparent_active == true and is_front_page()) : echo 'transparent-active'; endif; ?>">

        <div id="edumodo-mainnav" class="navbar-v1 edumodo-mainnav">
            <div class="container">
                <div class="row">
                    <div class="nav-logo-align">
                   <div class="col-xs-8 col-sm-9 col-md-4 logo">
                        <div class="navbar-header">     

                            <div class="logo-wrapper">
                                <?php 
    
                                    if ($edumodo_logo or $edumodo_logo_2) : ?>
                                        <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
                                            <?php 
                                                if(has_custom_logo()) :
                                                    the_custom_logo();
                                                endif; 
                                            ?>
                                            <img class="logo-transparent" src="<?php echo esc_url($edumodo_logo_2); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
                                            <img class="logo-default" src="<?php echo esc_url($edumodo_logo); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
                                        </a>
                                    <?php else : ?>
                                    <h2 class="site-title">
                                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                            <?php bloginfo( 'name' ); ?>
                                        </a>
                                    </h2>
                                    <h6 class="site-description"><?php bloginfo( 'description' ); ?></h6>
                                <?php endif; ?>
                            </div>


                        </div>
                    </div>

                        <div class="col-xs-4 col-sm-3 col-md-8 pull-right text-right hidden-xs hidden-sm">
                            <?php
                                wp_nav_menu( array(
                                    'menu'              => 'primary',
                                    'theme_location'    => 'primary',
                                    'depth'             => 0,
                                    'menu_class'        => 'navigation-main hidden-sm hidden-xs',
                                   // 'fallback_cb'       => 'edumodo_link_to_menu_editor',
                                    'fallback_cb'       => 'tx_megamenu_navwalker::fallback',
                                    'walker'            => new tx_megamenu_navwalker()
                                ));
                            ?>

                        </div>

                        <div class="col-xs-4 col-sm-3 pull-right text-right visible-xs visible-sm hidden-md hidden-lg">
                            <div id="mobile-menu-wrapper" class="mmenu-wrapper">
                                <div class="mmenu-icon">
                                    <a href="#mmenu" class="micon">
                                        <div id="nav-icon1">
                                          <span></span>
                                          <span></span>
                                          <span></span>
                                        </div>
                                    </a>

                                </div>
                                <nav id="mmenu">
                                    <?php
                                        wp_nav_menu(array(
                                            'menu' => 'mobile_menu',
                                            'theme_location' => 'mobile_menu',
                                        ));
                                    ?>
                                </nav>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </header><!-- #masthead -->












