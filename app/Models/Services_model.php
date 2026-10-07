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

        $slug = get_array_value($options, "slug");
        if ($slug) {
            $where .= " AND $services_table.slug=" . $this->db->escape($slug);
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $services_table.status=" . $this->db->escape($status);
        }

        $sql = "SELECT $services_table.*
                FROM $services_table
                WHERE $services_table.deleted=0 $where
                ORDER BY $services_table.sort_order ASC, $services_table.id ASC";

        return $this->db->query($sql);
    }

    function is_slug_exists($slug, $id = 0) {
        $services_table = $this->db->prefixTable('services');

        $sql = "SELECT $services_table.id
                FROM $services_table
                WHERE $services_table.deleted=0 AND $services_table.slug=" . $this->db->escape($slug) . " AND $services_table.id!=" . $this->db->escape((int) $id) . "
                LIMIT 1";

        return $this->db->query($sql)->getRow() ? true : false;
    }

}
