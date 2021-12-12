<?php

/* 
* ######################
* Admin Menu - Employees
* ######################
*/
function corona_admin_menu_CoronaEmployees() {
    if( ! class_exists( 'WP_List_Table' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
    global $wpdb;
    $html = '';
    $html .= '<div class="wrap"><h2>Übersicht der registrierten Mitarbeiter</h2></div>';
    $html .= '<div class="wrap"><h3>Einen Mitarbeiter erfassen</h3></div>
                <div class="option_info">Hier finden Sie Benutzer, welche Sie <b>vorab</b> als Benutzer im Wordpress angelegt haben.</br>Es werden Benutzer mit der Berechtigung <i>Administrator</i> und <i>Abonnent</i> gefunden.</div>
                  <div class="tableContainer">
                    <form method="POST">
                      <div class="divRow">';
                      $blogusers = get_users( array( 'role__in' => array( 'administrator','subscriber' ) ) );
              $html .= '<div class="divCell"><b>Mitarbeiter </b><select placeholder="Mitarbeiter" name="id" id="id">
                          <option value=""></option>';
                          foreach ( $blogusers as $user ) {
                            $html .= '<option value="' . esc_html( $user->ID ) . '">' . esc_html( $user->first_name ) . ' '. esc_html( $user->last_name ) . '</option>';
                          }
                $html .= '</select></div>
                        <div class="divCell"><button type="submit" name="submit">speichern</button></div>
                    </form>
                  </div>
                </div>';
  
    if(isset($_POST['submit'])){
      $id=sanitize_text_field($_POST['id']);
      $user = get_user_by('ID',$id);
  
      if($user){
        $firstname=$user->first_name;
        $lastname=$user->last_name;
      }
  
      if(null!=$id && null!=$id && null!=$lastname){
        $html .= CV_DB::insertEmployee($id, $firstname, $lastname);
        $html .= '</div></div>';  
      }else{
        $html .= 'Bitte alle Felder ausfüllen';
        $html .= '</div></div>';  
      }
    }
  
    $myListTable = new EmployeeTable();
    $html .= '<div class="wrap"><h3>Registrierte Mitarbeiter</h3>';
    $myListTable->prepare_items(); 
    $requestPage = $_REQUEST["page"];
    $html .= '<form id="events-filter" method="get"><input type="hidden" name="page" value="' .$requestPage. '" />';
    
    echo $html;
    
    $myListTable->display(); 
    $html = '';
    $html .= '</form></div></div>'; 
  
    echo $html;
  }