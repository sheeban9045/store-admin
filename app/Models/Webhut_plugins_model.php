<?php

namespace App\Models;

class Webhut_plugins_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'webhut_plugins';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $plugins_table = $this->db->prefixTable('webhut_plugins');

        $where = "";
        $id = get_array_value($options, "id");

        if ($id) {
            $where .= " AND id=" . $this->db->escape($id);
        }

        $sql = "SELECT *
                FROM $plugins_table
                WHERE deleted=0 $where
                ORDER BY id DESC";

        return $this->db->query($sql);
    }

    public function get_plugin_by_name($name)
    {
        $plugins_table = $this->db->prefixTable('webhut_plugins');

        $sql = "SELECT *
                FROM $plugins_table
                WHERE deleted = 0
                AND name = ?
                LIMIT 1";

        return $this->db->query($sql, [$name])->getRow();
    }

    public function verify_plugin_purchase($plugin_id, $hostname)
    {
        $orders_table = $this->db->prefixTable('webhut_orders');

        $sql = "SELECT id
                FROM $orders_table
                WHERE plugin_id = ?
                AND community = ?
                AND payment_status = 'success'
                AND status = 'success'
                LIMIT 1";

        return $this->db->query($sql, [
            $plugin_id,
            $hostname
        ])->getRow();
    }
}