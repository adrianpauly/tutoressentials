<?php

// Create custom endpoint to retrieve user course progress

define('NEW_TE_API_KEY','171ccbfb1262c305275ba6cb8fb12f8ca4a59b72');


/**
* Add API endpoints
*/


add_action('rest_api_init', 'add_user_progress_api');
add_action('rest_api_init', 'add_tutor_philosophy_list_api');

function add_user_progress_api() {
    register_rest_route( 'tutessentials/v1', 'user/(?P<user_email>\b[\w\.-]+%40[\w\.-]+\.\w{2,4}\b)+', array(
        'methods' => 'GET',
        'callback' => 'get_user_progress'
    ));
}

function add_tutor_philosophy_list_api() {
    register_rest_route( 'tutessentials/v1', '/tutor-philosophies', array(
        'methods' => 'GET',
        'callback' => 'get_tutor_philosophies'
    ));
}




/**
* Get user course progress
*
* Get thhe current course completion percentage, plus total lessons and lessons passed, given a specified email address 
*
* @param string URL-encoded string containing a user email address
*
* @return object WP REST response type object containing an array with course completion percentage, completed lessons, 
* total lessons, and completion timestamp
*/
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



/**
* Get final quiz answer (Tutor Philosophy) for all users
*
* Get the final Tutor Philosophy answer statuses for all users paired with their email addresses
*
* @param null
*
* @return object WP REST response type object containing an array with a listing of email addresses paired with
* Tutor Philosophy answer status
*/
function get_tutor_philosophies() {

    global $wpdb;

    if( empty($_SERVER['HTTP_API_KEY']) ) {
        return $error = new WP_Error('no_api_key', 'No API key sent');
    } else if($_SERVER['HTTP_API_KEY'] !== NEW_TE_API_KEY ) {
        return $error = new WP_Error('api_key_error', 'Invalid API key');
    } else {

        // post_status column in posts table is either 'graded' or 'not_graded' 

        // Get all tutor philosophy answers
        $get_tutor_philosophies_query = "
        SELECT p.ID as 'post_id', p.post_status, u.user_email
        FROM `wp_posts` AS p, `wp_users` AS u, `wp_postmeta` AS m 
        WHERE p.post_type = 'sfwd-essays'
        AND p.ID IN (SELECT m.post_id FROM `wp_postmeta` WHERE m.meta_key = 'question_id' AND m.meta_value = 78)
        AND p.post_author = u.ID
        AND p.post_status <> 'trash'";
        

        $response = $wpdb->get_results($get_tutor_philosophies_query);

        $result = new WP_REST_Response($response, 200);
        $result->set_headers(array('Cache-Control' => 'no-cache'));
        return $result;
    }
}



// ======= End API functions ========== //


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