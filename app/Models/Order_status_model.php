<?php

namespace App\Models;
use App\Models\Community_plugin_model;

class Order_status_model extends Crud_model {
    
    protected $table = null;
    
    protected $db;
    protected $db_builder = null;
    protected $allowedFields = array();

    function __construct() {
        $this->table = 'order_status';
        parent::__construct($this->table);
        $this->db = db_connect('default');
        $this->plugin_builder = $this->db->table("crm_plugins");
        $this->map_builder = $this->db->table("crm_planmapping");
        $this->Community_plugin_model = new Community_plugin_model();

    }

    function updateStatus($params) {

        if( !isset($params['id']) )
            return;
        $status = (isset($params['status']) && !empty($params['status']))? 0: 1;
        $pluginlist_table = $this->db->prefixTable('pluginlist');
        $query = "UPDATE $pluginlist_table SET status= " . $status . " WHERE plugin_id = ".$params['id'];
        $this->db->query($query);
        return;
    }


       function get_pluginlist($value = array()) {

        $pluginlist_table = $this->db->prefixTable('pluginlist');

        //added one more condition to change plugin name.
        if (!empty($value) ){
            if(isset($_GET['name']) && isset($_GET['id'])){

                $query = "UPDATE $pluginlist_table SET  plugin_name = '".$value['name']."'"." WHERE plugin_id = ".$value['id'] ;
            }
            else
            {
                if(isset($_GET['plan_id'])){
                    $query = "UPDATE $pluginlist_table SET status= ".$value['status']. " WHERE plugin_id=".$value['id'] ;
                }else{
                    $query = "UPDATE $pluginlist_table SET status= ".$value['status']. " WHERE plugin_id=".$value['id'];

                }
            }
            $this->db->query($query);
        }
        $sql = "SELECT * FROM $pluginlist_table";
        $get_pluginlist = $this->db->query($sql)->getResult();
        return $get_pluginlist;
   
    }

  

    function get_details($options = array()) {
        $order_status_table = $this->db->prefixTable('order_status');
        $orders_table = $this->db->prefixTable('orders');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $where = " AND $order_status_table.id=$id";
        }

        $sql = "SELECT $order_status_table.*, (SELECT COUNT($orders_table.id) FROM $orders_table WHERE $orders_table.deleted=0 AND $orders_table.status_id=$order_status_table.id) AS total_orders
        FROM $order_status_table
        WHERE $order_status_table.deleted=0 $where
        ORDER BY $order_status_table.sort ASC";

        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $order_status_table = $this->db->prefixTable('order_status');

