<?

/*
Plugin Name: Tutor Essentials SSO
Description: Handles single sign-on for cross-site authentication between Tutor Essentials and TMS
Author: Adrian Pauly
*/


// Output buffer to avoid headers being sent before redirection
function app_output_buffer() {
   ob_start();
}


/**
* Call API via PHP cURL
* 
* @param string $method POST, PUT, GET
* @param array $data 'param' => 'value'
* @return object $result The result from the cURL 
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
* Sets current site user or makes a new one based on the external authentication object
* 
* @param object $auth_object Authentication object coming from TMS
* @return object $user Wordpress user object
*
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
    	echo 'USER CREATED';
    } else {
    	echo 'USER EXISTS';
    }

    remove_action('authenticate', 'wp_authenticate_username_password', 20);

    return $user;
}



/**
* Redirects user to TMS if not authenticated 
*
*/
function sso_auth_redirect() {

	// Get current url
	$path = $_SERVER['REQUEST_URI'];
	$authURL = urlencode(home_url('auth'));
	$redirPath = urlencode($path);

	// URLs which are ok not to redirect
	$non_redirect_urls = array('wp-admin','wp-login','auth');
	$redirect = true;

	foreach($non_redirect_urls as $url) {
		if (strpos($path, $url)) $redirect = false;	
	}

	// Redirect if user is logged out and not on one of the safe pages
	if( !is_user_logged_in() && $redirect ) {
		echo 'REDIRECTING';
		//wp_redirect('https://tutormatchingservice.com/#/login/?redirectTo=' . $authURL . '?path=' . $redirPath);
		//exit();
	}
}


/**
* Custom auth main function. Gets auth_id and calls TMS auth API, then logs user in
*
*/
function custom_auth() {

	if(is_page('auth')) {

		echo '<pre>';
		var_dump('OH HEY');
		echo '</pre>';

		if(isset($_GET['auth_id'])) {

			//$auth_id = 'eyJpdiI6IkozeGloQjZpUUdieHZuMGxRZXNtdlE9PSIsInZhbHVlIjoiUFIxampFQXZseDRaZGNVTXJtcW5aQT09IiwibWFjIjoiMmU0ODZjOGQ5ZDRhZmIyNDkwMzAxM2U2NTA4NmRjODc1NjYxZmZhN2U3MTdlMWFmZjM2OGJlMmY2NmQ2MDcwYyJ9';
			$auth_id = $_GET['auth_id'];
			$auth_url = 'https://tutormatchingservice.com/api/v1/auth/verify-auth-id/' . $auth_id;

			$auth_result = callAPI('GET', $auth_url);

			if ($auth_object = json_decode($auth_result)) {

				$user = set_user($auth_object);

				if($user) {

					// Log the user in
					wp_set_current_user($user->ID);
					wp_set_auth_cookie($user->ID, true);

					// Redirect
					$redirect_after_login = get_site_url();
					wp_redirect($redirect_after_login);
					
				}
			} else {
				echo 'Authentication error: API did not return a valid authentication object';
			}

		} else {
			echo 'Error! No auth ID provided';
		}	
	}
}


/**
* Show admin bar for admin users only
*
*/
function admin_bar_admin_only() {
	if(!current_user_can('administrator')) {
		show_admin_bar(false);
	}
}

add_action('init', 'app_output_buffer');
add_action('init', 'sso_auth_redirect');
add_action('init', 'custom_auth');
add_action('init', 'admin_bar_admin_only');