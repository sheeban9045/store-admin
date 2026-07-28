<?php

namespace App\Models;

class Webhut_orders_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'webhut_orders';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $plugins_table = $this->db->prefixTable('webhut_orders');

        $where = "1";
        $id = get_array_value($options, "id");

        if ($id) {
            $where .= " AND id=" . $this->db->escape($id);
        }

        $sql = "SELECT *
                FROM $plugins_table
                WHERE $where
                ORDER BY id DESC";

        return $this->db->query($sql);
    }

    function get_plugin_purchased_by_client($where = "1") {
        $plugins_table = $this->db->prefixTable('webhut_orders');

        $sql = "SELECT *
                FROM $plugins_table
                WHERE $where
                ORDER BY id DESC";

        return $this->db->query($sql);
    }

    function get_orders_by_user($user_id) {
        $orders_table = $this->db->prefixTable('webhut_orders');
        $plugins_table = $this->db->prefixTable('webhut_plugins');

        return $this->db->table($orders_table . ' as o')
            ->select('o.*, p.name as plugin_name, p.icon as plugin_icon, p.tar_file as plugin_tar_file')
            ->join($plugins_table . ' as p', 'p.id = o.plugin_id', 'left')
            ->where('o.user_id', $user_id)
            ->orderBy('o.id', 'DESC')
            ->get();
    }
}