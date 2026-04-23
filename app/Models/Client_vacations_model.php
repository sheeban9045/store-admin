<?php

namespace App\Models;

class Client_vacations_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'client_vacations';
        parent::__construct($this->table);
    }

    function get_setting($params = []) {
        $result = $this->db_builder->getWhere(array('start_date' => $params['start_date'], 'end_date' => $params['end_date'], 'client_id' => $params['client_id']), 1);
        if (count($result->getResult()) == 1) {
            return $result->getRow()->vacation_id;
        }
    }

    public function get_client_holidays($client_id = 0) {
        $tickets_table = $this->db->prefixTable('client_vacations');


        $sql = "SELECT * FROM $tickets_table WHERE client_id = $client_id";
        return $this->db->query($sql)->getResult();
    }

    function save_setting($params = []) {
        // $fields = array(
        //     'setting_name' => $setting_name,
        //     'setting_value' => $setting_value
        // );

        $vacation_id = $this->get_setting($params);
        if ($vacation_id === NULL) {
            // $fields["type"] = $type; //type can't be updated

            return $this->db_builder->insert($params);
        } else {
            $this->db_builder->where('vacation_id', $vacation_id);
            $this->db_builder->update($params);
        }
    }

    function delete_vacation($params = []) {
        if (count($params)) {
            return $this->db_builder->delete($params);
        }
    }

    // //find all app settings and login user's setting
    // //user's settings are saved like this: user_[userId]_settings_name;
    // function get_all_required_settings($user_id = 0) {
    //     $settings_table = $this->db->prefixTable('settings');
    //     $sql = "SELECT $settings_table.setting_name,  $settings_table.setting_value
    //     FROM $settings_table
    //     WHERE $settings_table.deleted=0 AND ($settings_table.type = 'app' OR ($settings_table.type ='user' AND $settings_table.setting_name LIKE 'user_" . $user_id . "_%'))";
    //     return $this->db->query($sql);
    // }

}
