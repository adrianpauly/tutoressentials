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
    $user = get_user_by('login', $auth_object->username);

    // User doesn't currently exist in the WordPress database, create it
    // Otherwise the object is already loaded, continue log in

    // ** authenticate by user_login instead of user_email

    if(!$user) {
        $userdata = array('user_email'=>$auth_object->email,
                          'user_login'=>$auth_object->username,
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

if( $path = '/' ) {
    $redirect = false;
} else {
    foreach($non_redirect_urls as $url) {
    	if (strpos($path, $url)) $redirect = false;	
    }
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