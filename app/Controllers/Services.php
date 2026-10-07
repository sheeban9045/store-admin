<?php

namespace App\Controllers;

class Services extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    private function _get_price_types() {
        return array(
            "fixed" => app_lang("fixed_price"),
            "starting_from" => app_lang("starting_from"),
            "custom_quote" => app_lang("custom_quote")
        );
    }

    function index() {
        return $this->template->rander("services/index");
    }

    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Services_model->get_one($this->request->getPost('id'));
        $view_data['price_types_dropdown'] = $this->_get_price_types();

        return $this->template->view('services/modal_form', $view_data);
    }

    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required|max_length[255]",
            "price_type" => "required|in_list[fixed,starting_from,custom_quote]",
            "status" => "required|in_list[active,inactive]",
            "sort_order" => "integer",
            "icon" => "max_length[255]"
        ));

        $id = $this->request->getPost('id');
        $title = trim((string) $this->request->getPost('title'));

        $slug = trim((string) $this->request->getPost('slug'));
        $slug = url_title($slug ? $slug : $title, "-", true);
        if (!$slug) {
            echo json_encode(array("success" => false, 'message' => app_lang("service_slug_invalid")));
            return false;
        }

        if ($this->Services_model->is_slug_exists($slug, $id)) {
            echo json_encode(array("success" => false, 'message' => app_lang("service_slug_cant_duplicate")));
            return false;
        }

        $price_type = $this->request->getPost('price_type');
        $price = null;
        if ($price_type !== "custom_quote") {
            $price = unformat_currency((string) $this->request->getPost('price'));
            if ($price === "" || !is_numeric($price) || $price < 0) {
                echo json_encode(array("success" => false, 'message' => app_lang("service_price_required")));
                return false;
            }
        }

        $now = get_current_utc_time();
        $data = array(
            "title" => $title,
            "slug" => $slug,
            "short_description" => (string) $this->request->getPost('short_description'),
            "description" => decode_ajax_post_data((string) $this->request->getPost('description')),
            "price" => $price,
            "price_type" => $price_type,
            "icon" => trim((string) $this->request->getPost('icon')),
            "sort_order" => (int) $this->request->getPost('sort_order'),
            "status" => $this->request->getPost('status'),
            "updated_at" => $now
        );

        if (!$id) {
            $data["created_at"] = $now;
        }

        $save_id = $this->Services_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            $model_info = $this->Services_model->get_one($id);
            if ($model_info->id && $this->Services_model->is_slug_exists($model_info->slug, $id)) {
                echo json_encode(array("success" => false, 'message' => app_lang("service_slug_cant_duplicate")));
            } else if ($this->Services_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }
        } else {
            if ($this->Services_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    function list_data() {
        $list_data = $this->Services_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $data = $this->Services_model->get_details(array("id" => $id))->getRow();
        return $this->_make_row($data);
    }

    private function _make_row($data) {
        $options = modal_anchor(get_uri("services/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_service'), "data-post-id" => $data->id));
        $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_service'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("services/delete"), "data-action" => "delete"));

        $status_class = $data->status === "active" ? "bg-success" : "bg-secondary";

        return array(
            esc($data->title),
            esc($data->slug),
            ($data->price_type === "custom_quote" || is_null($data->price)) ? "-" : to_currency($data->price),
            get_array_value($this->_get_price_types(), $data->price_type),
            $data->sort_order,
            "<span class='badge $status_class'>" . app_lang($data->status) . "</span>",
            $options
        );
    }

}

/* End of file Services.php */
/* Location: ./app/controllers/Services.php */
