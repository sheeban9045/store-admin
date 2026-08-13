<?php

namespace App\Controllers;

class Api extends App_Controller {

    function __construct() {
        parent::__construct();
    }

    function check_license() {

        $domain_name = $this->request->getGet('domain_name');
        $license_key = $this->request->getGet('license_key');

        //shared key across all community installs (webhut + self-hosted)
        $valid_license_key = getenv('API_LICENSE_KEY');

        if (empty($domain_name) || empty($license_key)) {
            echo json_encode(array("status" => "invalid_request"));
            exit;
        }

        if ($license_key !== $valid_license_key) {
            echo json_encode(array("status" => "invalid_key"));
            exit;
        }

        $order = $this->Orders_model->is_domain_exists($domain_name);

        if (empty($order)) {
            echo json_encode(array("status" => "not_found"));
            exit;
        }

        echo json_encode(array("status" => $order->plan_status));
        exit;
    }

}
