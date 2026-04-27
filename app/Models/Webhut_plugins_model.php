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
}