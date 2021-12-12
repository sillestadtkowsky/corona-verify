<?php

/* 
* ##########################
* Admin Menu - Test Overview
* ##########################
*/
function corona_admin_menu_CoronaTestOverview() {
  
    global $wpdb;
    $html = '';
  
    $html .= '<div class="wrap"><h2>Übersicht durchgeführter Tests pro Mitarbeiter</h2></div>';    
    
    $html .= '<div class="wrap"><h3>Einen Corona Test erfassen</h3>
                <form method="POST">
                  <div class="divRow">';
                  // lade Mitarbeiter Daten aus DB
                  $blogusers = CV_DB::getEmployees();
    
                  // zeige Mitarbeiter DropDown
                  $html .= '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">
                  <option value=""></option>';
    
                  foreach ( $blogusers as $user ) {
                    $html .= '<option value="' . esc_sql( $user->persId ) . '">' . esc_sql( $user->firstname ) . ' '. esc_sql( $user->lastname ) . '</option>';
                  }
    
                  $html .='</select></div>';
  
    // Testzeitpunkt
    $html .='<div class="divCell"><b>Datum </b><input class="input-text" type="datetime-local" name="datum" placeholder="Datum"/></div>
                <div class="divCell"><b>Testergebnis </b>
                  <select placeholder="Testergebnis" name="ergebnis" id="ergebnis">
                    <option value=""></option>
                    <option value="negativ">Negativ</option>
                    <option value="positiv">Positiv</option>
                  </select>
                </div>
                <div class="divCell"><b>Symptome </b>
                  <select placeholder="Symptome" name="symptom" id="symptom">
                    <option value=""></option>
                    <option value="0">Nein</option>
                    <option value="1">Ja</option>
                  </select>
                </div>
                <div class="divCell">
                  <button type="submit" name="submit">speichern</button>
                </div>
                </form>
              </div>';
    
      if(isset($_POST['submit'])){
          $id=sanitize_text_field($_POST['id']);
          $datum=sanitize_text_field($_POST['datum']);
          $ergebnis=sanitize_text_field($_POST['ergebnis']);
          $symptom=sanitize_text_field($_POST['symptom']);
          $expired = new DateTime($datum);
          $expired = $expired->add(new DateInterval('PT24H'));
          $expired = $expired->format('Y-m-d H:i:s');
          $dateFormat = date('Y-m-d\TH:i:s.uP');
          $timestamp = new DateTime($datum);
          $timestamp = $timestamp->format('Y-m-d H:i:s');
          $html .= CV_DB::insertTestForEmployee($id, $timestamp, $ergebnis, $symptom, $expired); 
          $html .='</div></div>';  
  
      }
      
      $myListTable = new TestViewTable();
      $html .='<div class="wrap"><h3>Übersicht der verifizierten Tests</h3>';
      $myListTable->prepare_items(); 
      $requestPage = sanitize_text_field($_REQUEST["page"]);
      $html .='<form id="events-filter" method="get"><input type="hidden" name="page" value="' . sanitize_text_field($requestPage) . '" />';
      
      echo $html;
      
      $myListTable->display(); 
      $html = '';
      $html .= '</form></div></div>'; 
    
      echo $html;
  }