<?php
ob_start();
/**
*
* Plugin Name:       Corona Test Verifyer
* Plugin URI:        https://plugin.wp.osowsky-webdesign.de/
* Description:       Quittiert das Ergebnis eines durchgeführten Test
* Version:           1.1.7
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
require_once('class/db.class.php');
require_once('class/utils.class.php');
require_once('class/secure.class.php');
require_once('class/qr.class.php');

/*
* set defaults
*/
date_default_timezone_set('Europe/Berlin') ; 

/*
* load webfonts
*/
if (! function_exists('fa_custom_setup_cdn_webfont') ) {
  CV_UTILS::fa_custom_setup_cdn_webfont($cdn_url = '', $integrity = null);
}

/*
* Register Styles and Scripts
*/
function wpdocs_register_plugin_styles() {
  wp_register_style( 'corona-verify', plugins_url( 'corona-verify/css/front-style.css' ) );
  wp_register_style( 'corona-verify-fa', plugins_url( 'corona-verify/css/fa/css/all.css' ) );
  wp_enqueue_style( 'corona-verify' );
  wp_enqueue_style( 'corona-verify-fa' );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_register_plugin_styles' );

/*
* Register Styles and Scripts
*/
function corona_menu_creator() {
  add_menu_page('Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin-menu', 'corona_admin_menu', 'dashicons-editor-customchar' , 4 ); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Liste', 'Mitarbeiter', 'manage_options', 'corona_admin_menu_CoronaEmployees', 'corona_admin_menu_CoronaEmployees'); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Testübersicht', 'Testübersicht', 'manage_options', 'corona_admin_menu_CoronaTestOverview', 'corona_admin_menu_CoronaTestOverview'); 

  wp_register_style( 'corona-style', plugins_url('css/front-style.css', __FILE__) );
  wp_register_style( 'corona-style-fa', plugins_url('css/fa/css/all.css', __FILE__) );
  wp_register_script('corona-script', plugins_url('js/corona.js', __FILE__) );
  wp_enqueue_style( 'corona-style-fa' );
  wp_enqueue_style( 'corona-style' );
  wp_enqueue_script( 'corona-script' );
}

function corona_admin_menu() {
  echo '<div class="wrap"><h2>Willkommen im Corona Verify Admin Dashboard</h2></div>';
  echo '<div class="wrap">Hier haben Sie die Möglichkeit, die Mitarbeiter Ihrer Firma zu hinterlegen und durchgführte Corona Test zu dokumentieren.</div>';
}
add_action('admin_menu','corona_menu_creator');

/* 
* ShortCode [corona-verify-form]
* @param qr (1 || 0) >> display QR Code or not 
*/
function corona_verify_shortcode( $atts, $content = null, $tag = '') {
  global $wpdb;

  $a = shortcode_atts( array(
		'qr' => '0',
	), $atts );

  if(null!=$a){
    $qr = $a['qr'];
  }
  
  $ident = $_GET['ident'] ?? 'null';
  if($ident === 'null' || null == $ident){
    $personId = get_current_user_id();
    $showQR = true;
  }else{
    $showQR = false;
    $lesbar = CV_SECURE::decrypt($ident, "Wissen=M8");
    $paramPersId = explode("&", $lesbar)[0];
    $personId = explode("=", $paramPersId )[1];
    $paramTestId = explode("&", $lesbar)[1];
    $testId = explode("=", $paramTestId)[1];
  }

  // call DB Data
  $result = CV_DB::getLastTestForEmployee($personId);

  echo '<div class="corona-verify-form">
      <div class="corna-verify-heading"><h1>3G Verifizierung</h1>';  
        if ($wpdb->num_rows > 0) {      
          $test_ergebnis = $result[0];
          if(CV_UTILS::isGueltig($test_ergebnis->expired) == 1){
            echo '<div class="corna-verify-container-item">
                  <div class="paragraf"><p>Wir sind nach § 28 Infektionsschutzgesetz verpflichtet, 
                  den 3G-Status jedes unserer Mitarbeiter festzustellen und das Ergebnis zu dokumentieren. 
                  Einen gültigen 3G-Status hat derjenige, der entweder geimpft, genesen oder getestet ist. 
                  Wir versichern, dass jede Person, für die hier ein gültiger Status angezeigt wird, eine der vorgenannten Bedingungen erfüllt.</p></div>
                  <div class="corna-verify-container">
                  <label>Name</label>
                  <div class="name"><b>' .$test_ergebnis->vorname. ' ' .$test_ergebnis->name.'</b></div>
            <div class="ergebnis">';
            if($test_ergebnis->ergebnis === 'positiv'){
              echo '<div class="positiv">';
              echo '<b>Unser Mitarbeiter hat <u>KEINEN</u> gültigen ' .$test_ergebnis->status. ' Status</b>';
            }else{
              echo '<div class="negativ">';
              echo '<div class="greenBackground"><div class="aktuellesDatum">' .$DateAndTime = date('d.m.Y H:i', time()). ' Uhr</div> <b>3-G Status gültig</b></div>';
              if($qr == 1){
                $personId = get_query_var('persId', -1 );
                $testId = get_query_var( 'testId', -1 );
                if($personId == -1 || $testId == -1){
                  $personId = get_current_user_id();
                  $testId = $test_ergebnis->persID;
                }
                if($showQR){
                  echo '<div class="qr">';
                  echo '' .CV_QR::getCode($test_ergebnis->persID,$testId);
                  echo '</div>'; 
                }
              } 
            }
          }else{
            echo '<div class="expired">';
            echo '<b>Dieser Link ist nicht mehr gültig</b>';
          }
        }else{
            echo '<div class="expired">';
            echo '<b>Dieser Link ist nicht mehr gültig</b>';
        } 
            echo '</div></div><div></div></div>';
}
add_shortcode( 'corona-verify-form', 'corona_verify_shortcode' );

