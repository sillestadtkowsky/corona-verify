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

add_action( 'admin_menu', 'corona_menu_creator' );
function corona_menu_creator() {
    add_menu_page( 'Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin', 'corona-admin', '' , '81.912514514511451' ); 
}

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
$servername = "localhost";
$username = "d02dba56";
$password = "2z2D8hc6gXZMPJTk4Eky";
$dbname = "d02dba56";

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT persID, vorname, name FROM cv_employeee";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["persID"]. " - Name: " . $row["vorname"]. " " . $row["name"]. "<br>";
  }
} else {
  echo "0 results";
}
$conn->close();
?>

<?php
function wporg_options_page_html() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields( 'wporg_options' );
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections( 'wporg' );
        // output save settings button
        submit_button( __( 'Save Settings', 'textdomain' ) );
        ?>
      </form>
    </div>
    <?php
}
?>
<?php
add_action( 'admin_menu', 'wporg_options_page' );
function wporg_options_page() {
    add_menu_page(
        'WPOrg',
        'WPOrg Options',
        'manage_options',
        'wporg',
        'wporg_options_page_html',
        plugin_dir_url(__FILE__) . 'images/icon_wporg.png',
        20
    );
}

?>

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