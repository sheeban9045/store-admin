<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class Token extends App_Controller
{
    use ResponseTrait;

    public function get_csrf_token()
    {
        $csrfTokenName = csrf_token();
        $csrfHash = csrf_hash();

        $response = [
            'csrf_token_name' => $csrfTokenName,
            'csrf_hash' => $csrfHash
        ];

        return $this->respond($response);
    }
}
