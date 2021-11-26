<?php


if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/*
* ###########################
* Register Styles and Scripts
*/
function wpdocs_register_plugin_styles() {
  wp_register_style( 'corona-verify', plugins_url( '../css/front-style.css' ) );
  wp_register_style( 'corona-verify-fa', plugins_url( '../css/fa/css/all.css' ) );
  wp_enqueue_style( 'corona-verify' );
  wp_enqueue_style( 'corona-verify-fa' );
}
add_action( 'wp_enqueue_scripts', 'wpdocs_register_plugin_styles' );

/*
* ###########################
* Register Styles and Scripts
*/
function corona_menu_creator() {
  add_menu_page('Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin-menu', 'corona_admin_menu', 'dashicons-editor-customchar' , 4 ); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Liste', 'Mitarbeiter', 'manage_options', 'corona_admin_menu_CoronaEmployees', 'corona_admin_menu_CoronaEmployees'); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Testübersicht', 'Testübersicht', 'manage_options', 'corona_admin_menu_CoronaTestOverview', 'corona_admin_menu_CoronaTestOverview'); 

  wp_register_style( 'corona-style', plugins_url('../css/front-style.css', __FILE__) );
  wp_register_style( 'corona-style-fa', plugins_url('../css/fa/css/all.css', __FILE__) );
  wp_register_script('corona-script', plugins_url('../js/corona.js', __FILE__) );
  wp_enqueue_style( 'corona-style-fa' );
  wp_enqueue_style( 'corona-style' );
  wp_enqueue_script( 'corona-script' );
}

function corona_admin_menu() {
  echo '<div class="wrap"><h1>Willkommen im Corona Verify Admin Dashboard</h2></div>';
  echo '<div class="wrap">Hier haben Sie die Möglichkeit, die Mitarbeiter Ihrer Firma zu hinterlegen und durchgführte Corona Test zu dokumentieren.</div>';
}
add_action('admin_menu','corona_menu_creator');

/* 
* ####################
* Admin Menu - Employees
*/
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
      <div class="divTable">
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
      echo '</div></div>';

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
        echo ''. CV_DB::insertEmployee($id, $firstname, $lastname,$status);
      }else{
        echo "Bitte alle Felder ausfüllen";
      }
    }
}

/* 
* ####################
* Admin Menu - Test Overview
*/
function corona_admin_menu_CoronaTestOverview() {
  echo '<div class="wrap"><h2>Übersicht durchgeführter Tests pro Mitarbeiter</h2></div>';    
  global $wpdb;
  echo '<div class="wrap"><h3>Einen Corona Test erfassen</h3></div>';
  echo '<form method="POST">';
  echo '<div class=""><div class="divTable"><div class="divRow">';
  
  $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
  
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
  
        echo ''. CV_DB::insertTestForEmployee($id, $timestamp, $ergebnis, $symptom, $expired); 

    }
}
