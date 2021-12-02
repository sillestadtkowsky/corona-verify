<?php
ob_start();

/**
 *
 * Plugin Name:       Corona-Test-Verify
 * Plugin URI:        https://plugin.wp.osowsky-webdesign.de/
 * Description:       Dieses Plugin erlaubt jedem Mitarbeiter das digitale Vorzeigen eines gültigen 3G-Status, nach dem dieser zentral im Betrieb erfasst wurde. Für den Gegencheck wird zusätzlich ein QR-Code erzeugt, der eine zeitlich beschränkte Gültigkeit hat.
 * Version:           1.4.0
 * Requires at least: 5.8.2
 * Requires PHP:      7.2
 * Author:            Silvio Osowsky
 * Author URI:        https://osowsky-webdesign.de
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       osowsky-design-plugin
 */

/*
* Load required classes
*/
require_once __DIR__ . '/class/updater.class.php';
require_once __DIR__ . '/class/db.class.php';
require_once __DIR__ . '/class/utils.class.php';
require_once __DIR__ . '/class/secure.class.php';
require_once __DIR__ . '/class/qr.class.php';
require_once __DIR__ . '/class/option.class.php';
require_once __DIR__ . '/admin/corona-admin.php';
require_once __DIR__ . '/class/initdb.class.php';

/*
* set defaults
*/
date_default_timezone_set('Europe/Berlin');

register_activation_hook( __FILE__, array ( 'CV_INITDB', 'installDB') );
register_deactivation_hook( __FILE__, array ( 'CV_INITDB', 'deInstallDB') );

/*
* init Plugin Updater
*/
function corona_test_verifyer_push_update( $transient){
  
  if ( is_admin() ) {
    if( ! function_exists( 'get_plugin_data' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    $plugin_data = get_plugin_data( __FILE__ );

    if ( empty( $transient->checked ) ) {
      return $transient;
    }

    $remote = wp_remote_get( 
      'https://plugins-wordpress-osowsky-webdesign.info/corona-verify/info.json',
      array(
        'timeout' => 10,
        'headers' => array(
          'Accept' => 'application/json'
        )
      )
    );

    if(is_wp_error( $remote )|| 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ))) {
      return $transient;	
    }
    
    $remote = json_decode( wp_remote_retrieve_body( $remote ) );
  
      // your installed plugin version should be on the line below! You can obtain it dynamically of course 
    if($remote && version_compare( $plugin_data['Version'], $remote->version, '<' ) && version_compare( $remote->requires, get_bloginfo( 'version' ), '<' ) && version_compare( $remote->requires_php, PHP_VERSION, '<' )) {
      
      $res = new stdClass();
      $res->slug = $remote->slug;
      $res->plugin = plugin_basename( __FILE__ ); // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
      $res->new_version = $remote->version;
      $res->tested = $remote->tested;
      $res->package = $remote->download_url;
      $transient->response[ $res->plugin ] = $res;
      
      //$transient->checked[$res->plugin] = $remote->version;
    }
}
 
  return $transient;
}
add_filter( 'site_transient_update_plugins', 'corona_test_verifyer_push_update');

    /*
    * $res empty at this step
    * $action 'plugin_information'
    * $args stdClass Object ( [slug] => woocommerce [is_ssl] => [fields] => Array ( [banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1 ) [per_page] => 24 [locale] => en_US )
    */
    function corona_test_verifyer_plugin_info( $res, $action, $args ){

        // do nothing if this is not about getting plugin information
        if( 'plugin_information' !== $action ) {
            return false;
        }

        /*
        // do nothing if it is not our plugin
        if( plugin_basename( __DIR__ ) !== $args->slug ) {
            return false;
        }
        */
        
        // info.json is the file with the actual plugin information on your server
        $remote = wp_remote_get( 
            'https://plugins-wordpress-osowsky-webdesign.info/corona-verify/info.json', 
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json'
                ) 
            )
        );

        // do nothing if we don't get the correct response from the server
        if( is_wp_error( $remote ) || 200 !== wp_remote_retrieve_response_code( $remote ) || empty( wp_remote_retrieve_body( $remote ))){
            return $false;	
        }

        $remote = json_decode( wp_remote_retrieve_body( $remote ) );
        
        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_profile;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->num_ratings = $remote->num_ratings;;
        $res->rating = $remote->rating;
        $res->ratings = $remote->ratings;
        $res->requires = $remote->requires;
        $res->requires_php = $remote->requires_php;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->last_updated = $remote->last_updated;
        $res->sections = array(
            'description' => $remote->sections->description,
            'installation' => $remote->sections->installation,
            'changelog' => $remote->sections->changelog
            // you can add your custom sections (tabs) here
        );

        // in case you want the screenshots tab, use the following HTML format for its content:
        // <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
        if( ! empty( $remote->sections->screenshots ) ) {
            $res->sections[ 'screenshots' ] = $remote->sections->screenshots;
        }

        $res->banners = array(
            'low' => $remote->banners->low,
            'high' => $remote->banners->high
        );
        
        return $res;

    }

add_filter( 'plugins_api', 'corona_test_verifyer_plugin_info', 20, 3);

/*
* load webfonts
*/
if (!function_exists('fa_custom_setup_cdn_webfont')) {
  CV_UTILS::fa_custom_setup_cdn_webfont($cdn_url = '', $integrity = null);
}

