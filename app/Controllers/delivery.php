<?php

namespace App\Controllers;

use App\Controllers\Security_Controller;

class delivery extends Security_Controller
{
    function __construct() {
        parent::__construct();
    }


    public function index()
    {
        $view_data['holidays'] = get_setting("holidays");
        
        return $this->template->rander("delivery/delivey", $view_data);
        
    }

    public function add() {
        $title = $this->request->getVar('title');
        $start = $this->request->getVar('start');
        $end = $this->request->getVar('end');

        $holidayArray = [];

        $holidays = get_setting("holidays");
        if( !empty($holidays) ) {
            $holidayArray = json_decode($holidays);
        }            


        // Add new holiday
        $tempHolidayArray['title'] = $title;
        $tempHolidayArray['start'] = $start;
        $tempHolidayArray['end'] = $end;

        $holidayArray[] = $tempHolidayArray;

        $holidayArray = json_encode($holidayArray);

        $this->Settings_model->save_setting('holidays', $holidayArray);

        return 'success';
    }

    public function update() {

    }

    public function delete() {
        $start = $this->request->getVar('start');
        $end = $this->request->getVar('end');

        $holidayArray = [];
        $holidays = get_setting("holidays");
        if( !empty($holidays) ) {
            $holidayArray = json_decode($holidays, true);
        }

        if( !empty($holidayArray) ) {
            $finalArray = [];
            foreach($holidayArray as $holiday) {
                if( ($holiday['start'] != $start) && ($holiday['end'] != $end) )
                    $finalArray[] = $holiday;
            }

            $finalArray = json_encode($finalArray);
            $this->Settings_model->save_setting('holidays', $finalArray);
        }
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