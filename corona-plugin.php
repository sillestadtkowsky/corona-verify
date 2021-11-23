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

function corona_style_incl(){
  wp_enqueue_style('corona_style_css_and_js', CORONA_REGISTRATION_INCLUDE_URL."front-style.css");
  wp_enqueue_script('corona_style_css_and_js');
}
  
add_action('wp_footer','corona_style_incl');

//Adding Admin Menu
function corona_menu_creator() {
  add_menu_page('Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin-menu', 'corona_admin_menu', 'dashicons-editor-customchar' , 4 ); 
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Liste', 'Mitarbeiter', 'manage_options', 'corona_admin_menu_employees', 'corona_admin_menu_employees'); 

  wp_register_style( 'corona-style', plugins_url('css/front-style.css', __FILE__) );
  wp_enqueue_style( 'corona-style' );
}

function corona_admin_menu_employees() {
  echo '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2></div>';

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
              echo '<div class="corona-mitarbeiter-container">Mitarbeiterübersicht<div><table class="collapse">';
              echo '<tr>
                <th>ID</th>
                <th>Vorname</th>
                <th>Nachname</th>
              </tr>';
              // output data of each row
              while($row = $result->fetch_assoc()) {
                echo '<tr>
                <td>'.$row["persID"].'</td>
                <td>'.$row["vorname"].'</td>
                <td>'.$row["name"].'</td>
              </tr>';
              }
              echo '</div></table></div>';
            } else {
              echo "0 results";
            }
            $conn->close();
}
function corona_admin_menu() {
  echo '<div class="wrap"><h2>Willkommen im Corona Verify Admin Dashboard</h2></div>';
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