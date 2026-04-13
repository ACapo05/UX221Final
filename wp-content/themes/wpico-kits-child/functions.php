<?php
/**
 * Family Kits Child Theme Functions
 * Child of: wpico
 */

/* ============================================================
   ENQUEUE STYLES & SCRIPTS
   ============================================================ */
add_action( 'wp_enqueue_scripts', 'kits_child_enqueue', 20 );
function kits_child_enqueue() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'wpico-parent',
        get_template_directory_uri() . '/style.css'
    );
    // Child theme stylesheet (overrides Pico variables + custom styles)
    wp_enqueue_style(
        'kits-child-style',
        get_stylesheet_uri(),
        array( 'wpico-parent' ),
        wp_get_theme()->get( 'Version' )
    );
    // Kit Finder custom web component
    wp_enqueue_script(
        'kits-web-component',
        get_stylesheet_directory_uri() . '/js/kit-finder.js',
        array(),
        '1.0',
        array( 'strategy' => 'defer', 'in_footer' => true )
    );
}

/* ============================================================
   FAVICON
   ============================================================ */
add_action( 'wp_head', 'kits_add_favicon', 5 );
function kits_add_favicon() {
    $favicon_url = esc_url( get_stylesheet_directory_uri() . '/favicon.svg' );
    echo '<link rel="icon" type="image/svg+xml" href="' . $favicon_url . '">' . "\n";
    echo '<link rel="shortcut icon" href="' . $favicon_url . '">' . "\n";
}

/* ============================================================
   RE-ENABLE POSTS IN ADMIN (parent theme hides them)
   ============================================================ */
add_action( 'admin_menu', 'kits_restore_post_menu', 999 );
function kits_restore_post_menu() {
    add_menu_page(
        'Kit Posts',
        'Kit Posts',
        'edit_posts',
        'edit.php',
        '',
        'dashicons-archive',
        6
    );
}

/* ============================================================
   REGISTRATION FORM HANDLER
   ============================================================ */
add_action( 'admin_post_nopriv_kits_register', 'kits_handle_registration' );
add_action( 'admin_post_kits_register', 'kits_handle_registration' );
function kits_handle_registration() {
    // Verify nonce
    if ( ! isset( $_POST['kits_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kits_nonce'] ) ), 'kits_registration' ) ) {
        wp_die( 'Security check failed. Please go back and try again.' );
    }

    $name        = sanitize_text_field( wp_unslash( $_POST['kits_name'] ?? '' ) );
    $email       = sanitize_email( wp_unslash( $_POST['kits_email'] ?? '' ) );
    $brand       = sanitize_text_field( wp_unslash( $_POST['kits_brand'] ?? '' ) );
    $summary     = sanitize_textarea_field( wp_unslash( $_POST['kits_summary'] ?? '' ) );
    $kit_types   = isset( $_POST['kits_types'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['kits_types'] ) ) : array();

    // Word count validation
    $word_count = str_word_count( $summary );
    if ( $word_count < 80 || $word_count > 120 ) {
        wp_safe_redirect( add_query_arg( 'kits_error', 'wordcount', wp_get_referer() ) );
        exit;
    }

    if ( empty( $name ) || ! is_email( $email ) ) {
        wp_safe_redirect( add_query_arg( 'kits_error', 'missing', wp_get_referer() ) );
        exit;
    }

    // Save as a custom post for admin review
    $post_id = wp_insert_post( array(
        'post_type'    => 'post',
        'post_title'   => 'Kit Developer Application: ' . $name,
        'post_content' => $summary,
        'post_status'  => 'private',
        'post_author'  => 1,
        'meta_input'   => array(
            'applicant_email'    => $email,
            'applicant_brand'    => $brand,
            'applicant_kittypes' => implode( ', ', $kit_types ),
            '_is_kit_application' => '1',
        ),
    ) );

    $registration_page = get_page_by_path( 'kit-developer-registration' );
    $redirect_url      = $registration_page ? get_permalink( $registration_page ) : home_url( '/' );
    wp_safe_redirect( add_query_arg( 'kits_applied', '1', $redirect_url ) );
    exit;
}

/* ============================================================
   SHORTCODE: [kit_finder]
   Fallback for browsers without JS custom element support
   ============================================================ */
add_shortcode( 'kit_finder', 'kits_finder_shortcode' );
function kits_finder_shortcode( $atts ) {
    $shop_page = get_page_by_path( 'kit-shop' );
    $reg_page  = get_page_by_path( 'kit-developer-registration' );
    $shop_url  = $shop_page ? esc_url( get_permalink( $shop_page ) ) : esc_url( home_url( '/kit-shop/' ) );
    $reg_url   = $reg_page  ? esc_url( get_permalink( $reg_page ) )  : esc_url( home_url( '/kit-developer-registration/' ) );

    ob_start();
    ?>
    <kit-finder shop-url="<?php echo $shop_url; ?>" register-url="<?php echo $reg_url; ?>">
        <noscript>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                <a href="<?php echo $shop_url; ?>" role="button">Browse Kits to Make</a>
                <a href="<?php echo $reg_url; ?>" role="button" class="outline">Become a Kit Developer</a>
            </div>
        </noscript>
    </kit-finder>
    <?php
    return ob_get_clean();
}

/* ============================================================
   THEME SUPPORT
   ============================================================ */
add_action( 'after_setup_theme', 'kits_child_theme_support' );
function kits_child_theme_support() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
    register_nav_menus( array(
        'header' => __( 'Header Menu' ),
        'footer' => __( 'Footer Menu' ),
    ) );
}
