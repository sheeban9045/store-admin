<?php

namespace App\Controllers;

class data_item_id extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }


function data_item_id (){

return $this->template->view("order_status/index");
}

}
