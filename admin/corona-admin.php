<?php

require_once __DIR__ . '/../class/table.class.php';

/* 
* ####################
* Admin Menu - Employees
*/
function corona_admin_menu_CoronaEmployees() {
  global $wpdb;
    echo '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2></div>';
    echo '<div class="wrap"><h3>Einen Mitarbeiter erfassen</h3></div>';
    echo '<form method="POST">';
    echo '<div class=""><div class="divRow">';
    $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
    echo '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">';
    echo '<option value=""></option>';
    foreach ( $blogusers as $user ) {
      echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
    }
    echo '</select></div>';
    echo '<div class="divCell"><button type="submit" name="submit">speichern</button></div>';
    echo '</form></div></div>';  
    
    // call DB Data
    $result = CV_DB::getEmployees();

    echo '</br><div class="wrap"><h3>'.$wpdb->num_rows.' vorhandene Mitarbeiter</h3></div>';   

    if ($wpdb->num_rows > 0) {
      echo '<div class="tableContainer">
      <div class="divTable">
      <div class="divRow headerRow">
      <div class="divCell header">Nachname</div>
      <div class="divCell header">Vorname</div>
      <div class="divCell header">Personen Id</div></div>';        
      // output data of each row
      foreach($result as $mitarbeiter) {
        echo '<div class="divRow">
        <div class="divCell">'.$mitarbeiter->persID.'</div>
        <div class="divCell">'.$mitarbeiter->vorname.'</div>
        <div class="divCell">'.$mitarbeiter->name.'</div></div>';
      }
      echo '</div></div>';

    } else {
      echo "0 results";
    }
               
    if(isset($_POST['submit'])){
      $id=$_POST['id'];
      $user = get_user_by('ID',$id);

      if($user){
        $firstname=$user->first_name;
        $lastname=$user->last_name;
      }

      if(null!=$id && null!=$id && null!=$lastname){
        echo ''. CV_DB::insertEmployee($id, $firstname, $lastname);
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
  echo '<div class=""><div class="divRow">';
  
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
  echo '</form></div></div>';  
  
  // call DB Data   
  $result = CV_DB::getTestsForEmployees();
  
  echo '</br><div class="wrap"><h3>'.$wpdb->num_rows.' durchgeführte Tests</h3></div>';        
    if ($wpdb->num_rows > 0) {
      echo '<div class="tableContainer">
            <div class="divTable">
            <div class="divRow headerRow">
            <div class="divCell header">Personen Id</div>
            <div class="divCell header">Vorname</div>
            <div class="divCell header">Nachname</div>
            <div class="divCell header">Test Id</div>
            <div class="divCell header">Test Datum</div>
            <div class="divCell header">Test Uhrzeit</div>
            <div class="divCell header">Testergebnis</div>
            <div class="divCell header">Symptome</div>
            <div class="divCell header">Gültigkeit</div>
            </div>';
            
            // output data of each row
            foreach($result as $test_ergebnis) {
              echo '<div class="divRow">
              <div class="divCell">'.$test_ergebnis->persID.'</div>
              <div class="divCell">'.$test_ergebnis->vorname.'</div>
              <div class="divCell">'.$test_ergebnis->name.'</div>
              <div class="divCell">'.$test_ergebnis->id.'</div>
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

/* 
* ####################
* Admin Menu - Test Overview
*/
function corona_admin_menu_WpTableExample() {
  if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
  }

  $myListTable = new My_List_Table();
  echo '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2>'; 
  
  $myListTable->prepare_items(); 
  $requestPage = $_REQUEST["page"];
  echo '<form id="events-filter" method="post"><input type="hidden" name="page" value="' .$requestPage. '" />';
  $myListTable->display(); 
  echo '</form>';
  echo '</div>'; 
}