/*
* ###########################
* Register Styles and Scripts
*/
function wp_register_plugin_styles()
{
  wp_register_style('corona-style', plugins_url('/css/front-style.css', __FILE__));
  wp_register_style('table-style', plugins_url('/css/table.css', __FILE__));
  wp_register_style('corona-style-fa', plugins_url('/css/fa/css/all.css', __FILE__));
  
  wp_enqueue_style('corona-style-fa');
  wp_enqueue_style('corona-style');
  wp_enqueue_style('table-style');
}

add_action('wp_enqueue_scripts', 'wp_register_plugin_styles');

/*
* ###############################
* ADD Admin Menu and Admin Styles
*/
function corona_menu_creator()
{
  add_menu_page('Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin-menu', 'corona_admin_menu', 'dashicons-editor-customchar', 4);
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Liste', 'Mitarbeiter', 'manage_options', 'coronaEmployees', 'corona_admin_menu_CoronaEmployees');
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Testübersicht', 'Testübersicht', 'manage_options', 'coronaTestOverview', 'corona_admin_menu_CoronaTestOverview');
  //add_submenu_page('corona-admin-menu', 'WP Table Example', 'WP Table Example', 'manage_options', 'corona_admin_menu_WpTableExample', 'corona_admin_menu_WpTableExample');

  wp_register_style('corona-style', plugins_url('/css/front-style.css', __FILE__));
  wp_register_style('table-style', plugins_url('/css/table.css', __FILE__));
  wp_register_style('corona-style-fa', plugins_url('/css/fa/css/all.css', __FILE__));
  wp_register_script('corona-script', plugins_url('/js/corona.js', __FILE__));
  wp_enqueue_style('corona-style-fa');
  wp_enqueue_style('table-style');
  wp_enqueue_style('corona-style');
  wp_enqueue_script('corona-script');
}
add_action('admin_menu', 'corona_menu_creator');

/* 
* ShortCode [corona-verify-form]
* @param qr (1 || 0) >> display QR Code or not 
*/
function corona_verify_shortcode($atts, $content = null, $tag = '')
{
  global $wpdb;
  $options = new CV_OPTIONS();

  $a = shortcode_atts(array(
    'qr' => '0',
  ), $atts);

  if (null != $a) {
    $qr = $a['qr'];
  }

  $ident = $_GET['ident'] ?? 'null';
  if ($ident === 'null' || null == $ident) {
    $personId = get_current_user_id();
    $showQR = true;
  } else {
    $showQR = false;
    $lesbar = CV_SECURE::decrypt($ident, "Wissen=M8");
    $paramPersId = explode("&", $lesbar)[0];
    $personId = explode("=", $paramPersId)[1];
    $paramTestId = explode("&", $lesbar)[1];
    $testId = explode("=", $paramTestId)[1];
  }

  // call DB Data
  $result = CV_DB::getLastTestForEmployee($personId);

  $DEBUGMESSAGE = 'personId: ' . $personId;
  CV_UTILS::debugCode($DEBUGMESSAGE);

  echo '<div class="corona-verify-form">
      <div class="corna-verify-heading"><h1>' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN). ' Verifizierung</h1>';
    if (empty($result) != '1'){
    $test_ergebnis = $result[0];
    if (CV_UTILS::isGueltig($test_ergebnis->expired) == 1) {
      echo '<div class="corna-verify-container-item">
                  <div class="paragraf"><p>Wir sind nach § 28 Infektionsschutzgesetz verpflichtet, 
                  den 3G-Status jedes unserer Mitarbeiter festzustellen und das Ergebnis zu dokumentieren. 
                  Einen gültigen 3G-Status hat derjenige, der entweder geimpft, genesen oder getestet ist. 
                  Wir versichern, dass jede Person, für die hier ein gültiger Status angezeigt wird, eine der vorgenannten Bedingungen erfüllt.</p></div>
                  <div class="corna-verify-container">
                  <label>Name</label>
                  <div class="lastname"><b>' . $test_ergebnis->firstname . ' ' . $test_ergebnis->lastname . '</b></div>
                  <div class="testresult">';
      if ($test_ergebnis->testresult === 'positiv') {
        echo '<div class="positiv">';
        echo '<b>Unser Mitarbeiter hat <u>KEINEN</u> gültigen ' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS). ' Status</b>';
      } else {
        echo '<div class="negativ">';
        echo '<div class="greenBackground"><div class="aktuellesDatum">' . $DateAndTime = date('d.m.Y H:i', time()) . ' Uhr</div> <b>' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_STATUS). ' Status gültig</b></div>';

        if ($options->readOption(CV_OPTIONS::C_QR_CODE)==='yes') {
          if($showQR){
            $personId = get_query_var('persId', -1);
            $testId = get_query_var('testId', -1);
            if ($personId == -1 || $testId == -1) {
              $personId = get_current_user_id();
              $testId = $test_ergebnis->persId;
            }
              echo '<div class="qr">';
              echo '' . CV_QR::getCode($test_ergebnis->persId, $testId);
              echo '</div>';
          }
        }
      }
    } else {
      echo '<div class="expired">';
      echo '<b>Dieser Link ist nicht mehr gültig</b>';
    }
  } else {
    echo '<div class="expired">';
    echo '<b>Dieser Link ist nicht mehr gültig</b>';
  }
  echo '</div></div><div></div></div>';
}
add_shortcode('corona-verify-form', 'corona_verify_shortcode');