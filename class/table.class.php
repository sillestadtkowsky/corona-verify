<?php
require_once __DIR__ . '/db.class.php';

if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class My_List_Table extends WP_List_Table
{

  function get_columns()
  {
    $columns = array(
      'cb' => '<input type="checkbox" />',
      'name'      => 'Nachname',
      'vorname'    => 'Vorname',
      'persID' => 'Personen ID'
    );
    return $columns;
  }

  function prepare_items()
  {
    $data = CV_DB::getEmployeesArray();

    $per_page = 20;
    $current_page = $this->get_pagenum();
    if (1 < $current_page) {
      $offset = $per_page * ($current_page - 1);
    } else {
      $offset = 0;
    }
    $this->process_bulk_action();
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    usort($data, array(&$this, 'usort_reorder'));
    $this->items = $data;
  }

  function column_default($item, $column_name)
  {
    switch ($column_name) {
      case 'persID':
      case 'vorname':
      case 'name':
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
      'persID'  => array('persID', true),
      'vorname' => array('vorname', true),
      'name'   => array('name', true)
    );
    return $sortable_columns;
  }

  function usort_reorder($a, $b)
  {
    $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'name';
    $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
    $result = strcmp($a[$orderby], $b[$orderby]);
    return ($order === 'desc') ? $result : -$result;
  }

  function column_name($item)
  {

    $actions = array(
      'delete'    => sprintf('<a href="?page=%s&action=%s&persID[]=%s">Löschen</a>', '' . $_REQUEST["page"] . '', 'delete', $item['persID']),
    );

    return sprintf(
      '%1$s <span style="color:silver ; display : none;">(id:%2$s)</span>%3$s',
      /*$1%s*/
      $item['name'],
      /*$2%s*/
      $item['persID'],
      /*$3%s*/
      $this->row_actions($actions)
    );
  }

  function column_cb($item)
  {
    return sprintf(
      '<input type="checkbox" name="%1$s[]" value="%2$s" />',
      $item['persID'],  //Let's simply repurpose the table's singular label ("plugin")
      $item['persID']                //The value of the checkbox should be the record's id
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
      $delete_ids = esc_sql($_POST['persID']);
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
