<?php

namespace App\Controllers;

class Features extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->init_permission_checker("order");
    }

    protected function validate_access_to_items() {
        $access_invoice = $this->get_access_info("invoice");
        $access_estimate = $this->get_access_info("estimate");

        //don't show the items if invoice/estimate module is not enabled
        if (!(get_setting("module_invoice") == "1" || get_setting("module_estimate") == "1" )) {
            app_redirect("forbidden");
        }

        if ($this->login_user->is_admin) {
            return true;
        } else if ($access_invoice->access_type === "all" || $access_estimate->access_type === "all") {
            return true;
        } else {
            app_redirect("forbidden");
        }
    }

    function index() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        return $this->template->rander("features/index");
    }

    function list_data() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $list_data = $this->Features_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_item_row($data) {
        return array(
            $data->title,
            $data->type_title,
            $data->sort_order,
            modal_anchor(get_uri("features/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_features'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("features/delete"), "data-action" => "delete"))
        );
    }

    function modal_form() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Features_model->get_one($this->request->getPost('id'));
        $types = $this->Features_type_model->get_details()->getResult();

        $types_dropdown = array();

        foreach ($types as $type) {
            if (!$type->deleted) {
                $types_dropdown[$type->id] = $type->title;
            }
        }

        $view_data['types_dropdown'] = $types_dropdown;
        return $this->template->view('features/modal_form', $view_data);
    }

    function save() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
            "type" => "required",
            "sort_order" => "numeric",
        ));

        $id = $this->request->getPost('id');

        $feature_data = array(
            "title" => $this->request->getPost('title'),
            "type" => $this->request->getPost('type'),
            "sort_order" => $this->request->getPost('sort_order') ?? 0       
        );

        $feature_id = $this->Features_model->ci_save($feature_data, $id);
        if ($feature_id) {
            $options = array("id" => $feature_id);
            $feature_info = $this->Features_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $feature_info->id, "data" => $this->_make_item_row($feature_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Features_model->delete($id, true)) {
                $options = array("id" => $id);
                $feature_info = $this->Features_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $feature_info->id, "data" => $this->_make_item_row($feature_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Features_model->delete($id)) {
                $feature_info = $this->Features_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $feature_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function manage_category() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        return $this->template->rander("features/manage_category");
    }

    function feature_type_list_data() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $list_data = $this->Features_type_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_feature_type_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_feature_type_row($data) {
        return array(
            $data->title,
            $data->sort_order,
            modal_anchor(get_uri("features/types_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_features_types'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("features/delete_feature_type"), "data-action" => "delete"))
        );
    }

    function types_modal_form() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Features_type_model->get_one($this->request->getPost('id'));

        return $this->template->view('features/types_modal_form', $view_data);
    }

    function types_save() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
            "sort_order" => "numeric",
        ));

        $id = $this->request->getPost('id');

        $feature_type_data = array(
            "title" => $this->request->getPost('title'),
            "sort_order" => $this->request->getPost('sort_order') ?? 0
        );

        $feature_type_id = $this->Features_type_model->ci_save($feature_type_data, $id);
        if ($feature_type_id) {
            $options = array("id" => $feature_type_id);
            $feature_type_info = $this->Features_type_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "id" => $feature_type_info->id, "data" => $this->_make_feature_type_row($feature_type_info), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete_feature_type() {
        $this->access_only_team_members();
        $this->validate_access_to_items();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Features_type_model->delete($id, true)) {
                $options = array("id" => $id);
                $feature_type_info = $this->Features_type_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "id" => $feature_type_info->id, "data" => $this->_make_feature_type_row($feature_type_info), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Features_type_model->delete($id)) {
                $feature_type_info = $this->Features_type_model->get_one($id);
                echo json_encode(array("success" => true, "id" => $feature_type_info->id, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }
}