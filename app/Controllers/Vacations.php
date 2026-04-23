<?php

namespace App\Controllers;

use App\Controllers\Security_Controller;

class Vacations extends Security_Controller
{
    function __construct() {
        parent::__construct();
    }


    public function index()
    {
        $view_data['holidays'] = get_setting("holidays");
        
        return $this->template->rander("clients/vacations/index", $view_data);
        
    }

    public function add() {
        $params['title'] = $this->request->getVar('title');
        $params['start_date'] = $this->request->getVar('start');
        $params['end_date'] = $this->request->getVar('end');
        $params['client_id'] = $this->request->getVar('client_id');


        $this->Client_vacations_model->save_setting($params);

        return 'success';
    }

    public function update() {

    }

    public function delete() {
        $params['start_date'] = $this->request->getVar('start');
        $params['end_date'] = $this->request->getVar('end');
        $params['client_id'] = $this->request->getVar('client_id');

        $this->Client_vacations_model->delete_vacation($params);

        return 'success';
    }

    public function loadData()
    {
        $event = new holiday_vacation_model();
        // on page load this ajax code block will be run
        $data = $event->where([
            'start >=' => $this->request->getVar('start'),
            'end <='=> $this->request->getVar('end')
        ])->findAll();

        return json_encode($data);
    }

    public function ajax()
    {
        $event = new holiday_vacation_model();

        switch ($this->request->getVar('type')) {

                
            case 'add':
                $data = [
                    'title' => $this->request->getVar('title'),
                    'start' => $this->request->getVar('start'),
                    'end' => $this->request->getVar('end'),
                ];
                $event->insert($data);
                return json_encode($event);
                break;

                       
            case 'update':
                $data = [
                    'title' => $this->request->getVar('title'),
                    'start' => $this->request->getVar('start'),
                    'end' => $this->request->getVar('end'),
                ];

                $event_id = $this->request->getVar('id');
                
                $event->update($event_id, $data);

                return json_encode($event);
                break;

                    
            case 'delete':

                $event_id = $this->request->getVar('id');

                $event->delete($event_id);

                return json_encode($event);
                break;

            default:
                break;
        }
    }
}