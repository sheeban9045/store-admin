<?php

namespace App\Controllers;

class Community extends App_Controller {

    function index() {
    }
    
     function get_plugins_by_order() {
       $order_id = $_REQUEST['order_id'];
       $plan_detail = $this->Orders_model->get_plan_detail($order_id)->getRow();
       $pluginlist = $this->Order_status_model->getActivePlugins($plan_detail->item_id);
       $final_plugin_names = array();
       if(!empty($pluginlist)) {
           foreach($pluginlist as $plugin) {
               $final_plugin_names [] = $plugin->plugin_name; 
           }
       }
       echo json_encode($final_plugin_names);die;
    }
    
    // function get_order_details_info() {
    //   $order_id = $_REQUEST['order_id'];
    //   $order_item = $this->Orders_model->get_one($order_id);
    //   unset($order_item->stripe_response);
    //   $plan_detail = $this->Orders_model->get_plan_detail($order_id)->getRow();
    //   $order_item->plan_detail = $plan_detail;
    //   $pluginlist = $this->Order_status_model->getActivePlugins($plan_detail->item_id);
    //   $final_plugin_names = array();
    //   if(!empty($pluginlist)) {
    //       foreach($pluginlist as $plugin) {
    //           $final_plugin_names [] = $plugin->plugin_name; 
    //       }
    //   }
    //   $order_item->plugin_list = $final_plugin_names;
    //   echo json_encode($order_item);die;
    // }
    
    function get_order_details_info() {
       $rawData = file_get_contents('php://input');
       $data = json_decode($rawData);
       if(empty($data)) {
            echo false;die;
       }
       $order_ids = array();
       if(is_array($data->order_id)) {
           $order_ids = $data->order_id;
       } else {
           $order_ids[] = $data->order_id;
       }
       $final_response = array();
       if(!empty($order_ids)) {
           foreach($order_ids as $order_id) {
               $order_item = $this->Orders_model->get_one($order_id);
               unset($order_item->stripe_response);
               $plan_detail = $this->Orders_model->get_plan_detail($order_id)->getRow();
               $order_item->plan_detail = $plan_detail;
               $pluginlist = $this->Order_status_model->getActivePlugins($plan_detail->item_id);
               $final_plugin_names = array();
               if(!empty($pluginlist)) {
                   foreach($pluginlist as $plugin) {
                       $final_plugin_names [] = $plugin->plugin_name; 
                   }
               }
               $order_item->plugin_list = $final_plugin_names; 
               $final_response[] = $order_item;
           }
           echo json_encode($final_response);die;
       } else {
           echo false;die;
       }
    }

    function sso() {
        $rawData = file_get_contents('php://input');
        $user_data = json_decode($rawData);
        if(isset($user_data->email) && !empty($user_data->email) && isset($user_data->first_name) && !empty($user_data->first_name)) {
            if (!$this->Users_model->is_email_exists($user_data->email)){

                $company_name =  $user_data->first_name . " " . $user_data->last_name; 

                //save user name as company name if there is no company name entered
                $client_data = array(
                    "company_name" => $company_name,
                    "created_by" => 1 //add default admin
                ); 
                $client_data = clean_data($client_data);
                if (get_setting("disallow_duplicate_client_company_name") == "1" && $this->Clients_model->is_duplicate_company_name($company_name)){
                    echo json_encode(array("success" => false, 'message' => app_lang("account_already_exists_for_your_company_name") . " " . anchor(get_uri("signin"), app_lang('signin'), array("class" => "text-white text-off"))));die;
                }

                //create a client
                $client_id = $this->Clients_model->ci_save($client_data);
                if ($client_id) {
                    $user_data->client_id = $client_id;
                    if(is_object($user_data)) {
                        $user_data = (array)$user_data;
                        $user_data["created_at"] = get_current_utc_time();
                        $user_data["password"] = password_hash($user_data["password"], PASSWORD_DEFAULT);
                    }
                    $user_id = $this->Users_model->ci_save($user_data);
                    echo true;die;
                } else {
                    echo false;die;
                }
            } else {
                echo false;die;
            }
        } else {
            echo false;die;
        }
    }    
    
}