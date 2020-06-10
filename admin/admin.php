<?php

/* ADMIN AREA */
add_action( 'wp_ajax_neon_early_signup', 'neon_early_signup' );
add_action( 'wp_ajax_nopriv_neon_early_signup', 'neon_early_signup');

add_action('wp_ajax_neon_partner_early_signup', 'neon_partner_early_signup');
add_action('wp_ajax_nopriv_neon_partner_early_signup', 'neon_partner_early_signup');

function neon_early_signup() {
    if ( !empty($_POST) ) 
    { 
        $the_email = urldecode( $_POST['email'] ); 
    } 
    else 
    {
        $the_email = false;
    }
    error_log( $the_email );
    error_log( print_r($_POST, true));
    $retval = array();
    //get content
    if( $the_email ) {
    	$uname = explode("@",$the_email)[0];
    	$destination_email = urldecode( $_POST['dest_email']);

    	$already = username_exists( $uname );
    	if ( !$already && false == email_exists( $the_email ) ) {
			$random_password = wp_generate_password( $length = 5, $include_standard_special_chars = false );
			$user_id = wp_create_user( $uname, $random_password, $the_email );
			$retval['type'] = 'success';
			$retval['message'] = 'Thanks! Your email has been added to the list and you should hear from us soon.';
		} else {
			    $retval['type'] = 'success';
				$retval['message'] = 'Thanks! We\'ve already got that email address on file and will get in touch.';
		}

		if( $user_id ){
			$header('Content-Type: text/html; charset=utf-8'); 
			$subject = 'New NEONID early signup request';
			$body = $the_email .' wants access to NEONID! The email has been saved in our database.';
			wp_mail( $destination_email, $subject, $body, $header );
		}
            

            //get metadata (price, languages, etc.)
    } else { $retval['type'] = 'error'; $retval['message'] = 'There was a problem adding your email to the list.'; }
        
echo json_encode( $retval );

wp_die(); //AJAX calls, like surf nazis - must die
}

function neon_partner_early_signup() {
    if ( !empty($_POST) ) 
    { 
        $the_email = urldecode( $_POST['email'] ); 
        $partner_name = urldecode($_POST['partner_name']);
        $company_name = urldecode($_POST['company_name']);
    } 
    else 
    {
        $the_email = false;
    }
    $retval = array();
    //get content
    if( $the_email ) {
    	$uname = explode("@",$the_email)[0];
    	$destination_email = urldecode( $_POST['dest_email']);

    	$already = username_exists( $uname );
    	if ( !$already && false == email_exists( $the_email ) ) {
			$random_password = wp_generate_password( $length = 5, $include_standard_special_chars = false );
			$user_id = wp_create_user( $uname, $random_password, $the_email );
			if( $user_id ){
				$retval['type'] = 'success';

				$user_data = wp_update_user( 
					array( 
						'ID' => $user_id, 
						'first_name' => $partner_name, 
						'last_name'  => $company_name,
						'description'=> $partner_name . ' from ' . $company_name
					) );
				if ( is_wp_error( $user_data ) ) {		    
				    $retval['message'] = 'We\'ve added your email, but had trouble with your name. We\'ll follow up.';
				} else {
				    $retval['message'] = 'Thanks! Your email has been added to our list. We\'ll talk soon.';
				}
		    } else {
		    	$retval['type'] = "error";
		    	$retval['message'] = 'There was a problem adding your email to our list.';
		    }
		} else {
			    $retval['type'] = 'success';
				$retval['message'] = 'Thanks! We\'ve already got that email address on file and will get in touch.';
		}

		if( $user_id ){
			$header('Content-Type: text/html; charset=utf-8'); 
			$subject = 'New NEONID Business Partner early signup request';
			$body = $the_email .' wants access to NEONID! The email has been saved in our database. <br> Parter Name: '. $partner_name . '<br> Company Name: '.$company_name;

			wp_mail( $destination_email, $subject, $body, $header );
		}
            

            //get metadata (price, languages, etc.)
    } else { $retval['type'] = 'error'; $retval['message'] = 'There was a problem adding your email to the list.'; }
        
echo json_encode( $retval );

wp_die(); //AJAX calls, like surf nazis - must die
}