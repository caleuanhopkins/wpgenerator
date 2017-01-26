<?php

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Functions_customTable extends WP_List_Table {

    var $eDate = 0;
    var $selected_col;
    var $bulkActions = false;
    var $custom_tablenav = null;
    var $itemDelete = true;
    var $itemEdit = true;
    var $orderBy = null;
    var $orderCol = null;
    var $terms = array('singular'=>'item','plural'=>'items','ajax'=> true);

    public function __construct($eDate){
        global $status, $page;
        $this->eDate = $eDate;
        parent::__construct( $this->terms);
    }

    public function no_items() {
        _e( 'No results available.', 'wg_cal' );
    }

    public function column_name( $item ) {
        $theKeys = array_keys($item);
        $edit_nonce = wp_create_nonce( 'wg_cal_edit_item' );
        $delete_nonce = wp_create_nonce( 'wg_cal_delete_item' );

        if(!$this->selected_col){
            $title = '<strong>' . $item[$theKeys[1]] . '</strong>';
        }else{
            $title = '<strong>' . $item[$this->selected_col] . '</strong>';
        }
        $actions = array();
        if($this->itemEdit){
            $actions['edit'] = sprintf( '<a href="?page=%s&action=%s&item=%s&_wpnonce=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item[$theKeys[0]] ), $edit_nonce );
        }
        if($this->itemDelete){
            $actions['delete'] = sprintf( '<a class="delete-item" href="?page=%s&action=%s&item=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item[$theKeys[0]] ), $delete_nonce );
        }   
        return $title . $this->row_actions( $actions );
    }

    public function column_default($item, $column_name){
        $theItem = (array)$item;
        $theKeys = array_keys((array)$item);
        switch($column_name){
            default:
                return $theItem[$column_name];
        }
    }

    public function column_cb($item){
        $theItem = (array)$item;
        $theKeys = array_keys((array)$item);
        return sprintf(
            '<input id="item-'.$theItem[$theKeys[0]].'" type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $theItem[$theKeys[0]]
        );
    }

    public function get_columns($columns=null) {
        return $columns;
    }

    public function get_sortable_columns($columns=null) {
        $sortable_columns = array();
        if(!empty($this->orderBy) && $this->orderBy === 'DESC'){
            $order = false;
        }else{
            $order = true;
        }
        if(is_array($columns)){
            foreach($columns as $key => $col){
                $sortable_columns[$key] = array($key, false);
            }
        }
        return $sortable_columns;
    }

    public function get_bulk_actions() {
        if($this->bulkActions){
            $actions = array(
                'edit'    => 'Edit'
            );
            return $actions;
        }
    }

    public function prepare_items($tableData=null,$columnData=null,$colSortable=null, $selected_col=null) {
        global $wpdb;
        $per_page = 10;
        if(!$columnData){
            $columns = $this->get_columns();
        }else{
            $columns = $columnData;            
        }
        if($selected_col){
            $this->selected_col = $selected_col;
        }
        $hidden = array();
        if(!$colSortable){
            $sortable = $this->get_sortable_columns($columns);
        }else{
            $sortable = $this->get_sortable_columns($colSortable);
        }
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        if(isset($tableData[0]) && !is_array($tableData[0])){
            foreach($tableData as $key => $val){
                $data[$key] = (array)$val;
            }
        }else{
            $data = $tableData;
        }
        usort($data, array($this,'usort_reorder'));
        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items/$per_page)
        ) );
    }

    public function usort_reorder($a,$b){
        $a = (array)$a;
        $b = (array)$b;


       $aKeys = array_keys($a);
        $bKeys = array_keys($b);

        if(!empty($this->orderBy) && $this->orderBy === 'DESC'){
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
        }else{
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
        }

        if(empty($this->orderCol) && empty($_REQUEST['orderby'])){
            if ($a[$aKeys[0]] < $b[$bKeys[0]]) {
                $result = -1;
            } else if ($a[$aKeys[0]] > $b[$bKeys[0]]) {
                $result = 1;
            } else {
                $result = 0;
            }
        }elseif(!empty($_REQUEST['orderby'])){
            if ($a[$_REQUEST['orderby']] < $b[$_REQUEST['orderby']]) {
                $result = -1;
            } else if ($a[$_REQUEST['orderby']] > $b[$_REQUEST['orderby']]) {
                $result = 1;
            } else {
                $result = 0;
            }   
        }else{
            if ($a[$this->orderCol] < $b[$this->orderCol]) {
                $result = -1;
            } else if ($a[$this->orderCol] > $b[$this->orderCol]) {
                $result = 1;
            } else {
                $result = 0;
            }
        }

        return ($order==='desc') ? -$result : $result;
    }

    public function display_tablenav( $which ) 
    {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            <div class="alignleft actions">
                <?php $this->bulk_actions(); ?>
            </div>
            <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }

    public function extra_tablenav( $which ) {
        if($this->custom_tablenav){
            echo $this->custom_tablenav;
        }
    }

}
?>