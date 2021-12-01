<?php

require_once __DIR__ . '/../class/employee.table.class.php';
require_once __DIR__ . '/../class/testview.table.class.php';
require_once __DIR__ . '/../class/option.class.php';



/* 
* ####################
* Admin Menu - Employees
*/
function corona_admin_menu()
{
  $options = new CV_OPTIONS();
  echo '<div class="wrap"><h1>Willkommen im Corona Verify Admin Dashboard</h2></div>';

// check user capabilities
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? $_GET['tab'] : $default_tab;
$requestPage = $_REQUEST["page"];

echo "
  <nav class='nav-tab-wrapper' style='margin-top:20px;'>";
    echo "<a href='?page=" .$requestPage. "&tab=settings' class='nav-tab ";
    if($tab==='settings' || $tab==null){
      echo "nav-tab-active'>";
    }else{
      echo "'>";
    };
    echo "Einstellungen</a>";

    echo "<a href='?page=" .$requestPage. "&tab=tools' class='nav-tab ";
    if($tab==='tools'){
      echo "nav-tab-active'>";
    }else{
      echo "'>";
    };
    echo "Werkzeuge</a>";
    echo "</nav>";

    echo "<div class='tab-content'>";
    switch($tab) :
    case 'tools':
      echo "<div class='wrap'><h3>Nutzung</h3></div>";
      echo "
        <table class='form-table'>
          <tbody>
            <tr class='cv_shortcode'>
              <th scope='row'>shortCode</th>
              <td>		
              <code>[corona-verify-form]</code>
              <div class='option_info'>Erstellen Sie seine Seite und tragen Sie dort den oberhalb angezeigten Shortcode ein.</br>Ab sofort können Sie diese Seite aufrufen und bekommen die Verifizierungsseite angezeigt.</div>
				      </td>
            </tr>
          </tbody>
        </table>";
      break;
    default:
      echo "<div class='wrap'><h3>Optionen</h3></div>";
      echo "<form method='POST'>";
      echo "
        <table class='form-table'>
          <tbody>
            <tr class='cv_verifizierungskennzeichen'>
              <th scope='row'>Verifizierungskennzeichen</th>
              <td>		
                <input type='text' id='cv_verifizierungskennzeichen' name='cv_verifizierungskennzeichen' value='" .$options->readOption('cv_verifizierungskennzeichen'). "' class='regular-text'> 
                <div class='option_info'>Wird im Kopf der Verifizierungsseite gezeigt.</div>
				      </td>
            </tr>
            <tr class='cv_verifizierungsstatus'>
            <th scope='row'>Verifizierungsstatus</th>
            <td>		
              <input type='text' id='cv_verifizierungsstatus' name='cv_verifizierungsstatus' value='" .$options->readOption('cv_verifizierungsstatus'). "' class='regular-text'> 
              <div class='option_info'>Wird als Status unterhalb eines negativen Testergebnis auf der Verifizierungsseite gezeigt.</div>
            </td>
          </tr>
            <tr class='cv_max_rows'>
            <th scope='row'>Maximale Zeilenanzahl</th>
            <td>		
              <input type='number' min='2' max='100' id='cv_max_rows' name='cv_max_rows' required value='" .$options->readOption('cv_max_rows'). "' class='regular-text'> 
              <span class='validity'></span>
              <div class='option_info'>Legt fest, wie viele Zeilen in der Tabelle der Mitarbeiter und der Tabelle Mitarbeiter Tests angezeigt werden sollen.</div>
            </td>
          </tr>
            <tr class='cv_settings_update_time'>
              <th scope='row'></th>
              <td>
                <input type='hidden' id='cv_settings_update_time' name='cv_settings_update_time' value='' class='regular-text'>
              </td>
            </tr>
            <tr class='cv_qr_support'>
              <th scope='row'>Zeige QR Code</th>
              <td>
                <input type='checkbox' name='cv_qr' value='yes'";
                if ( 'yes' === $options->readOption('cv_qr')){
                  echo " checked='checked' >";
                } else{
                  echo " >";
                }
                echo "
                <div class='option_info'>Legt fest, ob eine QR Code auf der Verifizierungsseite für einen Kunden angezeigt werden soll.</div>
              </td>
            </tr>
          </tbody>
        </table>";
      
      echo "<p class='submit'><input type='submit' name='submit' id='submit' class='button button-primary' value='Änderungen speichern'></p>";
      echo "</form></div>"; 
    
      // Speichern der Optionen
      if(isset($_POST['submit'])){
        $vk=$_POST['cv_verifizierungskennzeichen'];
        $vs=$_POST['cv_verifizierungsstatus'];
        $qr=$_POST['cv_qr'];
        $mr=$_POST['cv_max_rows'];
    
        $options->updateOrAddOption('cv_max_rows', $mr, '', '');
        $options->updateOrAddOption('cv_verifizierungskennzeichen', $vk, '', '');
        $options->updateOrAddOption('cv_verifizierungsstatus', $vs, '', '');
        $options->updateOrAddOption('cv_qr', $qr, '', '');
        $options->updateOrAddOption('cv_settings_update_time', new DateTime(), '', '');
        echo '</div></div>';  
        
        wp_redirect( esc_url( add_query_arg()));
      }
    endswitch;
  echo "</div></div>";

}

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
  echo '<div class="option_info">Hier finden Sie Benutzer, welche Sie vorab <a href="users.php">hier</a> als Benutzer im Wordpress angelegt haben.</br>Es werden Benutzer mit der Berechtigung <i>Administrator</i> und <i>Abonnent</i> gefunden.</div>';
  echo '<div class="tableContainer">';
  echo '<div class="divRow">';
  $blogusers = get_users( array( 'role__in' => array( 'Administrator','subscriber' ) ) );
  echo '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">';
  echo '<option value=""></option>';
  foreach ( $blogusers as $user ) {
    echo '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
  }
  echo '</select></div>';
  echo '<div class="divCell"><button type="submit" name="submit">speichern</button></div>';
  echo '</form>'; 
  echo '</div></div>'; 

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
  <option value="positiv">Positiv</option>
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

