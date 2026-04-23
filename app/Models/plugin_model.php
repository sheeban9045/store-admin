<?php

namespace App\Models;

class plugin_model extends Crud_model {

    protected $table = null;
  

    //Get pluginlist

 function updatePluginStatus($value = array()){
    $pluginlist_table = $this->db->prefixTable('pluginlist');
    if (!empty($value) ){
        $query = "UPDATE $pluginlist_table SET status= ".$value['status']. " WHERE plugin_id=".$value['id']." And plan_id =".$value['plan_id'];
        $this->db->query($query);
    }
    $sql = "SELECT * FROM $pluginlist_table";
    $get_pluginlist = $this->db->query($sql)->getResult();
    return $get_pluginlist;
 }

 function get_pluginlist() {
        $pluginlist_table = $this->db->prefixTable('pluginlist');
        $sql = "SELECT * FROM $pluginlist_table;
        $get_pluginlist = $this->db->query($sql);
        return $this->db->getResult();
   
    }

}

function updatePluginStatus() {
    return plugin_model::instance();
}  
pluginsList();


