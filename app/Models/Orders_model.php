<?php

namespace App\Models;

class Orders_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'orders';
        parent::__construct($this->table);
    }

    //Selecting Domain By Harsh 
    function get_domain($order_id){
        $orders_table = $this->db->prefixTable('orders');
        $sql = "SELECT domain_name FROM $orders_table WHERE id='".$order_id."'";

        return ( $this->db->query($sql)->getRow()->domain_name);
    }

    //
    function set_community($order_id){
        $orders_table = $this->db->prefixTable('orders');
        $sql = "UPDATE  $orders_table SET is_community_set = 1 WHERE id='".$order_id."'";
        $this->db->query($sql);
    }

    function update_user_email($order_id, $db_name){
        // Get client_id
        $orders_table = $this->db->prefixTable('orders');
        $client_id = $this->db->query(
            "SELECT client_id FROM $orders_table WHERE id = ?", 
            [$order_id]
        )->getRow()->client_id;

        // Get email
        $users_table = $this->db->prefixTable('users');
        $email = $this->db->query(
            "SELECT email FROM $users_table WHERE id = ?", 
            [$client_id]
        )->getRow()->email;

        //  Connect to NEW database
        $new_db = new \mysqli("localhost", "webhut96_deepak_community", '@#$Deepak25', $db_name);

        if ($new_db->connect_error) {
            die("Connection failed: " . $new_db->connect_error);
        }

        // Update email (only 1 record exists)
        $stmt = $new_db->prepare("UPDATE engine4_users SET email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $stmt->close();
        $new_db->close();
    }

    //domain validation
    function is_domain_exists($domain)
    {
        $orders_table = $this->db->prefixTable('orders');
        $sql = "SELECT * FROM $orders_table WHERE domain_name='".$domain."'";

        return $this->db->query($sql)->getRow();
    }

    function get_details($options = array()) {
        $orders_table = $this->db->prefixTable('orders');
        $clients_table = $this->db->prefixTable('clients');
        $taxes_table = $this->db->prefixTable('taxes');
        $order_items_table = $this->db->prefixTable('order_items');
        $order_status_table = $this->db->prefixTable('order_status');
        $users_table = $this->db->prefixTable('users');
        $projects_table = $this->db->prefixTable('projects');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where .= " AND $orders_table.id=$id";
        }
        $client_id = get_array_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $orders_table.client_id=$client_id";
        }

        $order_date = get_array_value($options, "order_date");
        $deadline = get_array_value($options, "deadline");
        if ($order_date && $deadline) {
            //$where .= " AND ($orders_table.order_date BETWEEN '$order_date' AND '$deadline') ";
        }

        $after_tax_1 = "(IFNULL(tax_table.percentage,0)/100*IFNULL(items_table.order_value,0))";
        $after_tax_2 = "(IFNULL(tax_table2.percentage,0)/100*IFNULL(items_table.order_value,0))";

        $discountable_order_value = "IF($orders_table.discount_type='after_tax', (IFNULL(items_table.order_value,0) + $after_tax_1 + $after_tax_2), IFNULL(items_table.order_value,0) )";

        $discount_amount = "IF($orders_table.discount_amount_type='percentage', IFNULL($orders_table.discount_amount,0)/100* $discountable_order_value, $orders_table.discount_amount)";

        $before_tax_1 = "(IFNULL(tax_table.percentage,0)/100* (IFNULL(items_table.order_value,0)- $discount_amount))";
        $before_tax_2 = "(IFNULL(tax_table2.percentage,0)/100* (IFNULL(items_table.order_value,0)- $discount_amount))";

        $order_value_calculation = "(
            IFNULL(items_table.order_value,0)+
            IF($orders_table.discount_type='before_tax',  ($before_tax_1+ $before_tax_2), ($after_tax_1 + $after_tax_2))
            - $discount_amount
           )";

        $status_id = get_array_value($options, "status_id");
        if ($status_id) {
            $where .= " AND $orders_table.status_id='$status_id'";
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("orders", $custom_fields, $orders_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $sql = "SELECT $orders_table.*, $clients_table.currency, $clients_table.currency_symbol, $clients_table.company_name, $projects_table.title as project_title,
           $order_value_calculation AS order_value, tax_table.percentage AS tax_percentage, tax_table2.percentage AS tax_percentage2, $order_status_table.title AS order_status_title, $order_status_table.color AS order_status_color, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user, $users_table.user_type AS created_by_user_type $select_custom_fieds
        FROM $orders_table
        LEFT JOIN $clients_table ON $clients_table.id= $orders_table.client_id
        LEFT JOIN $order_status_table ON $orders_table.status_id = $order_status_table.id 
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $orders_table.tax_id
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $orders_table.tax_id2 
        LEFT JOIN (SELECT order_id, SUM(total) AS order_value FROM $order_items_table WHERE deleted=0 GROUP BY order_id) AS items_table ON items_table.order_id = $orders_table.id 
        LEFT JOIN $users_table ON $users_table.id=$orders_table.created_by
        LEFT JOIN $projects_table ON $projects_table.id= $orders_table.project_id
        $join_custom_fieds
        WHERE $orders_table.deleted=0 $where $custom_fields_where";


        
        return $this->db->query($sql);
    }

    function get_processing_order_total_summary($user_id) {
        $order_items_table = $this->db->prefixTable('order_items');
        $orders_table = $this->db->prefixTable('orders');
        $clients_table = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $taxes_table = $this->db->prefixTable('taxes');

        $where = " AND $order_items_table.created_by=$user_id";

        $order_tax_id = get_setting('order_tax_id') ? get_setting('order_tax_id') : 0;
        $order_tax_id2 = get_setting('order_tax_id2') ? get_setting('order_tax_id2') : 0;

        $item_sql = "SELECT SUM($order_items_table.total) AS order_subtotal, SUM($order_items_table.quantity) AS total_quantity, tax_table.percentage AS tax_percentage, tax_table.title AS tax_name,
            tax_table2.percentage AS tax_percentage2, tax_table2.title AS tax_name2
        FROM $order_items_table
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $order_tax_id
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $order_tax_id2
        WHERE $order_items_table.deleted=0 AND $order_items_table.order_id=0 $where";
        $item = $this->db->query($item_sql)->getRow();

        $select_order_client_id = "";
        if ($user_id) {
            $select_order_client_id = "(SELECT $users_table.client_id FROM $users_table WHERE $users_table.id=$user_id)";
        } else {
            $select_order_client_id = "(SELECT $orders_table.client_id FROM $orders_table WHERE $orders_table.id=0)";
        }

        $client_sql = "SELECT $clients_table.currency_symbol, $clients_table.currency FROM $clients_table WHERE $clients_table.id=$select_order_client_id";

        $client = $this->db->query($client_sql)->getRow();

        $result = new \stdClass();

        $result->order_subtotal = $item->order_subtotal;
        $result->total_quantity = $item->total_quantity;
        $result->tax_percentage = $item->tax_percentage;
        $result->tax_percentage2 = $item->tax_percentage2;
        $result->tax_name = $item->tax_name;
        $result->tax_name2 = $item->tax_name2;
        $result->tax = 0;
        $result->tax2 = 0;

        $order_subtotal = $result->order_subtotal;
        if ($item->tax_percentage) {
            $result->tax = $order_subtotal * ($item->tax_percentage / 100);
        }
        if ($item->tax_percentage2) {
            $result->tax2 = $order_subtotal * ($item->tax_percentage2 / 100);
        }

        $result->order_total = $item->order_subtotal + $result->tax + $result->tax2;

        $result->currency_symbol = isset($client->currency_symbol) ? $client->currency_symbol : get_setting("currency_symbol");
        $result->currency = isset($client->currency) ? $client->currency : get_setting("default_currency");
        return $result;
    }

    function get_order_total_summary($order_id = 0) {
        $order_items_table = $this->db->prefixTable('order_items');
        $orders_table = $this->db->prefixTable('orders');
        $clients_table = $this->db->prefixTable('clients');
        $taxes_table = $this->db->prefixTable('taxes');

        $item_sql = "SELECT SUM($order_items_table.total) AS order_subtotal, SUM($order_items_table.quantity) AS total_quantity 
        FROM $order_items_table
        LEFT JOIN $orders_table ON $orders_table.id= $order_items_table.order_id    
        WHERE $order_items_table.deleted=0 AND $order_items_table.order_id=$order_id AND $orders_table.deleted=0";
        $item = $this->db->query($item_sql)->getRow();


        $order_sql = "SELECT $orders_table.*, tax_table.percentage AS tax_percentage, tax_table.title AS tax_name,
            tax_table2.percentage AS tax_percentage2, tax_table2.title AS tax_name2
        FROM $orders_table
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $orders_table.tax_id
        LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $orders_table.tax_id2
        WHERE $orders_table.deleted=0 AND $orders_table.id=$order_id";
        $order = $this->db->query($order_sql)->getRow();

        $client_sql = "SELECT $clients_table.currency_symbol, $clients_table.currency FROM $clients_table WHERE $clients_table.id=$order->client_id";
        $client = $this->db->query($client_sql)->getRow();


        $result = new \stdClass();
        $result->order_subtotal = $item->order_subtotal;
        $result->total_quantity = $item->total_quantity;
        $result->tax_percentage = $order->tax_percentage;
        $result->tax_percentage2 = $order->tax_percentage2;
        $result->tax_name = $order->tax_name;
        $result->tax_name2 = $order->tax_name2;
        $result->tax = 0;
        $result->tax2 = 0;

        $order_subtotal = $result->order_subtotal;
        $order_subtotal_for_taxes = $order_subtotal;
        if ($order->discount_type == "before_tax") {
            $order_subtotal_for_taxes = $order_subtotal - ($order->discount_amount_type == "percentage" ? ($order_subtotal * ($order->discount_amount / 100)) : $order->discount_amount);
        }

        if ($order->tax_percentage) {
            $result->tax = $order_subtotal_for_taxes * ($order->tax_percentage / 100);
        }
        if ($order->tax_percentage2) {
            $result->tax2 = $order_subtotal_for_taxes * ($order->tax_percentage2 / 100);
        }
        $order_total = $item->order_subtotal + $result->tax + $result->tax2;

        //get discount total
        $result->discount_total = 0;
        if ($order->discount_type == "after_tax") {
            $order_subtotal = $order_total;
        }

        $result->discount_total = $order->discount_amount_type == "percentage" ? ($order_subtotal * ($order->discount_amount / 100)) : $order->discount_amount;

        $result->discount_type = $order->discount_type;

        $result->discount_total = is_null($result->discount_total) ? 0 : $result->discount_total;
        $result->order_total = $order_total - number_format($result->discount_total, 2, ".", "");

        $result->currency_symbol = isset($client->currency_symbol) ? $client->currency_symbol : get_setting("currency_symbol");
        //$result->currency_symbol = "$";
        
        $result->currency = isset($client->currency) ? $client->currency : get_setting("default_currency");
        return $result;
    }

    //get order last id
    function get_order_last_id() {
        $orders_table = $this->db->prefixTable('orders');

        $sql = "SELECT MAX($orders_table.id) AS last_id FROM $orders_table";

        return $this->db->query($sql)->getRow()->last_id;
    }

    //save initial number of order
    function save_initial_number_of_order($value) {
        $orders_table = $this->db->prefixTable('orders');

        $sql = "ALTER TABLE $orders_table AUTO_INCREMENT=$value;";

        return $this->db->query($sql);
    }

    //GEtting Oreder data of client 
    function my_recent_order($client_id){
        $orders_table = $this->db->prefixTable('orders');

        $sql = "SELECT * FROM $orders_table WHERE id = (SELECT MAX(id) FROM $orders_table WHERE client_id = $client_id);";

        return $this->db->query($sql);
    }
    
    //Harsh's Code for saving stripe response in database;
    function save_response($response,$order_id){
        $orders_table = $this->db->prefixTable('orders');
        $sql = "UPDATE $orders_table SET stripe_response = '".$response."' WHERE id = ".$order_id;
        $this->db->query($sql);
    }
    
    //harsh's Code for  Update status after payment 
    function update_status($order_id, $status){
        $orders_table = $this->db->prefixTable('orders');
        $sql = "UPDATE $orders_table SET status_id = ".(int)$status." WHERE id = ".$order_id;
        $this->db->query($sql);
    }

    function recent_order($client_id){
        $orders_table = $this->db->prefixTable('orders');
        $sql = "SELECT * FROM  $orders_table  WHERE id = (SELECT MAX(id) FROM $orders_table WHERE client_id = ".$client_id.")";
        
        return $this->db->query($sql);
    }

    function my_orders($client_id){
        $orders_table = $this->db->prefixTable('orders');
        $sql = "SELECT * FROM  $orders_table  WHERE client_id = ".$client_id." ORDER BY id DESC";
        
        return $this->db->query($sql);
    }

    function get_community_by_client($client_id, $where = "1"){
        $orders_table = $this->db->prefixTable('orders');

        $sql = "SELECT * FROM  $orders_table  WHERE client_id = ".$client_id." AND ".$where." ORDER BY id DESC";
        
        return $this->db->query($sql);
    }

    function get_plan_detail( $order_id){
        $order_items_table = $this->db->prefixTable('order_items');
        $sql = "SELECT * FROM  $order_items_table  WHERE id = (SELECT MIN(id) FROM $order_items_table WHERE order_id=".$order_id.")";
        //echo $sql;die;
        return $this->db->query($sql);
    }

    function check_self_community($community)
    {
        $orders_table = $this->db->prefixTable('orders');

        $sql = "SELECT self FROM $orders_table WHERE domain_name = '".$community."' LIMIT 1";

        $result = $this->db->query($sql)->getRow();

        if ($result && $result->self == 1) {
            return true;
        }

        return false;
    }


}


//domain validation

   // function validation(){
   //     $orders = document.getElementById("checklists_dropdown");
   //          if(domain.value.length <= 20 && domain.value.length >= 3){
   //          }
   //          else
   //          {
   //              alert("domain has to be between 3-20 characters.")
   //           }
             
   //          //duplication data list

   //           $orders = document.getElementById("checklists_dropdown");
   //          if(domain.value == domain.value){
   //          }
   //          else{
   //              alert("domain already exists.")
   //           }
   // }

