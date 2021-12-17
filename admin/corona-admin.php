<?php

/*
* ###############################
* ADD Admin Menu
* ###############################
*/
function corona_menu_creator()
{
  add_menu_page('Corona Verify Seite', 'Corona-Admin', 'manage_options', 'corona-admin-menu', 'corona_admin_home', 'dashicons-editor-customchar', 4);
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Liste', 'Mitarbeiter', 'manage_options', 'coronaEmployees', 'corona_admin_menu_CoronaEmployees');
  add_submenu_page('corona-admin-menu', 'Mitarbeiter-Testübersicht', 'Testübersicht', 'manage_options', 'coronaTestOverview', 'corona_admin_menu_CoronaTestOverview');
}
add_action('admin_menu', 'corona_menu_creator');

/* 
* ####################
* ADD Admin Home
* ####################
*/
function corona_admin_home()
{
  ?>
  <div class="wrap">
  <h1>Willkommen im Corona Verify Admin Dashboard</h1>
  </div>
  
  <?php  
  $options = new CV_OPTIONS();

  // check user capabilities
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;

?>
<nav class="nav-tab-wrapper" style="margin-top:20px;">
  <a href="?page=<?php echo sanitize_text_field($_REQUEST["page"])?>&tab=settings" class="nav-tab ';
      <?php  if($tab==="settings" || $tab==null){
              echo 'nav-tab-active">Einstellungen</a>';
            }else{
              echo  '">Einstellungen</a>';
            }
      ?>   
    <a href="?page=<?php echo sanitize_text_field($_REQUEST["page"])?>&tab=tools" class="nav-tab ';
        <?php   if($tab==="tools" || $tab==null){
                  echo 'nav-tab-active">Werkzeuge</a>';
                }else{
                  echo  '">Werkzeuge</a>';
                }
        ?>  
    <div class="tab-content">
      <?php   
        switch($tab) :
          case 'tools':
            echo viewAdminTools();
            break;
          default:
          echo  viewAdminOptions();
        endswitch;
      ?> 
    </div>
  <?php 
}

