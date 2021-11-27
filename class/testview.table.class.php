<?php
require_once __DIR__ . '/db.class.php';

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class TestViewTable extends WP_List_Table
{

  function get_columns()
  {
    $columns = array(
      'cb' => '<input type="checkbox" />',
      'persId' => 'Person Nummer',
      'firstname' => 'Vorname',
      'lastname' => 'Nachname',
      'id' => 'Test Nummer',
      'datum' => 'Test Datum',
      'zeit' => 'Test Uhrzeit',
      'testresult' => 'Test Ergebnis',
      'symptom' => 'Sympthome',
      'expiredDate' => 'Gültigkeit Datum',
      'expiredTime' => 'Gültigkeit Uhrzeit'
    );
    return $columns;
  }

  function prepare_items()
  {
    $data = CV_DB::getTestsForEmployeesArray();

    $per_page = 8;
    $current_page = $this->get_pagenum();
    $total_items = count($data);
    $this->set_pagination_args( array('total_items' => $total_items,'per_page' => $per_page ));
    
    if (1 < $current_page) {
      $offset = $per_page * ($current_page - 1);
    } else {
      $offset = 0;
    }

    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    usort($data, array(&$this, 'usort_reorder'));
    $this->items = array_slice($data,(($current_page-1)*$per_page),$per_page);
    $this->process_bulk_action();
  }

  function column_default($item, $column_name)
  {
    switch ($column_name) {
      case 'persId':
      case 'firstname':
      case 'lastname':
      case 'id':
      case 'datum':
      case 'zeit':
      case 'testresult':
      case 'symptom':
      case 'expiredDate':
      case 'expiredTime':
        return $item[$column_name];
      default:
        return print_r($item, false); //Show the whole array for troubleshooting purposes
    }
  }


  function deleteEmployee($actions)
  {
    echo 'delete';
  }

  function get_sortable_columns()
  {
    $sortable_columns = array(
      'persId' => array('persId', true),
      'firstname' => array('firstname', true),
      'lastname'   => array('lastname', true),
      'id' => array('id', true),
      'datum' => array('datum', true),
      'zeit' => array('zeit', true),
      'testresult' => array('testresult', true),
      'symptom' => array('symptom', true),
      'expiredDate' => array('expiredDate', true),
      'expiredTime' => array('expiredTime', true)
    );
    return $sortable_columns;
  }

  function usort_reorder($a, $b)
  {
    $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
    $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
    $testresult = strcmp($a[$orderby], $b[$orderby]);
    return ($order === 'desc') ? $testresult : -$testresult;
  }

  function column_name($item)
  {

    $actions = array(
      'delete'    => sprintf('<a href="?page=%s&action=%s&id[]=%s">Löschen</a>', '' . $_REQUEST["page"] . '', 'delete', $item['id']),
    );

    return sprintf(
      '%1$s <span style="color:silver ; display : none;">(id:%2$s)</span>%3$s',
      /*$1%s*/
      $item['lastname'],
      /*$2%s*/
      $item['id'],
      /*$3%s*/
      $this->row_actions($actions)
    );
  }

  function column_cb($item)
  {
    return sprintf(
      '<input type="checkbox" lastname="%1$s[]" value="%2$s" />',
      $item['id'], 
      $item['id']
    );
  }
  function get_bulk_actions()
  {
    $actions = array(
      'delete'    => 'Löschen'
    );
    return $actions;
  }

  public function process_bulk_action()
  {

    // If the delete bulk action is triggered
    $action = $this->current_action();
    if ('delete' === $action) {
      $delete_ids = esc_sql($_GET['id']);
      // loop over the array of record IDs and delete them
      foreach ($delete_ids as $did) {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM mncplugin WHERE id='" . $did . "'"));
      }

      wp_redirect(esc_url(add_query_arg()));
      exit;
    }
  }
}
