<?php
/**
*
* Plugin Name:       Corona Test Verifyer
* Plugin URI:        https://example.com/plugins/the-basics/
* Description:       Quittiert das Ergbnis eines durchgeführten Test
* Version:           1.0.1
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Silvio Osowsky
* Author URI:        https://osowsky-webdesign.de
* License:           GPL v2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html
* Update URI:        https://example.com/my-plugin/
* Text Domain:       sille-basics-plugin
*/
define('CORONA_REGISTRATION_INCLUDE_URL', plugin_dir_url(__FILE__).'includes/');

//Adding frontend Styles from includes folder

function corona_style_incl(){

wp_enqueue_style('corona_style_css_and_js', CORONA_REGISTRATION_INCLUDE_URL."front-style.css");

wp_enqueue_script('corona_style_css_and_js');

}

add_action('wp_footer','corona_style_incl');

// function to login Shortcode

function corona_login_shortcode( $atts ) {


?>

<div class="corona-verify-form">
    <div class="corna-verify-heading">  
        <?php _e("Corona - Verifizierungsseite",'');?>
    </div>
</div>

<?php

}

//Adding login shortcode

add_shortcode( 'corona-verify-form', 'corona_login_shortcode' );

//Redirecting to front end ,when login is failed

add_action( 'wp_login_failed', 'tuts_front_end_login_fail' ); // Hook for failed login

function tuts_front_end_login_fail( $username ) {

$referrer = $_SERVER['HTTP_REFERER'];

// if there's a valid referrer, and it's not the default log-in screen

if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {

wp_redirect( $referrer . '/?login=failed' );

exit;

}

}

?>