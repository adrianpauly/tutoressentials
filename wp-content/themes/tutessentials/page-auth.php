<?php
/**
 * Template name: Auth
 *
 */

if( isset($_GET['auth_id']) ) {

	if( !is_user_logged_in() ) {

		authorize( $_GET['auth_id'], $auth_base_url, $te_site_url );

	} else {
		// User is logged in already
		wp_redirect($te_site_url);
	}

} else {
    echo 'Error! No auth ID provided';
}    