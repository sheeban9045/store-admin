<?php

namespace App\Models;

class Service_orders_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'service_orders';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $orders_table = $this->db->prefixTable('service_orders');

        $where = "deleted=0";
        $id = get_array_value($options, "id");

        if ($id) {
            $where .= " AND id=" . $this->db->escape($id);
        }

        $user_id = get_array_value($options, "user_id");
        if ($user_id) {
            $where .= " AND user_id=" . $this->db->escape($user_id);
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND status=" . $this->db->escape($status);
        }

        $sql = "SELECT *
                FROM $orders_table
                WHERE $where
                ORDER BY id DESC";

        return $this->db->query($sql);
    }

    function get_orders_by_user($user_id) {
        $orders_table = $this->db->prefixTable('service_orders');
        $services_table = $this->db->prefixTable('services');

        return $this->db->table($orders_table . ' as o')
            ->select('o.*, s.title as service_name, s.image as service_image')
            ->join($services_table . ' as s', 's.id = o.service_id', 'left')
            ->where('o.user_id', $user_id)
            ->where('o.deleted', 0)
            ->orderBy('o.id', 'DESC')
            ->get();
    }
}
