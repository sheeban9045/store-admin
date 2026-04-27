<?php

namespace App\Controllers;

class Webhut_plugins extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->access_only_team_members();
        return $this->template->rander("webhut_plugins/index");
    }

    function list_data() {
        $this->access_only_team_members();

        $list_data = $this->Webhut_plugins_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_row($data) {
        // Icon display
        $icon = "";
        if ($data->icon) {
            $icon = "<img src='" . base_url("uploads/plugins/icons/" . $data->icon) . "' style='width:40px;height:40px;object-fit:cover;border-radius:6px;' />";
        }

        // Status badge
        $status = $data->status == "active"
            ? "<span class='badge bg-success'>" . app_lang('active') . "</span>"
            : "<span class='badge bg-danger'>" . app_lang('inactive') . "</span>";

        // Discount
        $discount = $data->discount_value
            ? ($data->discount_type == "percentage" ? $data->discount_value . "%" : "$" . $data->discount_value)
            : "-";

        return array(
            $icon,
            $data->name,
            $data->code,
            $data->version,
            $data->rate,
            $discount,
            $status,
            modal_anchor(get_uri("webhut_plugins/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array(
                "class" => "edit",
                "title" => app_lang('edit'),
                "data-post-id" => $data->id
            ))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array(
                'title' => app_lang('delete'),
                "class" => "delete",
                "data-id" => $data->id,
                "data-action-url" => get_uri("webhut_plugins/delete"),
                "data-action" => "delete"
            ))
        );
    }

    function modal_form() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Webhut_plugins_model->get_one($this->request->getPost('id'));
        return $this->template->view('webhut_plugins/modal_form', $view_data);
    }

    function save() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id"             => "numeric",
            "name"           => "required",
            "code"           => "required",
            "version"        => "required",
            "rate"           => "required|numeric",
            "discount_value" => "numeric",
        ));

        $id   = $this->request->getPost('id');
        $code = $this->request->getPost('code');
        $version = $this->request->getPost('version');
        $clean_code = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($code));
    
        // --- ZIP file upload ---
        $zip_file = $this->request->getPost('hidden_zip_file');
        $zip = $this->request->getFile('zip_file');

        if ($zip && $zip->isValid()) {
            
            // OLD ZIP DELETE
            if (!empty($zip_file)) {
                $old_zip_path = FCPATH . "uploads/plugins/zips/" . $zip_file;

                if (file_exists($old_zip_path)) {
                    unlink($old_zip_path);
                }
            }

            $clean_version = str_replace('.', '_', $version);
            $zip_name = $clean_code . "_" . $clean_version . ".zip";
            
            $zip->move(FCPATH . "uploads/plugins/zips/", $zip_name, true);
            $zip_file = $zip_name;
        }

        // --- Icon upload ---
        $icon = $this->request->getPost('hidden_icon');
        $icon_file = $this->request->getFile('icon');

        if ($icon_file && $icon_file->isValid()) {
            // OLD ICON  DELETE
            if (!empty($icon)) {
                $old_icon_path = FCPATH . "uploads/plugins/icons/" . $icon;

                if (file_exists($old_icon_path)) {
                    unlink($old_icon_path);
                }
            }
            
            $clean_version = str_replace('.', '_', $version);
            $icon_name = $clean_code . "_" . $clean_version . ".png";

            $icon_file->move(FCPATH . "uploads/plugins/icons/", $icon_name, true);
            $icon = $icon_name;
        }

        // --- Photos upload (multiple) ---
        $photos = json_decode($this->request->getPost('existing_photos') ?? '[]', true);
        $photo_files = $this->request->getFiles();

        if (!empty($photo_files['photos'])) {
            foreach ($photo_files['photos'] as $photo) {
                if ($photo->isValid()) {
                    $photo_name = time() . "_" . $photo->getName();
                    $photo->move(FCPATH . "uploads/plugins/photos/", $photo_name);
                    $photos[] = $photo_name;
                }
            }
        }

        // --- Final Data ---
        $plugin_data = array(
            "name"           => $this->request->getPost('name'),
            "description"    => $this->request->getPost('description'),
            "code"           => $clean_code,
            "discount_type"  => $this->request->getPost('discount_type'),
            "discount_value" => $this->request->getPost('discount_value') ?? 0,
            "status"         => $this->request->getPost('status') ?? "inactive",
            "rate"           => $this->request->getPost('rate'),
            "version"        => $version,
            "zip_file"       => $zip_file,
            "icon"           => $icon,
            "photos"         => json_encode($photos),
        );

        $plugin_id = $this->Webhut_plugins_model->ci_save($plugin_data, $id);

        if ($plugin_id) {
            $options = array("id" => $plugin_id);
            $plugin_info = $this->Webhut_plugins_model->get_details($options)->getRow();

            echo json_encode(array(
                "success" => true,
                "id"      => $plugin_info->id,
                "data"    => $this->_make_row($plugin_info),
                "message" => app_lang('record_saved')
            ));
        } else {
            echo json_encode(array(
                "success" => false,
                "message" => app_lang('error_occurred')
            ));
        }
    }

    function delete() {
        $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->request->getPost('undo')) {
            if ($this->Webhut_plugins_model->delete($id, true)) {
                $options = array("id" => $id);
                $plugin_info = $this->Webhut_plugins_model->get_details($options)->getRow();
                echo json_encode(array(
                    "success" => true,
                    "id"      => $plugin_info->id,
                    "data"    => $this->_make_row($plugin_info),
                    "message" => app_lang('record_undone')
                ));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang('error_occurred')));
            }
        } else {
            if ($this->Webhut_plugins_model->delete($id)) {
                $plugin_info = $this->Webhut_plugins_model->get_one($id);
                echo json_encode(array(
                    "success"  => true,
                    "id"       => $plugin_info->id,
                    "message"  => app_lang('record_deleted')
                ));
            } else {
                echo json_encode(array("success" => false, "message" => app_lang('record_cannot_be_deleted')));
            }
        }
    }
}