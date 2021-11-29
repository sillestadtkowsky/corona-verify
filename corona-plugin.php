<?php
ob_start();
/**
 *
 * Plugin Name:       Corona Test Verifyer
 * Plugin URI:        https://plugin.wp.osowsky-webdesign.de/
 * Description:       Dieses Plugin erlaubt jedem Mitarbeiter das digitale Vorzeigen eines gültigen 3G-Status, nach dem dieser zentral im Betrieb erfasst wurde. Für den Gegencheck wird zusätzlich ein QR-Code erzeugt, der eine zeitlich beschränkte Gültigkeit hat.
 * Version:           1.3.4
 * Requires at least: 5.2
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

/*
* set defaults
*/
date_default_timezone_set('Europe/Berlin');


/*
* init Plugin Updater
*/
function corona_test_verifyer_push_update( $transient){
  return CV_UPDATER::update($transient);
}
add_filter( 'site_transient_update_plugins', 'corona_test_verifyer_push_update');

/*
* init Plugin Updater
*/
function corona_test_verifyer_plugin_info( $res, $action, $args){
  return CV_UPDATER::info($res, $action, $args);
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
function wpdocs_register_plugin_styles()
{
  wp_register_style('corona-style', plugins_url('/css/front-style.css', __FILE__));
  wp_register_style('table-style', plugins_url('/css/table.css', __FILE__));
  wp_register_style('corona-style-fa', plugins_url('/css/fa/css/all.css', __FILE__));
  wp_enqueue_style('corona-style-fa');
  wp_enqueue_style('corona-style');
  wp_enqueue_style('table-style');
}
add_action('wp_enqueue_scripts', 'wpdocs_register_plugin_styles');

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

  echo '<div class="corona-verify-form">
      <div class="corna-verify-heading"><h1>' .$options->readOption(CV_OPTIONS::C_VERIFIZIERUNG_KENNZEICHEN). ' Verifizierung</h1>';
  if ($wpdb->num_rows > 0) {
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
