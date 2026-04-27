<?php

namespace App\Models;

class Features_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'features';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $features_table = $this->db->prefixTable('features');
        $types_table = $this->db->prefixTable('feature_types');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND f.id=" . $this->db->escape($id);
        }

        $sql = "SELECT f.*, ft.id AS type_id, ft.title AS type_title
                FROM $features_table f
                LEFT JOIN $types_table ft ON ft.id = f.type
                WHERE f.deleted=0 $where
                ORDER BY f.id ASC";

        return $this->db->query($sql);
    }
}
