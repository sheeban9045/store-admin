<?php

namespace App\Controllers;

class Forum_categories extends Security_Controller {

    protected $Forum_categories_model;

    function __construct() {
        parent::__construct();
        $this->Forum_categories_model = model('App\Models\Forum_categories_model');
    }

    //load forum categories list view. other users can see the categories on the forum home
    function index() {
        if (!$this->login_user->is_admin) {
            app_redirect("forum");
        }

        return $this->template->rander("forum/categories/index");
    }

    //load forum category add/edit modal form
    function modal_form() {
        $this->access_only_admin();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Forum_categories_model->get_one($this->request->getPost('id'));
        return $this->template->view('forum/categories/modal_form', $view_data);
    }

    //save forum category
    function save() {
        $this->access_only_admin();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required|max_length[255]",
            "sort" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "title" => trim((string) $this->request->getPost('title')),
            "description" => trim((string) $this->request->getPost('description')),
            "sort" => (int) $this->request->getPost('sort')
        );

        if ($id) {
            $category_info = $this->Forum_categories_model->get_one($id);
            if (!$category_info->id || $category_info->deleted) {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
                return false;
            }
        } else {
            $data["created_by"] = $this->login_user->id;
            $data["created_at"] = get_current_utc_time();
        }

        $save_id = $this->Forum_categories_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //delete/undo a forum category. a category with topics can't be deleted
    function delete() {
        $this->access_only_admin();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        if ($this->request->getPost('undo')) {
            if ($this->Forum_categories_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }
        } else {
            if ($this->Forum_categories_model->count_topics($id)) {
                echo json_encode(array("success" => false, 'message' => app_lang('forum_category_has_topics')));
                return false;
            }

            if ($this->Forum_categories_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    //get data for forum categories list
    function list_data() {
        $this->access_only_admin();

        $list_data = $this->Forum_categories_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //get a forum category list row
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Forum_categories_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    //prepare a forum category list row
    private function _make_row($data) {
        return array(
            anchor(get_uri("forum/category/" . $data->id), esc($data->title)),
            $data->description ? nl2br(esc($data->description)) : "-",
            $data->total_topics,
            $data->sort,
            modal_anchor(get_uri("forum_categories/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_category'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_category'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("forum_categories/delete"), "data-action" => "delete"))
        );
    }

}

/* End of file Forum_categories.php */
/* Location: ./app/Controllers/Forum_categories.php */
