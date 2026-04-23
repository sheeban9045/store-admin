<?php

namespace App\Models;

class manage_users_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'order_items';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $order_items_table = $this->db->prefixTable('order_items');
        $items_table = $this->db->prefixTable('items');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $order_items_table.id=$id";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $order_items_table.created_by=$created_by";
        }

        $order_id = get_array_value($options, "order_id");
        if ($order_id) {
            $where .= " AND $order_items_table.order_id=$order_id";
        }

        $processing = get_array_value($options, "processing");
        if ($processing && $created_by) {
            $where .= " AND $order_items_table.order_id=0";
        }

       
    }

}
