<?php

namespace App\Controllers;

class manage_users extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("user");
    }

    function index() {
        $this->check_access_to_store();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("users", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("users", $this->login_user->is_admin, $this->login_user->user_type);

        if ($this->login_user->user_type === "staff") {
            $view_data['user_statuses'] = $this->user_status_model->get_details()->getResult();
            return $this->template->rander("users/index", $view_data);
        } else {
            //client view
            $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";

            return $this->template->rander("clients/users/client_portal", $view_data);
        }
    }

    function process_user() {
        $this->check_access_to_store();
        $view_data = get_user_making_data();
        $view_data["cart_items_count"] = count($this->user_items_model->get_all_where(array("created_by" => $this->login_user->id, "user_id" => 0, "deleted" => 0))->getResult());

        $view_data['clients_dropdown'] = "";
        if ($this->login_user->user_type == "staff") {
            $view_data['clients_dropdown'] = $this->_get_clients_dropdown();
        }

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("users", 0, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        $view_data['companies_dropdown'] = $this->_get_companies_dropdown();    

        return $this->template->rander("users/process_user", $view_data);
    }

    function item_list_data_of_login_user() {
        $this->check_access_to_store();
        $options = array("created_by" => $this->login_user->id, "processing" => true);
        $list_data = $this->user_items_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }

        echo json_encode(array("data" => $result));
    }

    /* prepare a row of user item list table */

    private function _make_item_row($data) {
        $item = "<div class='item-row strong mb5' data-id='$data->id'><div class='float-start move-icon'><i data-feather='menu' class='icon-16'></i></div> $data->title</div>";
        if ($data->description) {
            $item .= "<span>" . nl2br($data->description) . "</span>";
        }
        $type = $data->unit_type ? $data->unit_type : "";

        return array(
            $data->sort,
            $item,
            "<a class='cart-item-quantity-btn float-start mr10 mt5' href='javascript:void(0)' onclick='manageuserQty($data->id, 0)'>&nbsp-&nbsp   </a><div class='b-a w cart-item-quantity float-start clickable'>" . to_decimal_format($data->quantity) . "</div><a class='cart-item-quantity-btn float-start ml10 mt5' href='javascript:void(0)' onclick='manageuserQty($data->id, 1)'> &nbsp+&nbsp </a> " . $type,
            to_currency($data->rate),
            to_currency($data->total),
            // modal_anchor(get_uri("users/item_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id, "data-post-user_id" => $data->user_id))
                 js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("users/delete_item"), "data-action" => "delete"))
        );
    }

    /* load item modal */

    function item_modal_form() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $model_info = $this->user_items_model->get_one($id);
        if ($id) { //check permission only for existing item
            $this->check_access_to_this_user_item($model_info);
        }

        $view_data['model_info'] = $model_info;
        $view_data['user_id'] = $this->request->getPost('user_id');

        return $this->template->view('users/item_modal_form', $view_data);
    }

    /* add or edit an user item */

    function save_item() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $item_id = $this->request->getPost("item_id");

        if ($id) { //item added to user items
            $item_info = $this->user_items_model->get_one($id);
            $this->check_access_to_this_user_item($item_info);
        } else { //item not added to user items yet
            $item_info = $this->Items_model->get_one($item_id);
            $this->check_access_to_this_item($item_info);
        }

        $quantity = unformat_currency($this->request->getPost('user_item_quantity'));

        $user_item_data = array(
            "description" => $this->request->getPost('user_item_description'),
            "quantity" => $quantity,
            "created_by" => $this->login_user->id,
            "item_id" => isset($item_info->item_id) ? $item_info->item_id : $item_id
        );

        // if ($this->login_user->user_type === "staff") {
        //     //when it's adding by team members, they could change terms
        //     $rate = unformat_currency($this->request->getPost('user_item_rate'));
        //     $user_item_data["title"] = $this->request->getPost('user_item_title');
        //     $user_item_data["unit_type"] = $this->request->getPost('user_unit_type');
        //     $user_item_data["rate"] = unformat_currency($this->request->getPost('user_item_rate'));
        //     $user_item_data["total"] = $rate * $quantity;
        // } else {
        //     //adding by clients, they can't change terms
        //     $user_item_data["title"] = $item_info->title;
        //     $user_item_data["unit_type"] = $item_info->unit_type;
        //     $user_item_data["rate"] = $item_info->rate;
        //     $user_item_data["total"] = $item_info->rate * $quantity;
        // }

        // $user_id = $this->request->getPost("user_id");
        // if ($user_id) { //user created already, add user id
        //     $user_item_data["user_id"] = $user_id;
        // }

        // $user_item_id = $this->user_items_model->ci_save($user_item_data, $id);
        // if ($user_item_id) {

        //     //check if the add_new_item flag is on, if so, add the item to libary. 
        //     $add_new_item_to_library = $this->request->getPost('add_new_item_to_library');
        //     if ($add_new_item_to_library) {
        //         $library_item_data = array(
        //             "title" => $this->request->getPost('user_item_title'),
        //             "description" => $this->request->getPost('user_item_description'),
        //             "unit_type" => $this->request->getPost('user_unit_type'),
        //             "rate" => unformat_currency($this->request->getPost('user_item_rate'))
        //         );
        //         $this->Items_model->ci_save($library_item_data);
        //     }

            $options = array("id" => $user_item_id);
            $item_info = $this->user_items_model->get_details($options)->getRow();

            echo json_encode(array("success" => true, "user_id" => $item_info->user_id, "data" => $this->_make_item_row($item_info), "user_total_view" => $this->_get_user_total_view($item_info->user_id), 'id' => $user_item_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //update the sort value for user item
    function update_item_sort_values($id = 0) {
        $this->check_access_to_store();
        $sort_values = $this->request->getPost("sort_values");
        if ($sort_values) {

            //extract the values from the comma separated string
            $sort_array = explode(",", $sort_values);

            //update the value in db
            foreach ($sort_array as $value) {
                $sort_item = explode("-", $value); //extract id and sort value

                $id = get_array_value($sort_item, 0);
                $sort = get_array_value($sort_item, 1);

                $data = array("sort" => $sort);
                $this->user_items_model->ci_save($data, $id);
            }
        }
    }

    /* delete or undo an user item */

    function delete_item() {
        $this->check_access_to_store();
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $user_item_info = $this->user_items_model->get_one($id);
        $this->check_access_to_this_user_item($user_item_info);

        if ($this->request->getPost('undo')) {
            if ($this->user_items_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->user_items_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "user_id" => $item_info->user_id, "data" => $this->_make_item_row($item_info), "user_total_view" => $this->_get_user_total_view($item_info->user_id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->user_items_model->delete($id)) {
                $item_info = $this->user_items_model->get_one($id);
                echo json_encode(array("success" => true, "user_id" => $item_info->user_id, "user_total_view" => $this->_get_user_total_view($item_info->user_id), 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* user total section */

    private function _get_user_total_view($user_id = 0) {
        if ($user_id) {
            $view_data["user_total_summary"] = $this->users_model->get_user_total_summary($user_id);
            $view_data["user_id"] = $user_id;
            return $this->template->view('users/user_total_section', $view_data);
        } else {
            $view_data = get_user_making_data();
            return $this->template->view('users/processing_user_total_section', $view_data);
        }
    }

    function place_user() {

        $user_total_summary = get_user_making_data();
        $qty_min_user = get_setting('qty_min_user');
        $qty_max_user = get_setting('qty_max_user');
        $delivery_date = $this->request->getPost('delivery_date');
        $client_id = $this->request->getPost('client_id');

        if( ($user_total_summary['user_total_summary']->total_quantity < $qty_min_user) ) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            return;
        }

        if( ($user_total_summary['user_total_summary']->total_quantity > $qty_max_user) ) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            return;
        }

        if( ($user_total_summary['user_total_summary']->total_quantity % 5 != 0) ) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            return;
        }


        $vacation_title = '';
        $is_delivery_date_available = true;
        $temp_delivery_date = $delivery_date;
        $temp_delivery_date_array = explode("T", $temp_delivery_date);
        $temp_delivery_date = $temp_delivery_date_array[0];
        $temp_delivery_date_timestamp = strtotime($temp_delivery_date);

        $tomorrowDateTimestamp = strtotime(date("Y-m-d", strtotime('+1 day')));

        // Put condition for next day delivery time
        if( $temp_delivery_date_timestamp <= $tomorrowDateTimestamp ) {
            $selectedTime = str_replace(":", "", $temp_delivery_date_array[1]);
            $nextDayTimeLimit = str_replace(":", "", get_setting("time_limit_for_delivery_next_day"));

            if( $selectedTime > $nextDayTimeLimit ) {
                echo json_encode(array("success" => false, 'message' => sprintf(app_lang('deliver_time_not_available_error_msg'), get_setting("time_limit_for_delivery_next_day"))));
                return;
            }
        }
        


        // Put the condition for Vacations
        if( empty($client_id) && !empty($this->login_user) && !empty($this->login_user->is_admin) ) {
            // In case of Admin
            $holidays = get_setting("holidays");
            if( !empty($holidays) ) {
                $holidayArray = json_decode($holidays, true);
                if( !empty($holidayArray) ) {
                    foreach( $holidayArray as $holiday ) {
                        $vacation_start_timestamp = strtotime($holiday['start']);
                        $vacation_end_timestamp = strtotime($holiday['end']);

                        if( ($temp_delivery_date_timestamp >= $vacation_start_timestamp) && ($temp_delivery_date_timestamp < $vacation_end_timestamp) ) {
                            $vacation_title = $holiday['title'];
                            $is_delivery_date_available = false;
                            break;
                        }

                    }
                }
            }

        }else {
            // In case of User
            $user_id = !empty($client_id)? $client_id: $this->login_user->client_id;
            $getClientHoliday = $this->Client_vacations_model->get_client_holidays($user_id);

            if( !empty($getClientHoliday) ) {
                foreach($getClientHoliday as $holiday) {
                    $vacation_start_timestamp = strtotime($holiday->start_date);
                    $vacation_end_timestamp = strtotime($holiday->end_date);

                    if( ($temp_delivery_date_timestamp >= $vacation_start_timestamp) && ($temp_delivery_date_timestamp < $vacation_end_timestamp) ) {
                        $vacation_title = $holiday->title;
                        $is_delivery_date_available = false;
                        break;
                    }
                }
            }
        }

        if( empty($is_delivery_date_available) ) {
            echo json_encode(array("success" => false, 'message' => sprintf(app_lang('deliver_date_not_available_error_msg'), $vacation_title)));
            return;
        }


        $this->check_access_to_store();

        $user_items = $this->user_items_model->get_all_where(array("created_by" => $this->login_user->id, "user_id" => 0, "deleted" => 0))->getResult();
        if (!$user_items) {
            echo json_encode(array("success" => false, 'message' => app_lang('no_items_text')));
            exit;
        }

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "user");

        $user_data = array(
            "client_id" => $this->request->getPost("client_id") ? $this->request->getPost("client_id") : $this->login_user->client_id,
            "user_date" => get_my_local_time(), // get_today_date(),
            "note" => $this->request->getPost('user_note'),
            "delivery_date" => str_replace('T', ' ', $delivery_date),
            "created_by" => $this->login_user->id,
            "status_id" => $this->user_status_model->get_first_status(),
            "tax_id" => get_setting('user_tax_id') ? get_setting('user_tax_id') : 0,
            "tax_id2" => get_setting('user_tax_id2') ? get_setting('user_tax_id2') : 0,
            "company_id" => $this->request->getPost('company_id') ? $this->request->getPost('company_id') : get_default_company_id()
        );

        $user_data["files"] = $files_data;

        $user_id = $this->users_model->ci_save($user_data);

        if ($user_id) {
            save_custom_fields("users", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

            //save items to this user
            foreach ($user_items as $user_item) {
                $user_item_data = array("user_id" => $user_id);
                $this->user_items_model->ci_save($user_item_data, $user_item->id);
            }

            $redirect_to = get_uri("users/view/$user_id");
            if ($this->login_user->user_type == "client") {
                $redirect_to = get_uri("users/preview/$user_id");
            }

            //send notification
            log_notification("new_user_received", array("user_id" => $user_id));

            echo json_encode(array("success" => true, "redirect_to" => $redirect_to, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of users, prepared for datatable  */

    function list_data() {    

        $this->access_only_allowed_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("users", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "status_id" => $this->request->getPost("status_id"),
            "user_date" => $this->request->getPost("start_date"),
            "deadline" => $this->request->getPost("end_date"),
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("users", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $list_data = $this->users_model->get_details($options)->getResult();

        $result = array();
        $list_data = array_reverse($list_data);
        if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'production') ) {
            foreach ($list_data as $data) { 
                if(empty($data->delivery_date))
                    continue;

                $temp_delivery_date = date("Y-m-d", strtotime($data->delivery_date));
                $temp_tomorrow_date = date("Y-m-d", strtotime('+1 day'));

                if( $temp_delivery_date != $temp_tomorrow_date )
                    continue;

                $result[] = $this->_make_row($data, $custom_fields);
            }
        }else {
            foreach ($list_data as $data) {
                $result[] = $this->_make_row($data, $custom_fields);
            }
        }

        echo json_encode(array("data" => $result));
    }

    /* prepare a row of user list table */

    private function _make_row($data, $custom_fields) {
        $user_url = "";
        $user_id = $data->id;
        if ($this->login_user->user_type == "staff") {
            $user_url = anchor(get_uri("users/view/" . $data->id), get_user_id($data->id));
        } else {
            //for client
            $user_url = anchor(get_uri("users/preview/" . $data->id), get_user_id($data->id));
        }





        $client = anchor(get_uri("clients/view/" . $data->client_id), $data->company_name);
        $total_quantity = get_user_making_data($data->id);

        $temp_total_qty = !empty($total_quantity['user_total_summary']->total_quantity)? $total_quantity['user_total_summary']->total_quantity: 0;

            // $checkmark = js_anchor("<span class='checkbox-blank mr15 float-start' id 'mycheck'></span>", );

        if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'yearly') )
            $checkmark = js_anchor("<input type='checkbox' name='yearly_checkbox_user_ids' id 'checkbox_$user_id' value='$user_id' />", );
        else if( isset($_REQUEST['datarange']) && !empty($_REQUEST['datarange']) && ($_REQUEST['datarange'] == 'production') )
            $checkmark = js_anchor("<input type='checkbox' name='production_checkbox_user_ids' id 'checkbox_$user_id' value='$user_id' />", );
        else
            $checkmark = js_anchor("<input type='checkbox' name='monthly_checkbox_user_ids' id 'checkbox_$user_id' value='$user_id' />", );


            

        $temp_delivery_date = !empty($data->delivery_date)? format_to_date($data->delivery_date, false): '-';

        
        $row_data = array(
             $checkmark,
          
             $user_url,
            $client,
            $data->user_date,
            format_to_date($data->user_date, false),
            $temp_delivery_date,
            
           $temp_total_qty,
            to_currency($data->user_value)
        );

        if ($this->login_user->user_type == "staff") {
            $row_data[] = js_anchor($data->user_status_title, array("style" => "background-color: $data->user_status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-user-status"));
        } else {
            $row_data[] = "<span style='background-color: $data->user_status_color;' class='badge'>$data->user_status_title</span>";
        }

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = modal_anchor(get_uri("users/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_user'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_user'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("users/delete"), "data-action" => "delete"));

        return $row_data;
       
    }



    //load the yearly view of user list
    function yearly() {
        return $this->template->view("users/yearly_users");
    }

    function production() {
        return $this->template->view("users/production");
    }


    /* load new user modal */

    function modal_form() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "numeric"
        ));

        $client_id = $this->request->getPost('client_id');
        $view_data['model_info'] = $this->users_model->get_one($this->request->getPost('id'));

        //make the drodown lists
        $view_data['taxes_dropdown'] = array("" => "-") + $this->Taxes_model->get_dropdown_list(array("title"));
        $view_data['clients_dropdown'] = $this->_get_clients_dropdown();

        $view_data['user_statuses'] = $this->user_status_model->get_details()->getResult();

        $view_data['client_id'] = $client_id;

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("users", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        $view_data['companies_dropdown'] = $this->_get_companies_dropdown();
        if (!$view_data['model_info']->company_id) {
            $view_data['model_info']->company_id = get_default_company_id();
        }

        return $this->template->view('users/modal_form', $view_data);
    }

    private function _get_clients_dropdown() {
        $clients_dropdown = array("" => "-");
        $clients = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        foreach ($clients as $key => $value) {
            $clients_dropdown[$key] = $value;
        }
        return $clients_dropdown;
    }

    /* add, edit or clone an user */

    function save() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "user_client_id" => "required|numeric",
            "user_date" => "required",
            "status_id" => "required"
        ));

        $client_id = $this->request->getPost('user_client_id');
        $id = $this->request->getPost('id');

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "user");
        $new_files = unserialize($files_data);

        $user_data = array(
            "client_id" => $client_id,
            "user_date" => $this->request->getPost('user_date'),
            "tax_id" => $this->request->getPost('tax_id') ? $this->request->getPost('tax_id') : 0,
            "tax_id2" => $this->request->getPost('tax_id2') ? $this->request->getPost('tax_id2') : 0,
            "company_id" => $this->request->getPost('company_id') ? $this->request->getPost('company_id') : get_default_company_id(),
            "note" => $this->request->getPost('user_note'),
            "status_id" => $this->request->getPost('status_id')
        );

        //check if the status has been changed,
        //if so, send notification
        $user_info = $this->users_model->get_one($id);
        if ($user_info->status_id !== $this->request->getPost('status_id')) {
            log_notification("user_status_updated", array("user_id" => $id));
        }

        //is editing? update the files if required
        if ($id) {
            $timeline_file_path = get_setting("timeline_file_path");
            $new_files = update_saved_files($timeline_file_path, $user_info->files, $new_files);
        }

        $user_data["files"] = serialize($new_files);

        $user_id = $this->users_model->ci_save($user_data, $id);
        if ($user_id) {
            save_custom_fields("users", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

            echo json_encode(array("success" => true, "data" => $this->_row_data($user_id), 'id' => $user_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an user */

    function delete() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* load user details view */

    function view($user_id = 0) {
        $this->access_only_allowed_members();

        if ($user_id) {
            validate_numeric_value($user_id);

            $view_data = get_user_making_data($user_id);

            if ($view_data) {
                $access_info = $this->get_access_info("invoice");
                $view_data["show_invoice_option"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;

                $access_info = $this->get_access_info("estimate");
                $view_data["show_estimate_option"] = (get_setting("module_estimate") && $access_info->access_type == "all") ? true : false;

                $view_data["can_create_projects"] = $this->can_create_projects();

                $view_data["user_id"] = $user_id;

                $view_data['user_statuses'] = $this->user_status_model->get_details()->getResult();

                return $this->template->rander("users/view", $view_data);
            } else {
                show_404();
            }
        }
    }

    private function check_access_to_this_user($user_data) {
        //check for valid user
        if (!$user_data) {
            show_404();
        }

        //check for security
        $user_info = get_array_value($user_data, "user_info");
        if ($this->login_user->user_type == "client") {
            if ($this->login_user->client_id != $user_info->client_id) {
                app_redirect("forbidden");
            }
        }
    }

    function download_pdf($user_id = 0, $mode = "download") {
        if ($user_id) {
            validate_numeric_value($user_id);
            $user_data = get_user_making_data($user_id);
            $this->check_access_to_store();
            $this->check_access_to_this_user($user_data);

            if (@ob_get_length())
                @ob_clean();
            //so, we have a valid user data. Prepare the view.

            prepare_user_pdf($user_data, $mode);
        } else {
            show_404();
        }
    }

    //view html is accessable to client only.
    function preview($user_id = 0, $show_close_preview = false) {
        $this->check_access_to_store();

        if ($user_id) {
            validate_numeric_value($user_id);
            $user_data = get_user_making_data($user_id);
            $this->check_access_to_this_user($user_data);

            $user_data['user_info'] = get_array_value($user_data, "user_info");

            $view_data['user_preview'] = prepare_user_pdf($user_data, "html");

            //show a back button
            $view_data['show_close_preview'] = $show_close_preview && $this->login_user->user_type === "staff" ? true : false;

            $view_data['user_id'] = $user_id;

            return $this->template->rander("users/user_preview", $view_data);
        } else {
            show_404();
        }
    }

    /* prepare suggestion of user item */

    function get_user_item_suggestion() {
        $key = $_REQUEST["q"];
        $suggestion = array();

        $items = $this->Invoice_items_model->get_item_suggestion($key, $this->login_user->user_type);

        foreach ($items as $item) {
            $suggestion[] = array("id" => $item->title, "text" => $item->title);
        }

        if ($this->login_user->user_type === "staff") {
            $suggestion[] = array("id" => "+", "text" => "+ " . app_lang("create_new_item"));
        }

        echo json_encode($suggestion);
    }

    function get_user_item_info_suggestion() {
        $item = $this->Invoice_items_model->get_item_info_suggestion($this->request->getPost("item_name"), $this->login_user->user_type);
        if ($item) {
            $item->rate = $item->rate ? to_decimal_format($item->rate) : "";
            echo json_encode(array("success" => true, "item_info" => $item));
        } else {
            echo json_encode(array("success" => false));
        }
    }

    function save_user_status($id = 0) {
        validate_numeric_value($id);
        $this->access_only_allowed_members();
        if (!$id) {
            show_404();
        }

        $data = array(
            "status_id" => $this->request->getPost('value')
        );

        $save_id = $this->users_model->ci_save($data, $id);

        if ($save_id) {
            log_notification("user_status_updated", array("user_id" => $id));
            $user_info = $this->users_model->get_details(array("id" => $id))->getRow();
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => app_lang('record_saved'), "user_status_color" => $user_info->user_status_color));
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /* return a row of user list table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("users", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->users_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* load discount modal */

    function discount_modal_form() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "user_id" => "required|numeric"
        ));

        $user_id = $this->request->getPost('user_id');

        $view_data['model_info'] = $this->users_model->get_one($user_id);

        return $this->template->view('users/discount_modal_form', $view_data);
    }

    /* save discount */

    function save_discount() {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "user_id" => "required|numeric",
            "discount_type" => "required",
            "discount_amount" => "numeric",
            "discount_amount_type" => "required"
        ));

        $user_id = $this->request->getPost('user_id');

        $data = array(
            "discount_type" => $this->request->getPost('discount_type'),
            "discount_amount" => $this->request->getPost('discount_amount'),
            "discount_amount_type" => $this->request->getPost('discount_amount_type')
        );

        $data = clean_data($data);

        $save_data = $this->users_model->ci_save($data, $user_id);
        if ($save_data) {
            echo json_encode(array("success" => true, "user_total_view" => $this->_get_user_total_view($user_id), 'message' => app_lang('record_saved'), "user_id" => $user_id));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of user items, prepared for datatable  */

    function item_list_data($user_id = 0) {
        validate_numeric_value($user_id);
        $this->access_only_allowed_members();

        $list_data = $this->user_items_model->get_details(array("user_id" => $user_id))->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* list of user of a specific client, prepared for datatable  */

    function user_list_data_of_client($client_id) {
        validate_numeric_value($client_id);
        $this->check_access_to_store();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("users", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("client_id" => $client_id, "custom_fields" => $custom_fields, "custom_field_filter" => $this->prepare_custom_field_filter_values("users", $this->login_user->is_admin, $this->login_user->user_type));

        $list_data = $this->users_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for users */

    function validate_users_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    function file_preview($id = "", $key = "") {
        if ($id) {
            validate_numeric_value($id);
            $user_info = $this->users_model->get_one($id);
            $files = unserialize($user_info->files);
            $file = get_array_value($files, $key);

            $file_name = get_array_value($file, "file_name");
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");

            $view_data["file_url"] = get_source_url_of_file($file, get_setting("timeline_file_path"));
            $view_data["is_image_file"] = is_image_file($file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_name);
            $view_data["is_google_drive_file"] = ($file_id && $service_type == "google") ? true : false;

            return $this->template->view("users/file_preview", $view_data);
        } else {
            show_404();
        }
    }

}

/* End of file users.php */
/* Location: ./app/controllers/users.php */