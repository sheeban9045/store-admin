<?php

namespace App\Controllers;

class Forum_replies extends Security_Controller {

    protected $Forum_topics_model;
    protected $Forum_replies_model;

    function __construct() {
        parent::__construct();
        $this->Forum_topics_model = model('App\Models\Forum_topics_model');
        $this->Forum_replies_model = model('App\Models\Forum_replies_model');
    }

    //admin can manage everything, other users can manage only their own replies
    private function can_manage_forum_item($item) {
        return $this->login_user->is_admin || ($item->id && $item->created_by == $this->login_user->id);
    }

    //check if the reply exists and the login user can manage it
    private function check_access_to_manage_reply($reply_info) {
        if (!$reply_info || !$reply_info->id || $reply_info->deleted) {
            show_404();
        }

        if (!$this->can_manage_forum_item($reply_info)) {
            app_redirect("forbidden");
        }
    }

    //load all replies list view. everyone can see the replies, only admin and the creator can manage a reply
    function index() {
        return $this->template->rander("forum/replies/index");
    }

    //load login user's replies list view
    function my_replies() {
        return $this->template->rander("forum/replies/my_replies");
    }

    //show a reply in modal
    function view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $reply_info = $this->Forum_replies_model->get_details(array("id" => $this->request->getPost('id')))->getRow();
        if (!$reply_info) {
            show_404();
        }

        $view_data['reply_info'] = $reply_info;
        return $this->template->view('forum/replies/view', $view_data);
    }

    //load reply edit modal form
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $reply_info = $this->Forum_replies_model->get_details(array("id" => $this->request->getPost('id')))->getRow();
        $this->check_access_to_manage_reply($reply_info);

        $view_data['model_info'] = $reply_info;
        $view_data['list_type'] = $this->_get_list_type($this->request->getPost('list_type'));
        return $this->template->view('forum/replies/modal_form', $view_data);
    }

    //save a new reply or update an existing reply
    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "topic_id" => "numeric",
            "description" => "required"
        ));

        $id = $this->request->getPost('id');
        $data = array(
            "description" => trim((string) $this->request->getPost('description'))
        );

        if ($id) {
            //only the description can be updated
            $this->check_access_to_manage_reply($this->Forum_replies_model->get_details(array("id" => $id))->getRow());
        } else {
            $topic_info = $this->Forum_topics_model->get_details(array("id" => $this->request->getPost('topic_id')))->getRow();
            if (!$topic_info) {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
                return false;
            }

            $data["topic_id"] = $topic_info->id;
            $data["created_by"] = $this->login_user->id;
            $data["created_at"] = get_current_utc_time();
        }

        $save_id = $this->Forum_replies_model->ci_save($data, $id);
        if (!$save_id) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            return false;
        }

        if (!$id) {
            $this->Forum_topics_model->update_last_activity($data["topic_id"]);
        }

        $reply_info = $this->Forum_replies_model->get_details(array("id" => $save_id))->getRow();
        $list_type = $this->_get_list_type($this->request->getPost('list_type'));

        if ($list_type === "topic") {
            $reply_info->can_manage = $this->can_manage_forum_item($reply_info);
            $row_data = $this->template->view("forum/replies/reply_row", array("reply" => $reply_info));
        } else {
            $row_data = $this->_make_row($reply_info, $list_type);
        }

        echo json_encode(array("success" => true, "id" => $save_id, "data" => $row_data, 'message' => app_lang('record_saved')));
    }

    //delete a reply
    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $this->check_access_to_manage_reply($this->Forum_replies_model->get_one($id));

        if ($this->Forum_replies_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    //list data of all replies
    function all_list_data() {
        $this->_echo_list_data(array(), "all");
    }

    //list data of login user's replies
    function my_list_data() {
        $this->_echo_list_data(array("created_by" => $this->login_user->id), "my");
    }

    private function _echo_list_data($options, $list_type) {
        $list_data = $this->Forum_replies_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $list_type);
        }
        echo json_encode(array("data" => $result));
    }

    private function _get_list_type($list_type = "") {
        return in_array($list_type, array("all", "my", "topic")) ? $list_type : "all";
    }

    //prepare a reply list row
    private function _make_row($data, $list_type = "all") {
        $description = mb_strlen($data->description) > 100 ? mb_substr($data->description, 0, 100) . "..." : $data->description;
        $reply = modal_anchor(get_uri("forum_replies/view"), esc($description), array("title" => app_lang('reply'), "data-post-id" => $data->id));
        $topic = anchor(get_uri("forum_topics/view/" . $data->topic_id), esc($data->topic_title));
        $created_at = format_to_relative_time($data->created_at);

        $options = "";
        if ($this->can_manage_forum_item($data)) {
            $options = modal_anchor(get_uri("forum_replies/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_reply'), "data-post-id" => $data->id, "data-post-list_type" => $list_type))
                    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_reply'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("forum_replies/delete"), "data-action" => "delete-confirmation"));
        }

        if ($list_type === "my") {
            return array($reply, $topic, esc($data->category_title), $data->created_at, $created_at, $options);
        }

        $author = $data->created_by_user ? esc($data->created_by_user) : "-";
        return array($reply, $topic, esc($data->category_title), $author, $data->created_at, $created_at, $options);
    }

}

/* End of file Forum_replies.php */
/* Location: ./app/Controllers/Forum_replies.php */
