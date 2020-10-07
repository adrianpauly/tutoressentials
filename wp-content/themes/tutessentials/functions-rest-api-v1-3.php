<?php

// Create custom endpoint to retrieve user course progress

define('NEW_TE_API_KEY','171ccbfb1262c305275ba6cb8fb12f8ca4a59b72');

/**
* Add API endpoints
*/
add_action('rest_api_init', 'add_rest_api_routes');

function add_rest_api_routes() {

    register_rest_route( 'tutessentials/v1', '/user/(?P<user_email>\b[\w\.-]+%40[\w\.-]+\.\w{2,4}\b)+', array(
        'methods' => 'GET',
        'callback' => 'rest_get_user_progress',
        'permission_callback' => 'rest_authorize_api'
    ));

    register_rest_route( 'tutessentials/v1', '/progress/(?P<user_email>\b[\w\.-]+%40[\w\.-]+\.\w{2,4}\b)+', array(
        'methods' => 'GET',
        'callback' => 'rest_get_user_progress',
        'permission_callback' => 'rest_authorize_api'
    ));

    register_rest_route( 'tutessentials/v1', '/tutor-philosophies', array(
        'methods' => 'GET',
        'callback' => 'rest_get_tutor_philosophies',
        'permission_callback' => 'rest_authorize_api'
    ));
    
    register_rest_route( 'tutessentials/v1', '/tutor-philosophy/(?P<user_email>\b[\w\.-]+%40[\w\.-]+\.\w{2,4}\b)+', array(
        'methods' => 'GET',
        'callback' => 'rest_get_tutor_philosophy',
        'permission_callback' => 'rest_authorize_api'
    ));
    
    register_rest_route( 'tutessentials/v1', '/tutor-philosophy/set-status/(?P<user_email>\b[\w\.-]+%40[\w\.-]+\.\w{2,4}\b)+', array(
        'methods' => array('POST','GET'),
        'callback' => 'rest_set_tutor_philosophy_status',
        'permission_callback' => 'rest_authorize_api',
        'args' => array (
            'status' => array (
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return in_array($param, array('graded','not_graded','rejected'));
                }
            )
        )
    ));

}



/**
* Authorize API access
*
* Chheck for an HTTP API Key to authorize API access
*
* @param null
*
* @return boolean authorization to access the API
*/
function rest_authorize_api() {

    define('NEW_TE_API_KEY','171ccbfb1262c305275ba6cb8fb12f8ca4a59b72');

    if( empty($_SERVER['HTTP_API_KEY']) ) {
        return $error = new WP_Error('no_api_key', 'No API key sent');
    } else if($_SERVER['HTTP_API_KEY'] !== NEW_TE_API_KEY ) {
        return $error = new WP_Error('api_key_error', 'Invalid API key');
    } else {
        return true;
    }

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
function rest_get_user_progress($request) {

    global $wpdb;
    $email = urldecode($request['user_email']);
    $user = get_user_by( 'email', $email );

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
    return rest_ensure_response($result);
}



/**
* Get final Tutor Philosophy status for all users
*
* Get the final Tutor Philosophy answer statuses for all users paired with their email addresses
*
* @param null
*
* @return object WP REST response type object containing an array with a listing of email addresses paired with
* Tutor Philosophy answer status
*/
function rest_get_tutor_philosophies() {

    $response = get_tutor_philosophies();

    $result = new WP_REST_Response($response, 200);
    $result->set_headers(array('Cache-Control' => 'no-cache'));
    return rest_ensure_response($result);

}




/**
* Get complete Tutor Philosophy answer for a specified user
*
* Get the complete final Tutor Philosophy answer for a specified user, given their email address
*
* @param string URL-encoded string containing a user email address
* TODO: Change unique ID from email to TMS ID
*
* @return object WP REST response type object containing an array with a listing of email addresses paired with
* Tutor Philosophy answer status
*/
function rest_get_tutor_philosophy($request) {

    $email = urldecode($request['user_email']);

    // Get tutor philosophy for specified email
    $response = get_tutor_philosophy($email);

    $result = new WP_REST_Response($response, 200);
    $result->set_headers(array('Cache-Control' => 'no-cache'));
    return rest_ensure_response($result);
}




/**
* Set Tutor Philosophy status for a specified post ID
*
* Set the Tutor Philosophy post status field to 'graded', 'not_graded' or 'rejected'
*
* @param string URL-encoded string containing a user email address
*
* @return object WP REST response type object containing an array with a listing of email addresses paired with
* Tutor Philosophy answer status
*/
function rest_set_tutor_philosophy_status($request) {

    $response = get_tutor_philosophy( urldecode($request['user_email']) );

    $update_data = array(
        'ID' => $response[0]->post_id,
        'post_status' => $request->get_param('status')
    );

    $updated = wp_update_post($update_data, true);

    $result = new WP_REST_Response($updated, 200);
    $result->set_headers(array('Cache-Control' => 'no-cache'));
    return $result;   
}


// ^^^^^^^^^ End API functions ^^^^^^^^^^^^ //




// ========= Internal query functions =========== //


/**
* Get Tutor Philosophy answer for a specified email address
*
* Get the complete final Tutor Philosophy answer for a specified user, given their email address
*
* @param string user email address
*
* @return array Tutor Philosophy content and data
* 
*/
function get_tutor_philosophy($email) {

    global $wpdb;

    $philosophy_question_ids = array('134','143','146','147','149');
    $question_id_count = count($philosophy_question_ids);
    $i = 0;
    $philosophy_question_string = '';
    foreach($philosophy_question_ids as $qid) {
        $philosophy_question_string .= 'm.meta_value = ' . $qid;
        $i++;
        if($i < $question_id_count) {
            $philosophy_question_string .=  ' OR ';
        }
    }

    // Get tutor philosophy for specified email
    $get_tutor_philosophy_query = "
    SELECT p.ID as post_id, p.post_status, p.post_content
    FROM wp_posts AS p
    INNER JOIN wp_users AS u ON p.post_author = u.ID
    WHERE p.ID IN (SELECT m.post_id FROM wp_postmeta AS m WHERE m.meta_key = 'question_id' AND ($philosophy_question_string))
    AND u.user_email = '$email'
    AND p.post_status <> 'trash'
    ";

    $db_response = $wpdb->get_results($get_tutor_philosophy_query);

    $result = !empty($db_response) ? $db_response : 'No results' ;

    return $result;
}


/**
* Get final Tutor Philosophy status for all users
*
* Get the final Tutor Philosophy answer statuses for all users paired with their email addresses
*
* @param null
*
* @return object WP DB object containing a listing of email addresses paired with
* Tutor Philosophy answer status
*/
function get_tutor_philosophies() {

    global $wpdb;

    // Get all tutor philosophy answers
    $get_tutor_philosophies_query = "
    SELECT p.ID as 'post_id', p.post_status, u.user_email
    FROM `wp_posts` AS p, `wp_users` AS u, `wp_postmeta` AS m 
    WHERE p.post_type = 'sfwd-essays'
    AND p.ID IN (SELECT m.post_id FROM `wp_postmeta` WHERE m.meta_key = 'question_id' AND m.meta_value = 134)
    AND p.post_author = u.ID
    AND p.post_status <> 'trash'";
    
    return $response = $wpdb->get_results($get_tutor_philosophies_query);

}




/* Cache bypass: For WP REST API specific paths, use the rest_post_dispatch filter */ 

// wp-json paths or any custom endpoints 
$regex_json_path_patterns = array(
  '#^/wp-json/wp/v1?#',
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