<?php

namespace App\Controllers;

class Services extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    function index() {
        return $this->template->rander("services/index");
    }

    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $model_info = $this->Services_model->get_one($this->request->getPost('id'));
        $image = $this->_get_image_info($model_info->image);

        $view_data['model_info'] = $model_info;
        $view_data['image_files'] = $image ? serialize(array($image)) : "";

        return $this->template->view('services/modal_form', $view_data);
    }

    function upload_file() {
        upload_file_to_temp();
    }

    function validate_image_file() {
        $file_name = $this->request->getPost("file_name");
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
        } else if (is_image_file($file_name)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_valid_image_files')));
        }
    }

    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required|max_length[255]",
            "status" => "required|in_list[active,inactive]",
            "sort_order" => "integer",
            "price_type" => "max_length[100]"
        ));

        $id = $this->request->getPost('id');
        $title = trim((string) $this->request->getPost('title'));

        $slug = trim((string) $this->request->getPost('slug'));
        $slug = url_title(str_replace("_", " ", $slug ? $slug : $title), "-", true);
        if (!$slug) {
            echo json_encode(array("success" => false, 'message' => app_lang("service_slug_invalid")));
            return false;
        }

        if ($this->Services_model->is_slug_exists($slug, $id)) {
            echo json_encode(array("success" => false, 'message' => app_lang("service_slug_cant_duplicate")));
            return false;
        }

        $price = null;
        $price_input = trim((string) $this->request->getPost('price'));
        if ($price_input !== "") {
            $price = unformat_currency($price_input);
            if (!is_numeric($price) || $price < 0) {
                echo json_encode(array("success" => false, 'message' => app_lang("service_price_invalid")));
                return false;
            }
        }

        if (!$this->_is_valid_uploaded_images()) {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_valid_image_files')));
            return false;
        }

        $now = get_current_utc_time();
        $data = array(
            "title" => $title,
            "slug" => $slug,
            "description" => decode_ajax_post_data((string) $this->request->getPost('description')),
            "price" => $price,
            "price_type" => trim((string) $this->request->getPost('price_type')),
            "sort_order" => (int) $this->request->getPost('sort_order'),
            "status" => $this->request->getPost('status'),
            "updated_at" => $now
        );

        if (!$id) {
            $data["created_at"] = $now;
        }

        //single image: a new upload replaces the existing image, the delete (x) link removes it
        $image_path = get_setting("timeline_file_path");
        $old_image = $id ? $this->_get_image_info($this->Services_model->get_one($id)->image) : null;
        $remove_files = array();

        $new_images = unserialize(move_files_from_temp_dir_to_permanent_dir($image_path, "service"));
        if (count($new_images)) {
            $data["image"] = serialize(array_pop($new_images));
            $remove_files = $new_images;
            if ($old_image) {
                $remove_files[] = $old_image;
            }
        } else if ($old_image && in_array(get_array_value($old_image, "file_name"), (array) $this->request->getPost("delete_file"))) {
            $data["image"] = "";
            $remove_files[] = $old_image;
        }

        $save_id = $this->Services_model->ci_save($data, $id);
        if ($save_id) {
            delete_app_files($image_path, $remove_files);
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

        $image = $this->_get_image_info($data->image);
        $image_html = "<span class='text-off'><i data-feather='image' class='icon-16'></i></span>";
        if ($image) {
            $thumbnail = get_source_url_of_file($image, get_setting("timeline_file_path"), "thumbnail");
            $image_html = "<img src='" . esc($thumbnail) . "' alt='' style='width:48px;height:48px;object-fit:cover;border-radius:4px;' />";
        }

        $status_class = $data->status === "active" ? "bg-success" : "bg-secondary";

        return array(
            $image_html,
            esc($data->title),
            esc($data->slug),
            is_null($data->price) ? "-" : to_currency($data->price, "$"),
            $data->price_type ? esc($data->price_type) : "-",
            $data->sort_order,
            "<span class='badge $status_class'>" . app_lang($data->status) . "</span>",
            $options
        );
    }

    //the image column holds a serialized file info array (same as users.image)
    private function _get_image_info($image) {
        $image = $image ? @unserialize($image) : null;
        return (is_array($image) && get_array_value($image, "file_name")) ? $image : null;
    }

    //uploaded files are moved by name from the temp folder, only allow plain image file names
    private function _is_valid_uploaded_images() {
        $file_names = array_filter((array) $this->request->getPost("file_names"));
        $manual_files = get_array_value($_FILES, "manualFiles");
        if ($manual_files && is_array(get_array_value($manual_files, "name"))) {
            $file_names = array_merge($file_names, array_filter($manual_files["name"]));
        }

        foreach ($file_names as $file_name) {
            if ($file_name !== basename($file_name) || !is_valid_file_to_upload($file_name) || !is_image_file($file_name)) {
                return false;
            }
        }

        return true;
    }

}

/* End of file Services.php */
/* Location: ./app/controllers/Services.php */
