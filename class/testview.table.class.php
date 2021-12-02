<?php
require_once __DIR__ . '/db.class.php';
require_once __DIR__ . '/option.class.php';

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
    $options = new CV_OPTIONS();
    $data = CV_DB::getTestsForEmployeesArray();

    $per_page = $options->readOption($options::C_TABLE_MAX_ROWS);
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
    $orderby = (!empty($_GET['orderby'])) ? esc_sql($_GET['orderby']) : 'id';
    $order = (!empty($_GET['order'])) ? esc_sql($_GET['order']) : 'asc';
    $testresult = strcmp($a[$orderby], $b[$orderby]);
    return ($order === 'desc') ? $testresult : -$testresult;
  }

  function column_cb($item)
  {
    return sprintf(
      '<input type="checkbox" name="%1$s[]" value="%2$s" />',
      'id',  //Let's simply repurpose the table's singular label ("plugin")
      $item['id']                //The value of the checkbox should be the record's id
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

    //Detect when a bulk action is being triggered...
    if ( 'delete' === $this->current_action() ) {
      if ( false ) {
        die( 'Go get a life script kiddies' );
      }
      else {
      $delete_ids = esc_sql($_GET['id']);
      foreach ( $delete_ids as $id ) {
        echo ''. CV_DB::deleteTestsForEmployees( $id );
      }
      //wp_redirect( esc_url( add_query_arg() ) );
      exit;
    }
  }
  }
}
