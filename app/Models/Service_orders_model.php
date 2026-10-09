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

    function get_service_payments_for_list($options = array()) {
        $orders_table = $this->db->prefixTable('service_orders');
        $users_table = $this->db->prefixTable('users');
        $clients_table = $this->db->prefixTable('clients');
        $services_table = $this->db->prefixTable('services');

        $where = "$orders_table.deleted=0 AND $orders_table.status='success'";

        $client_id = get_array_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $users_table.client_id=" . $this->db->escape($client_id);
        }

        $start_date = get_array_value($options, "start_date");
        $end_date = get_array_value($options, "end_date");
        if ($start_date && $end_date) {
            $where .= " AND ($orders_table.created_at BETWEEN '$start_date' AND '$end_date') ";
        }

        $currency = get_array_value($options, "currency");
        if ($currency) {
            $where .= " AND $clients_table.currency=" . $this->db->escape($currency);
        }

        $sql = "SELECT 
                    $orders_table.id,
                    $orders_table.total_amount AS amount,
                    $orders_table.created_at AS payment_date,
                    $services_table.title AS service_name,
                    $users_table.client_id,
                    $clients_table.currency_symbol,
                    'Stripe' AS payment_method_title,
                    1 AS is_service
                FROM $orders_table
                LEFT JOIN $users_table ON $users_table.id = $orders_table.user_id
                LEFT JOIN $clients_table ON $clients_table.id = $users_table.client_id
                LEFT JOIN $services_table ON $services_table.id = $orders_table.service_id
                WHERE $where
                ORDER BY $orders_table.created_at DESC";

        return $this->db->query($sql);
    }
}
