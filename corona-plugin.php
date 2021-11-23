<?php
/**
*
* Plugin Name:       Corona Test Verifyer
* Plugin URI:        https://example.com/plugins/the-basics/
* Description:       Quittiert das Ergebnis eines durchgeführten Test
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


define('CORONA_REGISTRATION_INCLUDE_URL', plugin_dir_url(__FILE__).'css/');

function corona_style_incl(){
  wp_enqueue_style('corona_style_css_and_js', CORONA_REGISTRATION_INCLUDE_URL."front-style.css");
  wp_enqueue_script('corona_style_css_and_js');
}
  
add_action('wp_footer','corona_style_incl');

//Adding Admin Menu
function corona_menu_creator() {
  add_menu_page('Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin-menu', 'corona_admin_menu', 'dashicons-editor-customchar' , 4 ); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Liste', 'Mitarbeiter', 'manage_options', 'corona_admin_menu_employees', 'corona_admin_menu_employees'); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Testübersicht', 'Testübersicht', 'manage_options', 'corona_admin_menu_employees_test', 'corona_admin_menu_employees_test'); 

  wp_register_style( 'corona-style', plugins_url('css/front-style.css', __FILE__) );
  wp_enqueue_style( 'corona-style' );
}

function corona_admin_menu_employees_test() {
  global $wpdb;
  echo '<div class="wrap"><h2>Übersicht durchgeführter Tests pro Mitarbeiter</h2></div>';        
          $sql = "SELECT cv_employeee.persID, cv_employeee.vorname as vorname, cv_employeee.name as name, 
          cv_test_for_employee.persId as persID, DATE_FORMAT(cv_test_for_employee.date, '%d.%m.%Y') as datum , 
          cv_test_for_employee.time as zeit, cv_test_for_employee.ergebnis as ergebnis, 
          cv_test_for_employee.symptom as symptom FROM  cv_employeee RIGHT JOIN cv_test_for_employee ON cv_employeee.persID=cv_test_for_employee.persID";
          $result = $wpdb->get_results($sql);
          
          if ($wpdb->num_rows > 0) {
            echo '<div class="tableContainer">
            <div class="divTable">
                <div class="divRow headerRow">';
            echo '<div class="divCell header">id</div>
            <div class="divCell header">Vorname</div>
            <div class="divCell header">Nachname</div>
            <div class="divCell header">Test Datum</div>
            <div class="divCell header">Test Uhrzeit</div>
            <div class="divCell header">Testergebnis</div>
            <div class="divCell header">Symptome</div>
            </div>';
            
            // output data of each row
            foreach($result as $test_ergebnis) {
              echo '<div class="divRow">';
              echo '<div class="divCell">'.$test_ergebnis->persID.'</div>
              <div class="divCell">'.$test_ergebnis->vorname.'</div>
              <div class="divCell">'.$test_ergebnis->name.'</div>
              <div class="divCell">'.$test_ergebnis->datum.'</div>
              <div class="divCell">'.$test_ergebnis->zeit.'</div>
              <div class="divCell">'.$test_ergebnis->ergebnis.'</div>
              <div class="divCell">'.$test_ergebnis->symptom.'</div></div>'
              ;
            }
            echo '</div></div></div>';
          } else {
            echo "0 results";
          }
}

function corona_admin_menu_employees() {
  global $wpdb;
  echo '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2></div>';
  echo '<div class="wrap"><h3>Einen Mitarbeiter erfassen</h3></div>';
  echo '<form method="POST">';
  echo '<div class=""><div class="divTable"><div class="divRow">';
  echo '<div class="divCell"><input class="input-text" type="text" name="id" placeholder="PersonenID"/></div>';
  echo '<div class="divCell"><input class="input-text" type="text" name="firstname" placeholder="Vorname"/></div>';
  echo '<div class="divCell"><input class="input-text" type="text" name="lastname" placeholder="Nachname"/></div>';
  echo '<div class="divCell"><button type="submit" name="submit">speichern</button></div>';
  echo '</form></div></div></div>';  

  echo '</br><div class="wrap"><h3>Vorhandene Mitarbeiter</h3></div>';
            
    $sql = "SELECT persID, vorname, name FROM cv_employeee";
    $result = $wpdb->get_results($sql);
            
    if ($wpdb->num_rows > 0) {
      echo '<div class="tableContainer">
      <div class="divRow headerRow">';
      echo '<div class="divCell header">id</div>
      <div class="divCell header">Vorname</div>
      <div class="divCell header">Nachname</div></div>';
              
      // output data of each row
      foreach($result as $mitarbeiter) {
        echo '<div class="divRow">';
        echo '<div class="divCell">'.$mitarbeiter->persID.'</div>
        <div class="divCell">'.$mitarbeiter->vorname.'</div>
        <div class="divCell">'.$mitarbeiter->name.'</div></div>';
      }
      echo '</div>';

      } else {
        echo "0 results";
      }
               
    if(isset($_POST['submit'])){
      $id=$_POST['id'];
      $firstname=$_POST['firstname'];
      $lastname=$_POST['lastname'];

      if(null!=$id && null!=$id && null!=$lastname){
      $sql = "INSERT INTO cv_employeee (persID, vorname, name) VALUES ($id, '$firstname', '$lastname')";

      if ($wpdb->get_results($sql)=== TRUE) {
        echo "Der Mitarbeiter $firstname $lastname wurde erfolgreich gespeichert.";
      }
    }else{
      echo "Bitte alle Felder ausfüllen";
    }
  }

}

function corona_admin_menu() {
  echo '<div class="wrap"><h2>Willkommen im Corona Verify Admin Dashboard</h2></div>';
  echo '<div class="wrap">Hier haben Sie die Möglichkeit, die Mitarbeiter Ihrer Firma zu hinterlegen und durchgführte Corona Test zu dokumentieren.</div>';
}
add_action('admin_menu','corona_menu_creator');

  // function to login Shortcode
  
  function corona_login_shortcode( $atts ) {
  ?>

  <div class="corona-verify-form">
      <div class="corna-verify-heading">  
          <?php _e("Corona - Verifizierungsseite",'');?>
      </div>
  </div>
<?php } ?>

<?php
add_shortcode( 'corona-verify-form', 'corona_login_shortcode' );