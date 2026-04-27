<?php

namespace App\Models;

class Features_type_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'feature_types';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $features_types_table = $this->db->prefixTable('feature_types');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id)
            $where .= " AND id=$id";

        $limit_query = "";
        $limit = get_array_value($options, "limit");
        if ($limit) {
            $offset = get_array_value($options, "offset");
            $limit_query = "LIMIT $offset, $limit";
        }

        $sql = "SELECT *  FROM $features_types_table
        WHERE deleted=0 $where ORDER BY `sort_order` ASC
        $limit_query";
        return $this->db->query($sql);
    }
}
