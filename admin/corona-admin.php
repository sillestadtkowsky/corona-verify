<?php

require_once __DIR__ . '/../class/employee.table.class.php';
require_once __DIR__ . '/../class/testview.table.class.php';

/* 
* ####################
* Admin Menu - Employees
*/
function corona_admin_menu_CoronaEmployees() {
  if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
  }
  global $wpdb;
  echo '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2></div>';
  echo '<div class="wrap"><h3>Einen Mitarbeiter erfassen</h3></div>';
  echo '<form method="POST">';
  echo '<div class="divRow">';
  $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
  echo '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">';
  echo '<option value=""></option>';
  foreach ( $blogusers as $user ) {
    echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
  }
  echo '</select></div>';
  echo '<div class="divCell"><button type="submit" name="submit">speichern</button></div>';
  echo '</form></div>'; 

  if(isset($_POST['submit'])){
    $id=$_POST['id'];
    $user = get_user_by('ID',$id);

    if($user){
      $firstname=$user->first_name;
      $lastname=$user->last_name;
    }

    if(null!=$id && null!=$id && null!=$lastname){
      echo ''. CV_DB::insertEmployee($id, $firstname, $lastname);
      echo '</div></div>';  
    }else{
      echo "Bitte alle Felder ausfüllen";
      echo '</div></div>';  
    }
  }

  $myListTable = new EmployeeTable();
  echo '<div class="wrap"><h3>Registrierte Mitarbeiter</h3>';
  
  $myListTable->prepare_items(); 
  $requestPage = $_REQUEST["page"];
  echo '<form id="events-filter" method="get"><input type="hidden" name="page" value="' .$requestPage. '" />';
  $myListTable->display(); 
  echo '</form>';
  echo '</div></div>'; 
}

/* 
* ####################
* Admin Menu - Test Overview
*/
function corona_admin_menu_CoronaTestOverview() {

  echo '<div class="wrap"><h2>Übersicht durchgeführter Tests pro Mitarbeiter</h2></div>';    
  global $wpdb;
  echo '<div class="wrap"><h3>Einen Corona Test erfassen</h3>';
  echo '<form method="POST">';
  echo '<div class="divRow">';
  
  // lade Mitarbeiter Daten aus DB
  $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
  
  // zeige Mitarbeiter DropDown
  echo '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">';
  echo '<option value=""></option>';
  
  foreach ( $blogusers as $user ) {
    echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
  }
  
  echo '</select></div>';

  // Testzeitpunkt
  echo '<div class="divCell"><b>Datum </b><input class="input-text" type="datetime-local"" name="datum" placeholder="Datum"/></div>';
  
  // zeige Testergebnis DropDown
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
  echo '</form></div>';  
  
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
        echo '</div></div>';  

    }
    $myListTable = new TestViewTable();
    echo '<div class="wrap"><h3>Registrierte Mitarbeiter</h3>';
    
    $myListTable->prepare_items(); 
    $requestPage = $_REQUEST["page"];
    echo '<form id="events-filter" method="get"><input type="hidden" name="page" value="' .$requestPage. '" />';
    $myListTable->display(); 
    echo '</form>';
    echo '</div></div>'; 
}

