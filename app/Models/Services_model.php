<?php

namespace App\Models;

class Services_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'services';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $services_table = $this->db->prefixTable('services');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $services_table.id=" . $this->db->escape($id);
        }

        $sql = "SELECT $services_table.*
                FROM $services_table
                WHERE $services_table.deleted=0 $where
                ORDER BY $services_table.id ASC";

        return $this->db->query($sql);
    }

}