        $sql = "SELECT MAX($order_status_table.sort) as sort
        FROM $order_status_table
        WHERE $order_status_table.deleted=0";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            return $result->getRow()->sort;
        } else {
            return 0;
        }
    }

    function get_first_status() {
        $order_status_table = $this->db->prefixTable('order_status');

        $sql = "SELECT $order_status_table.id AS first_order_status
        FROM $order_status_table
        WHERE $order_status_table.deleted=0
        ORDER BY $order_status_table.sort ASC
        LIMIT 1";

        return $this->db->query($sql)->getRow()->first_order_status;
    }

    


    //Temporary

    function myPlans($plugin_name){
        //Using planmapping table because we have to choose plan that are added for this plugin
        $plan_table = $this->db->prefixTable('planmapping');

        $sql = "SELECT * FROM $plan_table WHERE plugin_name= '$plugin_name'";
        $get_planlist = $this->db->query($sql)->getResult();
        return $get_planlist;
    }
    function deletePlans($plugin_name){
        //Using planmapping table because we have to choose plan that are added for this plugin
        $plan_table = $this->db->prefixTable('planmapping');
        
        $sql = "DELETE FROM $plan_table WHERE plugin_name= '$plugin_name'";
        $this->db->query($sql);
        
    }

    function getPlans(){
        $plan_table = $this->db->prefixTable('plan');

        $sql = "SELECT * FROM $plan_table";
        $get_planlist = $this->db->query($sql)->getResult();
        return $get_planlist;
    }

    // function get_Plan_Plugin($id){
    //     $planmapping_table = $this->db->prefixTable('planmapping');

    //     $sql = "SELECT name FROM $planmapping_table LEFT JOIN crm_plan ON crm_plan.plan_Id=$planmapping_table.plan_id where plugin_id = ".$id;

    //     $get_planlist = $this->db->query($sql)->getResult();
    //     return $get_planlist;
    // }
    
    function get_Plan_Plugin($name){
        $planmapping_table = $this->db->prefixTable('planmapping');

        $sql = "SELECT name FROM $planmapping_table LEFT JOIN crm_plan ON crm_plan.plan_Id=$planmapping_table.plan_id where plugin_name = '$name'";
        $get_planlist = $this->db->query($sql)->getResult();
        return $get_planlist;
    }
    
    function getActivePlugins($id){
        $map_table = $this->db->prefixTable('planmapping');
        // $plugin_table = $this->db->prefixTable('plugins');

        // $sql = "SELECT * FROM  $map_table LEFT JOIN $plugin_table ON  $map_table.plugin_id=$plugin_table.plugin_id
        // WHERE $plugin_table.status=1 AND  $map_table.plan_id=$id";

        $sql = "SELECT * FROM  $map_table WHERE $map_table.plan_id=$id";

        
        $get_pluginlist = $this->db->query($sql)->getResult();
        foreach($get_pluginlist as $plugin) {
            $plugin_obj = $this->Community_plugin_model->getPlugin($plugin->plugin_name);
            $plugin->plugin_title = (!empty($plugin_obj))?$plugin_obj[0]->title :'';
        }
        return $get_pluginlist;
    }

    //function to get description and change log
    function insertPlugins($value = array())
    {
        $array=array();

            $array[] = "WPFORO";              
            $array[] = "BP POKE";             
            $array[] = "WCFM";                
            $array[] = "Wp Quiz";             
            $array[] = "Crisp Online Ch";             
            $array[] = "WP Captcha";              
            $array[] = "WP Attachments";              
            $array[] = "OptinMonster";                
            $array[] = "WP Debugging";               
            $array[] = "Query Monitor";               
            $array[] = "Buddy press mem";             
            $array[] = "classic editor";              
            $array[] = "gift buddypress";             
            $array[] = "Tooltips";                
            $array[] = "woo commerce st";             
            $array[] = "Wp Stream";               
            $array[] = "Audio Album";             
            $array[] = "BP Profile Sea";              
            $array[] = "Contact Form 7";              
            $array[] ="Mojo Marketplac";

        foreach($array as $p){
                 $data = array(
                    'title' =>  $p,
                    'status' => '1',
                    'image' =>  $p,
                    'body' =>  $p
                );
                 
                 $this->plugin_builder->insert($data);
        }
        return $array;
    }

    function mapPlansPlugins($plan,$plugin){
        $data=array(
            'plan_id'=> $plan,
            'plugin_name'=> $plugin
        );
        $this->map_builder->insert($data);
    }


    function getPlugins(){
        $plugins_table = $this->db->prefixTable('plugins');
        $sql = "SELECT * FROM $plugins_table";
        
        $get_pluginlist = $this->db->query($sql)->getResult();
        return $get_pluginlist;
    }

    function insertScreenshot($values = array()){
        $this->db->table("crm_screenshots")->insert($values);
    }

    function insertLog($text,$version,$plugin_name){
        $values = array(
            'body' => $text,
            'plugin_name' => $plugin_name,
            'version' => $version

        );
        $this->db->table("crm_changelog")->insert($values);
    }

    function getLogs($plugin_name){
        $logs_table = $this->db->prefixTable('changelog');
        $sql = "SELECT * FROM $logs_table WHERE plugin_name = '$plugin_name'";
        
        $get_logslist = $this->db->query($sql)->getResult();
        return $get_logslist;
    }

    function getPlugin($id){
        $plugin_table = $this->db->prefixTable('plugins');
        $sql = "SELECT * FROM $plugin_table WHERE plugin_Id = $id";
        
        $get_plugin = $this->db->query($sql)->getResult();
        return $get_plugin;
    }

    function editPlugin($values = array()){
        
        $plugin_table = $this->db->prefixTable('plugins');

        $title ="title = '".$values['title']."'";
        $body ="body = '".$values['body']."'";
        $status= "status= ".$values['status']; 
        $condition = "plugin_Id = ".$values['plugin_id'];
        if(!empty($values['image'])){

            $image = "image = '".$values['image']."'";
            $sql="UPDATE $plugin_table SET $title , $body , $status , $image WHERE $condition ";

        }else{

            $sql="UPDATE $plugin_table SET $title , $body , $status WHERE $condition ";   
        }
        $this->db->query($sql);   
    }

    //***************Code For Heading And Options*************************************

    function insertHeading($values = array()){
        $heading_table = $this->db->prefixTable('headings');
        $sql = "SELECT Count(heading_id) as count FROM $heading_table WHERE heading_id = ".$values['heading_id'];
        
        $get_heading = $this->db->query($sql)->getResult();
        
        if($get_heading[0]->count > 0){
            $title ="title = '".$values['title']."'";
            $condition = "heading_id = ".$values['heading_id'];
            
            $sql="UPDATE $heading_table SET $title WHERE $condition";
             
            $this->db->query($sql); 
            
        }
        else{
            $heading = $this->db->table("crm_headings")->insert($values);
        }
        
        //Replace Function is not working here dont know why
    }  

    function getHeadings(){
        $heading_table = $this->db->prefixTable('headings');
        $sql = "SELECT * FROM $heading_table";
        
        $get_heading = $this->db->query($sql)->getResult();
        return $get_heading;
    }

    function deleteHeading($heading_id){
        
         $heading_table = $this->db->prefixTable('headings');
        
        $sql = "DELETE FROM $heading_table WHERE heading_id= $heading_id";
        $this->db->query($sql);

        //Also Delete its options

        $option_table = $this->db->prefixTable('options');
        
        $sql = "DELETE FROM $option_table WHERE heading_id= $heading_id";
        $this->db->query($sql);
    } 

    function insertOptions($values = array(),$heading_id = 0){
        //Deleting All Previous Options
        $option_table = $this->db->prefixTable('options');
        
        $sql = "DELETE FROM $option_table WHERE heading_id= $heading_id";
        $this->db->query($sql);

        
        foreach($values as $option){
            $this->db->table("crm_options")->insert($option);
        }
    }

    function getOptions($id){
        $option_table = $this->db->prefixTable('options');
        $sql = "SELECT * FROM $option_table WHERE heading_id=".$id;
        
        $get_option = $this->db->query($sql)->getResult();
        return $get_option;
    }
}