/* 
* Adding Admin Menu and Register Styles and Scripts
*/
function corona_admin_menu_CoronaTestOverview() {
  echo '<div class="wrap"><h2>Übersicht durchgeführter Tests pro Mitarbeiter</h2></div>';    
  global $wpdb;
  echo '<div class="wrap"><h3>Einen Corona Test erfassen</h3></div>';
  echo '<form method="POST">';
  echo '<div class=""><div class="divTable"><div class="divRow">';
  $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
  // Array of WP_User objects.
  echo '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">';
  echo '<option value=""></option>';
  foreach ( $blogusers as $user ) {
    echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
  }
  echo '</select></div>';
  echo '<div class="divCell"><b>Datum </b><input class="input-text" type="datetime-local"" name="datum" placeholder="Datum"/></div>';
  echo '<div class="divCell"><b>Testergebnis </b><select placeholder="Testergebnis" name="ergebnis" id="ergebnis">';
  echo '<option value=""></option>
  <option value="negativ">Negativ</option>
  <option value="negativ">Positiv</option>
  </select></div>';
  echo '<div class="divCell"><b>Symptome </b><select placeholder="Symptome" name="symptom" id="symptom">
  <option value=""></option>
  <option value="0">Nein</option>
  <option value="1">Ja</option>
  </select></div>';
  echo '<div class="divCell"><button type="submit" name="submit">speichern</button></div>';
  echo '</form></div></div></div>';  
  
  // call DB Data   
  $result = CV_DB::getTestsForEmployees();
  
  echo '</br><div class="wrap"><h3>'.$wpdb->num_rows.' durchgeführte Tests</h3></div>';        
    if ($wpdb->num_rows > 0) {
      echo '<div class="tableContainer">
            <div class="divTable">
            <div class="divRow headerRow">
            <div class="divCell header">Test Id</div>
            <div class="divCell header">Personen Id</div>
            <div class="divCell header">Vorname</div>
            <div class="divCell header">Nachname</div>
            <div class="divCell header">Status</div>
            <div class="divCell header">Test Datum</div>
            <div class="divCell header">Test Uhrzeit</div>
            <div class="divCell header">Testergebnis</div>
            <div class="divCell header">Symptome</div>
            <div class="divCell header">Gültigkeit</div>
            </div>';
            
            // output data of each row
            foreach($result as $test_ergebnis) {
              echo '<div class="divRow">
              <div class="divCell">'.$test_ergebnis->id.'</div>
              <div class="divCell">'.$test_ergebnis->persID.'</div>
              <div class="divCell">'.$test_ergebnis->vorname.'</div>
              <div class="divCell">'.$test_ergebnis->name.'</div>
              <div class="divCell">'.$test_ergebnis->status.'</div>
              <div class="divCell">'.$test_ergebnis->datum.'</div>
              <div class="divCell">'.$test_ergebnis->zeit.'</div>
              <div class="divCell">'.$test_ergebnis->ergebnis.'</div>';
              if($test_ergebnis->symptom == 0){
                echo '<div class="divCell">nein</div>';
              }else{
                echo '<div class="divCell">ja</div>';
              }
              echo '<div class="divCell">'.$test_ergebnis->expiredDate. ' - ' .$test_ergebnis->expiredTime. '</div></div>';
            }
            echo '</div></div></div>';
      } else {
            echo '0 results';
      }

      if(isset($_POST['submit'])){
        $id=$_POST['id'];
        $datum=$_POST['datum'];
        $ergebnis=$_POST['ergebnis'];
        $symptom=$_POST['symptom'];
        $expired = new DateTime($datum, new DateTimeZone("CET"));
        $expired = $expired->add(new DateInterval('PT25H'));
        $expired = $expired->format('Y-m-d H:i:s');
        $dateFormat = date('Y-m-d\TH:i:s.uP');
        $timestamp = date('Y-m-d H:i:s');
  
          $sql = "INSERT INTO cv_test_for_employee (persId, dateTime, ergebnis, symptom, dateExpired) VALUES ($id, '$timestamp', '$ergebnis', $symptom, '$expired')";
  
          if ($wpdb->get_results($sql)=== TRUE) {
            echo "Der Test zum Mitarbeiter wurde erfolgreich gespeichert.";
          }
    }
}

