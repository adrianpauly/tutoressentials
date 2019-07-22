<?

//// TMS authentication

///// Auth page functions

/**
* Call API via PHP curl
* 
* @param $method string - POST, PUT, GET
* @param $data array - ('param' => 'value')
*
*/
function callAPI($method, $url, $data = false) {

    $curl = curl_init();

    if($data) $url = sprintf("%s?%s", $url, http_build_query($data));

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}


/**
* Sets current site user or makes a new one based on 
* the external authentication object 
*
* @param $auth_object object Validated authentication object coming from TMS
* @return $user object Wordpress user object
*/
function set_user($auth_object) {

    // External user exists, try to load the user info from the WordPress user table
    $user = get_user_by('email', $auth_object->email);

    // User doesn't currently exist in the WordPress database, create it
    // Otherwise the object is already loaded, continue log in
    if(!$user) {
        $userdata = array('user_email'=>$auth_object->email,
                          'user_login'=>$auth_object->email,
                          'first_name'=>$auth_object->first_name,
                          'last_name'=>$auth_object->last_name
                          );
        $new_user_id = wp_insert_user($userdata);
        $user = new WP_User($new_user_id);
        //echo 'USER CREATED';
    } else {
        //echo 'USER EXISTS';
    }

    remove_action('authenticate', 'wp_authenticate_username_password', 20);

    return $user;
}


/**
* Authorizes current access request by validating via the TMS API, then redirects to specified URL
*
* @param $auth_id string Auth ID given by TMS
* @param $auth_base_url string API URL on TMS site to which validation requests are sent
* @param $redirect_url string URL to which user should be ultimately redirected once they're logged in
*/
function authorize($auth_id, $auth_base_url, $redirect_url) {

    $auth_url = $auth_base_url . $auth_id;

    $auth_result = callAPI('GET', $auth_url);

    if ($auth_object = json_decode($auth_result)) {

        $user = set_user($auth_object);

        if($user) {

            // Log the user in
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);

            // Redirect
            wp_redirect($redirect_url);
            
        }
    } else {
        echo 'Authentication error: Invalid auth key (API did not return a valid authentication object)';
    }
}


/**
* Output buffer to avoid headers being sent before redirection
*/ 
// Seems it's not necessary
// function app_output_buffer() {
//    ob_start();
// }
//add_action('init', 'app_output_buffer');


/** 
* Encode the URL but then reestablish the colon to its normal state
* (to match how TMS encodes URLs)
*
* @param $url string Unencoded URL
* @return string Semi-encoded URL
*/
function urlsemiencode($url) {
    $encoded = urlencode($url);
    return str_replace('%3A', ':', $encoded);
}



// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Get current url
$path = $_SERVER['REQUEST_URI'];
$redirPath = urlsemiencode($path);
$authURL = urlsemiencode(home_url('auth'));

$auth_base_url = 'https://tutormatchingservice.com/api/v1/auth/verify-auth-id/';
$login_base_url = 'https://tutormatchingservice.com/#/login/?redirectTo=';

$te_site_url = get_site_url();
$course_page_path = '?sfwd-courses=tutor-essentials-course';

// URLs which are ok not to redirect
$non_redirect_urls = array('wp-admin','wp-login','auth','wp-json');
$redirect = true;

foreach($non_redirect_urls as $url) {
	if (strpos($path, $url)) $redirect = false;	
}

//if(is_user_logged_in()) echo '<p style="position:absolute; bottom: 30px">User logged in</p>';

// Redirect if user is logged out and not on one of the safe pages
if( !is_user_logged_in() && $redirect) {
	echo 'REDIRECTING';
    $login_url = $login_base_url . $authURL;
    if($path != '/') $login_url .= '?path=' . $redirPath;

    wp_redirect($login_url);
	exit();
} 


// auth_id check on course page
if( isset($_GET['sfwd-courses']) && $_GET['sfwd-courses'] == 'tutor-essentials-course' ) {
   
    if(isset($_GET['auth_id'])) {

        authorize($_GET['auth_id'], $auth_base_url, $te_site_url . $course_page_path );

    } 
}



///// END AUTHORIZATION CODE ////


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

        return array(
            'percentage' => isset( $percentage ) ? $percentage : 0,
            'completed'  => isset( $completed ) ? $completed : 0,
            'total'      => isset( $total ) ? $total : 0,
        );        
    }
}