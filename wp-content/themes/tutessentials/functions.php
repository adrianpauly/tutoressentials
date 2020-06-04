<?

function additional_scripts() {

    // Slick slider
    if(is_front_page()) {
        wp_enqueue_style('slick-style', get_stylesheet_directory_uri() . '/slick/slick.css', array(), EDUMODO_VERSION, 'all');
        wp_enqueue_script('slick-js', get_stylesheet_directory_uri() . '/slick/slick.js', array('jquery'), EDUMODO_VERSION, true);
    }

    // Custom scripts + styles
    wp_enqueue_script('foundation-js', get_stylesheet_directory_uri() . '/js/vendor/foundation.js', array('jquery'), EDUMODO_VERSION, true);
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), EDUMODO_VERSION, true);
    wp_enqueue_style('foundation-css', get_stylesheet_directory_uri() . '/css/foundation.min.css', array(), EDUMODO_VERSION, 'all');
    wp_enqueue_style('fonts', 'https://fonts.googleapis.com/css?family=Montserrat:300,400,600,700|Nunito+Sans:300,400,700&display=swap', array(), EDUMODO_VERSION, 'all');
    wp_enqueue_style('custom-styles', get_stylesheet_directory_uri() . '/css/custom.css', array(), EDUMODO_VERSION, 'all');
}
add_action('wp_enqueue_scripts', 'additional_scripts');




// Show Next & Previous Button on Lesson

add_filter('learndash_show_next_link', 'learndash_show_next_link_proc', 10, 3);
function learndash_show_next_link_proc( $show_next_link = false, $user_id = 0, $post_id = 0 ) {
    
    // Example 2) Check post type 
    $post_type = get_post_type( $post_id );
    if ( $post_type == 'sfwd-lessons' || $post_type == 'sfwd-quiz') 
      $show_next_link = true;
    
    return $show_next_link;
}


// Only show admin bar to admins
if(!current_user_can('administrator')) {
    show_admin_bar(false);
}


// Get via cURL
function get_curl($endpoint) {

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => 1
    ));

    $curl_result = curl_exec($curl);

    if($curl_result === false ) throw new Exception(curl_error($curl), curl_errno($curl));
    
    curl_close($curl);

    return $curl_result;
}




// Get privacy pages content from AN
function get_privacy($endpoint) {
    $curl_result = get_curl($endpoint);
    return json_decode($curl_result);
}


// Include TMS authentication functions
include('functions-tms-auth.php');

// Include REST API functions
include('functions-rest-api-v1-3.php');