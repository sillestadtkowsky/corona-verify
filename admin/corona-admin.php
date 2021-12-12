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
  $html = '';
  $html .= '<div class="wrap">';
  $html .= '<h1>Willkommen im Corona Verify Admin Dashboard</h1>';
  $html .= '</div>';
  $options = new CV_OPTIONS();

  // check user capabilities
  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }

//Get the active tab from the $_GET param
$default_tab = null;
$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $default_tab;
$requestPage = sanitize_text_field($_REQUEST["page"]);

$html .= '<nav class="nav-tab-wrapper" style="margin-top:20px;">
            <a href="?page=' . sanitize_text_field( $requestPage ) . '&tab=settings" class="nav-tab ';
            if($tab==="settings" || $tab==null){
              $html .= 'nav-tab-active">';
            }else{
              $html .= '"> ';
            }
            $html .= 'Einstellungen</a>
              
            <a href="?page='. sanitize_text_field( $requestPage ) .'&tab=tools" class="nav-tab ';
            if($tab==="tools"){
              $html .= 'nav-tab-active">';
            }else{
              $html .= '"> ';
            }
            $html .= 'Werkzeuge</a></nav>';

            $html .= '<div class="tab-content">';
              switch($tab) :
                case 'tools':
                  $html .= viewAdminTools();
                  break;
                default:
                  $html .= viewAdminOptions();
              endswitch;
            $html .= '</div>';

    echo $html;
}

