<?php

namespace App\Controllers;



class create_domain extends Security_Controller
{
    function __construct() {
        parent::__construct();
    }
      function index() {
        return $this->template->view("create_domain/index");
    }

function InsertInTable()
{
 return $this->template->model("model/Create_domain_model");

 }
 }


