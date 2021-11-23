<?php
define('CORONA_REGISTRATION_INCLUDE_URL', plugin_dir_url(__FILE__).'includes/');

function corona_style_incl(){
  wp_enqueue_style('corona_style_css_and_js', CORONA_REGISTRATION_INCLUDE_URL."front-style.css"); 
  wp_enqueue_script('corona_style_css_and_js');
}

add_action('wp_footer','corona_style_incl');

<?php
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
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["persID"]. " - Name: " . $row["vorname"]. " " . $row["name"]. "<br>";
  }
} else {
  echo "0 results";
}
$conn->close();
?>

<?php
function wporg_options_page_html() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields( 'wporg_options' );
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections( 'wporg' );
        // output save settings button
        submit_button( __( 'Save Settings', 'textdomain' ) );
        ?>
      </form>
    </div>
    <?php } ?>
