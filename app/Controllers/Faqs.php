<?php

namespace App\Controllers;

class Faqs extends Security_Controller {

    protected $FAQs_model;

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
        $this->FAQs_model = model('App\Models\FAQs_model');
        $this->Faq_categories_model = model('App\Models\Faq_categories_model');
    }

    //load items list view
    function index() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        return $this->template->rander("faqs/index", $view_data);
    }

     //get categories dropdown
     private function _get_categories_dropdown() {
        $categories = $this->Item_categories_model->get_all_where(array("deleted" => 0), 0, 0, "title")->getResult();

        $categories_dropdown = array(array("id" => "", "text" => "- " . app_lang("category") . " -"));
        foreach ($categories as $category) {
            $categories_dropdown[] = array("id" => $category->id, "text" => $category->title);
        }

        return json_encode($categories_dropdown);
    }

   

    /* load item modal */

    function modal_form() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->FAQs_model->get_one($this->request->getPost('id'));

        $view_data['categories_dropdown'] = $this->Faq_categories_model->get_dropdown_list(array("title"));

        return $this->template->view('faqs/modal_form', $view_data);
    }

    /* add or edit an item */

    function save() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "title" => "required",
            "description" => "required",
            "category_id" => "required",
        ));

        $id = $this->request->getPost('id');

        $item_data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "category_id" => $this->request->getPost('category_id'),
        );

        $item_id = $this->FAQs_model->ci_save($item_data, $id);

        if ($item_id) {
            $options = array("id" => $item_id);
            $faq_info = $this->FAQs_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $faq_info->id, "data" => $this->_make_row($faq_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an item */

    function delete() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->request->getPost('undo')) {
            if ($this->FAQs_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->FAQs_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $item_info->id, "data" => $this->_make_row($item_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->FAQs_model->delete($id)) {
               
                $item_info = $this->FAQs_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $item_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of items, prepared for datatable  */

    function list_data() {
        $list_data = $this->FAQs_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of item list table */

    private function _make_row($data) {

        return array(
            $data->title,
            nl2br($data->description),
            $data->category_title ? $data->category_title : "-",
            modal_anchor(get_uri("faqs/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("faqs/delete"), "data-action" => "delete"))
        );
    }

  

    function view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->FAQs_model->get_details(array("id" => $this->request->getPost('id'), "login_user_id" => $this->login_user->id))->getRow();

        $view_data['model_info'] = $model_info;
        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

        return $this->template->view('items/view', $view_data);
    }


    /* store criteria */

    function grid_view($offset = 0, $limit = 20, $category_id = 0, $search = "") {
        validate_numeric_value($offset);
        validate_numeric_value($limit);
        validate_numeric_value($category_id);
        $this->check_access_to_store();

        $options = array("login_user_id" => $this->login_user->id);

        $item_search = $this->request->getPost("item_search");
        if ($item_search) {
            $search = $this->request->getPost("search");
            $category_id = $this->request->getPost("category_id") ? $this->request->getPost("category_id") : 0;
        }

        if ($search) {
            $options["search"] = $search;
        }

        if ($category_id) {
            $options["category_id"] = $category_id;
        }

        if ($this->login_user->user_type == "client") {
            $options["show_in_client_portal"] = 1; //show all items on admin side
        }

        //get all rows
        $all_items = $this->FAQs_model->get_details($options)->resultID->num_rows;

        $options["offset"] = $offset;
        $options["limit"] = $limit;

        $view_data["items"] = $this->FAQs_model->get_details($options)->getResult();
        $view_data["result_remaining"] = $all_items - $limit - $offset;
        $view_data["next_page_offset"] = $offset + $limit;

        $view_data["search"] = clean_data($search);
        $view_data["category_id"] = $category_id;

        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();

        $view_data["cart_items_count"] = count($this->Order_FAQs_model->get_all_where(array("created_by" => $this->login_user->id, "order_id" => 0, "deleted" => 0))->getResult());

        if ($offset) { //load more view
            return $this->template->view("items/items_grid_data", $view_data);
        } else if ($item_search) { //search suggestions view
            echo json_encode(array("success" => true, "data" => $this->template->view("items/items_grid_data", $view_data)));
         } else { //default view
             return $this->template->rander("items/grid_view", $view_data);
         }

         //     } else { //default view
         //     return $this->template->rander("items/pricing", $view_data);
         // }
    }

    function save_item_from_excel_file() {
        $this->access_only_team_members();

        if (!$this->validate_import_items_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }

        $file_name = $this->request->getPost('file_name');
        require_once(APPPATH . "ThirdParty/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new \SpreadsheetReader($temp_file_path . $file_name);
        $allowed_headers = $this->_get_allowed_headers();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, continue to the next loop
                continue;
            }

            $item_data_array = $this->_prepare_item_data($value, $allowed_headers);
            $item_data = get_array_value($item_data_array, "item_data");

            //couldn't prepare valid data
            if (!($item_data && count($item_data))) {
                continue;
            }

            //save item data
            $item_save_id = $this->FAQs_model->ci_save($item_data);
            if (!$item_save_id) {
                continue;
            }
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _get_allowed_headers() {
        return array(
            "title", //required
            "description",
        );
    }

    private function _store_headers_position($headers_row = array()) {
        $allowed_headers = $this->_get_allowed_headers();

        //check if all headers are correct and on the right position
        $final_headers = array();
        foreach ($headers_row as $key => $header) {
            $key_value = str_replace(' ', '_', strtolower(trim($header, " ")));
            $header_on_this_position = get_array_value($allowed_headers, $key);
            $header_array = array("key_value" => $header_on_this_position, "value" => $header);

            if ($header_on_this_position == $key_value) {
                //allowed headers
                //the required headers should be on the correct positions
                //pushed header at last of this loop
            } else {
                //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            if ($key_value) {
                array_push($final_headers, $header_array);
            }
        }

        return $final_headers;
    }
   

}

/* End of file items.php */
/* Location: ./app/controllers/items.php */