function corona_admin_menu_CoronaEmployees() {
  global $wpdb;
    echo '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2></div>';
    echo '<div class="wrap"><h3>Einen Mitarbeiter erfassen</h3></div>';
    echo '<form method="POST">';
    echo '<div class=""><div class="divTable"><div class="divRow">';
    $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
    echo '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">';
    echo '<option value=""></option>';
    foreach ( $blogusers as $user ) {
      echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
    }
    echo '</select></div>';
    echo '<div class="divCell"><b>Status </b><select placeholder="status" name="status" id="status">
    <option value=""></option>
    <option value="3G">3G</option>
    <option value="2G">2G</option>
    <option value="2G+">2G+</option>
    </select></div>';
    echo '<div class="divCell"><button type="submit" name="submit">speichern</button></div>';
    echo '</form></div></div></div>';  
    
    // call DB Data
    $result = CV_DB::getEmployees();

    echo '</br><div class="wrap"><h3>'.$wpdb->num_rows.' vorhandene Mitarbeiter</h3></div>';   

    if ($wpdb->num_rows > 0) {
      echo '<div class="tableContainer">
      <div class="divRow headerRow">
      <div class="divCell header">Personen Id</div>
      <div class="divCell header">Vorname</div>
      <div class="divCell header">Nachname</div>
      <div class="divCell header">Status</div></div>';
              
      // output data of each row
      foreach($result as $mitarbeiter) {
        echo '<div class="divRow">
        <div class="divCell">'.$mitarbeiter->persID.'</div>
        <div class="divCell">'.$mitarbeiter->vorname.'</div>
        <div class="divCell">'.$mitarbeiter->name.'</div>
        <div class="divCell">'.$mitarbeiter->status.'</div></div>';
      }
      echo '</div>';

      } else {
        echo "0 results";
      }
               
    if(isset($_POST['submit'])){
      $id=$_POST['id'];
      $status=$_POST['status'];
      $user = get_user_by('ID',$id);

      if($user){
        $firstname=$user->first_name;
        $lastname=$user->last_name;
      }

      if(null!=$id && null!=$id && null!=$lastname){
      $sql = "INSERT INTO cv_employeee (persID, vorname, name, status) VALUES ($id, '$firstname', '$lastname','$status')";

      if ($wpdb->get_results($sql)=== TRUE) {
        echo "Der Mitarbeiter $firstname $lastname wurde erfolgreich gespeichert.";
      }
    }else{
      echo "Bitte alle Felder ausfüllen";
    }
  }
}