<?php

namespace App\Controllers;

class License extends App_Controller {


    function __construct() {
        parent::__construct();
    }

    public function verify_plugin()
    {
        $name = trim($this->request->getGet('name') ?? '');
        $hostname = trim($this->request->getGet('hostname') ?? '');
        $signature = trim($this->request->getGet('signature') ?? '');

        if (!$name || !$hostname || !$signature) {
            return $this->response->setJSON([
                "success" => false,
                "message" => "Required parameters are missing."
            ]);
        }

        $secretKey = "WEBHUT_PLUGIN_SECRET_2026";

        $expectedSignature = hash_hmac(
            'sha256',
            $name . '|' . strtolower($hostname),
            $secretKey
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return $this->response->setJSON([
                "success" => false,
                "message" => "Invalid request."
            ]);
        }

        // Find plugin
        $plugin = $this->Webhut_plugins_model->get_plugin_by_name($name);

        if (!$plugin) {
            return $this->response->setJSON([
                "success" => false,
                "message" => "Plugin not found."
            ]);
        }

        $verified = $this->Webhut_plugins_model->verify_plugin_purchase(
            $plugin->id,
            strtolower($hostname)
        );

        if (!$verified) {
            return $this->response->setJSON([
                "success" => false,
                "message" => "This plugin has not been purchased for this community."
            ]);
        }

        return $this->response->setJSON([
            "success" => true,
            "message" => "Plugin verified successfully."
        ]);
    }

}
