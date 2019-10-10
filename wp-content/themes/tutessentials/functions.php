<?

function additional_scripts() {

    // Slick slider
    if(is_front_page()) {
        wp_enqueue_style('slick-style', get_stylesheet_directory_uri() . '/slick/slick.css', array(), EDUMODO_VERSION, 'all');
        wp_enqueue_script('slick-js', get_stylesheet_directory_uri() . '/slick/slick.js', array('jquery'), EDUMODO_VERSION, true);
    }

    // Custom scripts + styles
    wp_enqueue_script('custom', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), EDUMODO_VERSION, true);
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

 



// Create custom endpoint to retrieve user course progress

define('NEW_TE_API_KEY','171ccbfb1262c305275ba6cb8fb12f8ca4a59b72');

add_action('rest_api_init', 'add_user_progress_api');

function add_user_progress_api() {
    register_rest_route( 'tutessentials/v1', 'user/(?P<user_email>\b[\w\.-]+%40[\w\.-]+\.\w{2,4}\b)+', array(
        'methods' => 'GET',
        'callback' => 'get_user_progress'
    ));
}

function get_user_progress($data) {

    global $wpdb;
    $email = urldecode($data['user_email']);
    $user = get_user_by( 'email', $email );

    if( empty($_SERVER['HTTP_API_KEY']) ) {
        return $error = new WP_Error('no_api_key', 'No API key sent');
    } else if($_SERVER['HTTP_API_KEY'] !== NEW_TE_API_KEY ) {
        return $error = new WP_Error('api_key_error', 'Invalid API key');
    } else {

        $course_id = 6; // Tutor Essentials course ID, the only one on the site
        $course_progress = get_user_meta( $user->ID, '_sfwd-course_progress', true );


        // Lifted from ld-course-progress.php

        $percentage = 0;

        if ( ( ! empty( $course_progress ) ) && ( isset( $course_progress[ $course_id ] ) ) && ( ! empty( $course_progress[ $course_id ] ) ) ) {
            if ( isset( $course_progress[ $course_id ]['completed'] ) ) {
                $completed = absint( $course_progress[ $course_id ]['completed'] );
            }

            if ( isset( $course_progress[ $course_id ]['total'] ) ) {
                $total = absint( $course_progress[ $course_id ]['total'] );
            }
        } else {
            $total = 0;
        }

        // If $total is still false we calculate the total from course steps.
        if ( false === $total ) {
            $total = learndash_get_course_steps_count( $course_id );
        }

        if ( $total > 0 ) {
            $percentage = intval( $completed * 100 / $total );
            $percentage = ( $percentage > 100 ) ? 100 : $percentage;
        } else {
            $percentage = 0;
        }

        //Get course completed timestamp
        $get_completion_time_query = "
            SELECT `activity_completed` 
            FROM `wp_learndash_user_activity` 
            WHERE `user_id`=" . $user->ID . "
            AND `activity_type`='course' 
            ORDER BY `activity_completed` DESC LIMIT 1";
            
        $completion_time_results = $wpdb->get_results($get_completion_time_query);

        $response = array(
            'percentage'        => isset( $percentage ) ? $percentage : 0,
            'completed'         => isset( $completed ) ? $completed : 0,
            'total'             => isset( $total ) ? $total : 0,
            'completion_time'   => $completion_time_results[0]->activity_completed
        );

        $result = new WP_REST_Response($response, 200);
        $result->set_headers(array('Cache-Control' => 'no-cache'));
        return $result;
    }
}


/* Cache bypass: For WP REST API specific paths, use the rest_post_dispatch filter */ 

// wp-json paths or any custom endpoints 
$regex_json_path_patterns = array(
  '#^/wp-json/wp/v2?#',
  '#^/wp-json/?#'
);

foreach ($regex_json_path_patterns as $regex_json_path_pattern) {
  if (preg_match($regex_json_path_pattern, $_SERVER['REQUEST_URI'])) {
      // re-use the rest_post_dispatch filter in the Pantheon page cache plugin  
      add_filter( 'rest_post_dispatch', 'filter_rest_post_dispatch_send_cache_control', 12, 2 );

      // Re-define the send_header value with any custom Cache-Control header
      function filter_rest_post_dispatch_send_cache_control( $response, $server ) {
          $server->send_header( 'Cache-Control', 'no-cache, must-revalidate, max-age=0' );
          return $response;
      }
      break;
  }
}


// TMS authentication functions on a separate file
include('functions-tms-auth.php');