<?php

namespace App\Controllers;

class Forum_topics extends Security_Controller {

    protected $Forum_categories_model;
    protected $Forum_topics_model;
    protected $Forum_replies_model;
    protected $Forum_topic_likes_model;

    function __construct() {
        parent::__construct();
        $this->Forum_categories_model = model('App\Models\Forum_categories_model');
        $this->Forum_topics_model = model('App\Models\Forum_topics_model');
        $this->Forum_replies_model = model('App\Models\Forum_replies_model');
        $this->Forum_topic_likes_model = model('App\Models\Forum_topic_likes_model');
    }

    //admin can manage everything, other users can manage only their own topics
    private function can_manage_forum_item($item) {
        return $this->login_user->is_admin || ($item->id && $item->created_by == $this->login_user->id);
    }

    //check if the topic exists and the login user can manage it
    private function check_access_to_manage_topic($topic_info) {
        if (!$topic_info->id || $topic_info->deleted) {
            show_404();
        }

        if (!$this->can_manage_forum_item($topic_info)) {
            app_redirect("forbidden");
        }
    }

    //load all topics list view. everyone can see the topics, only admin and the creator can manage a topic
    function index() {
        $view_data['categories_dropdown'] = $this->_get_categories_filter_dropdown();
        return $this->template->rander("forum/topics/index", $view_data);
    }

    //load login user's topics list view
    function my_topics() {
        return $this->template->rander("forum/topics/my_topics");
    }

    //show a topic with its replies
    function view($topic_id = 0) {
        validate_numeric_value($topic_id);

        $topic_info = $topic_id ? $this->Forum_topics_model->get_details(array("id" => $topic_id))->getRow() : null;
        if (!$topic_info) {
            show_404();
        }

        $topic_info->description_html = $this->_get_description_html($topic_info->description);
        $view_data['topic_info'] = $topic_info;
        $view_data['can_manage_topic'] = $this->can_manage_forum_item($topic_info);
        $view_data['like_info'] = $this->Forum_topic_likes_model->get_like_info($topic_id, $this->login_user->id);

        $replies = $this->Forum_replies_model->get_details(array("topic_id" => $topic_id))->getResult();
        foreach ($replies as $reply) {
            $reply->can_manage = $this->can_manage_forum_item($reply);
        }
        $view_data['replies'] = $replies;

        return $this->template->rander("forum/topics/view", $view_data);
    }

    //load topic add/edit modal form
    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "category_id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $model_info = $this->Forum_topics_model->get_one($id);

        if ($id) {
            $this->check_access_to_manage_topic($model_info);
        } else {
            $model_info->category_id = $this->request->getPost('category_id');
        }

        $view_data['model_info'] = $model_info;
        $view_data['can_change_category'] = ($this->login_user->is_admin || !$id);
        $view_data['categories_dropdown'] = $this->_get_categories_dropdown();
        $view_data['list_type'] = $this->_get_list_type($this->request->getPost('list_type'));

        return $this->template->view('forum/topics/modal_form', $view_data);
    }

    //save topic
    function save() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required|max_length[255]",
            "description" => "required",
            "category_id" => "numeric"
        ));

        $id = $this->request->getPost('id');
        $description = clean_data(trim((string) $this->request->getPost('description')));

        //the rich text editor may post empty markup
        $description_text = html_entity_decode(strip_tags($description, "<img>"), ENT_QUOTES, "UTF-8");
        if (preg_replace('/[\s\x{00A0}]+/u', "", $description_text) === "") {
            echo json_encode(array("success" => false, 'message' => app_lang('field_required') . " (" . app_lang('description') . ")"));
            return false;
        }

        $data = array(
            "title" => trim((string) $this->request->getPost('title')),
            "description" => $description
        );

        if ($id) {
            $this->check_access_to_manage_topic($this->Forum_topics_model->get_one($id));
        }

        //only admin can change the category of an existing topic
        if (!$id || $this->login_user->is_admin) {
            $category_info = $this->Forum_categories_model->get_one($this->request->getPost('category_id'));
            if (!$category_info->id || $category_info->deleted) {
                echo json_encode(array("success" => false, 'message' => app_lang('field_required') . " (" . app_lang('category') . ")"));
                return false;
            }

            $data["category_id"] = $category_info->id;
        }

        if (!$id) {
            $now = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;
            $data["created_at"] = $now;
            $data["last_activity_at"] = $now;
        }

        $save_id = $this->Forum_topics_model->ci_save($data, $id);
        if ($save_id) {
            $topic_info = $this->Forum_topics_model->get_details(array("id" => $save_id))->getRow();
            $list_type = $this->_get_list_type($this->request->getPost('list_type'));
            echo json_encode(array("success" => true, "id" => $save_id, "category_id" => $topic_info->category_id, "data" => $this->_make_row($topic_info, $list_type), "topic_url" => get_uri("forum_topics/view/" . $save_id), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //delete a topic with its replies
    function delete() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $topic_info = $this->Forum_topics_model->get_one($id);
        $this->check_access_to_manage_topic($topic_info);

        if ($this->Forum_topics_model->delete_topic_and_replies($id)) {
            echo json_encode(array("success" => true, "category_url" => get_uri("forum/category/" . $topic_info->category_id), 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    //like or unlike a topic. any logged in user can like a visible topic once
    function like_topic() {
        $this->validate_submitted_data(array(
            "topic_id" => "required|numeric",
            "action" => "required|in_list[like,unlike]"
        ));

        $topic_id = $this->request->getPost('topic_id');
        $topic_info = $this->Forum_topics_model->get_details(array("id" => $topic_id))->getRow();
        if (!$topic_info) {
            show_404();
        }

        if ($this->request->getPost('action') === "like") {
            $this->Forum_topic_likes_model->like($topic_info->id, $this->login_user->id);
        } else {
            $this->Forum_topic_likes_model->unlike($topic_info->id, $this->login_user->id);
        }

        $like_info = $this->Forum_topic_likes_model->get_like_info($topic_info->id, $this->login_user->id);
        return $this->template->view("forum/topics/like_button", array("topic_info" => $topic_info, "like_info" => $like_info));
    }

    //list data of all topics
    function all_list_data() {
        $options = array("category_id" => $this->request->getPost('category_id'));
        $this->_echo_list_data($options, "all");
    }

    //list data of login user's topics
    function my_list_data() {
        $options = array("created_by" => $this->login_user->id);
        $this->_echo_list_data($options, "my");
    }

    //list data of the topics of a category
    function category_list_data($category_id = 0) {
        validate_numeric_value($category_id);
        if (!$category_id) {
            show_404();
        }

        $options = array("category_id" => $category_id);
        $this->_echo_list_data($options, "category");
    }

    //list data of searched topics
    function search_list_data() {
        $search = trim((string) $this->request->getPost('search'));
        if (!$search) {
            echo json_encode(array("data" => array()));
            return false;
        }

        $options = array("search" => $search);
        $this->_echo_list_data($options, "all");
    }

    private function _echo_list_data($options, $list_type) {
        $list_data = $this->Forum_topics_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $list_type);
        }
        echo json_encode(array("data" => $result));
    }

    //the description is saved as html from the rich text editor (or plain text when the editor is disabled)
    private function _get_description_html($description = "") {
        $editor_tags_pattern = '/<\/?(p|br|div|span|strong|b|em|i|u|s|strike|sub|sup|font|ul|ol|li|h[1-6]|blockquote|pre|code|table|thead|tbody|tr|th|td|hr|a|img)\b[^>]*>/i';
        if (!preg_match($editor_tags_pattern, $description)) {
            return nl2br(htmlspecialchars($description, ENT_QUOTES, "UTF-8", false));
        }

        $clean_data = new \App\Libraries\Clean_data();
        return $clean_data->xss_clean($description);
    }

    private function _get_list_type($list_type = "") {
        return in_array($list_type, array("all", "my", "category")) ? $list_type : "all";
    }

    //prepare a topic list row
    private function _make_row($data, $list_type = "all") {
        $title = anchor(get_uri("forum_topics/view/" . $data->id), esc($data->title));
        $author = $data->created_by_user ? esc($data->created_by_user) : "-";
        $last_activity = $data->last_activity_at ? format_to_relative_time($data->last_activity_at) : "-";

        $options = "";
        if ($this->can_manage_forum_item($data)) {
            $options = modal_anchor(get_uri("forum_topics/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_topic'), "data-post-id" => $data->id, "data-post-list_type" => $list_type))
                    . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_topic'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("forum_topics/delete"), "data-action" => "delete-confirmation"));
        }

        if ($list_type === "my") {
            return array($title, esc($data->category_title), $data->total_replies, $data->last_activity_at, $last_activity, $options);
        } else if ($list_type === "category") {
            return array($title, $author, $data->total_replies, $data->last_activity_at, $last_activity, $options);
        }

        return array($title, esc($data->category_title), $author, $data->total_replies, $data->last_activity_at, $last_activity, $options);
    }

    private function _get_categories_dropdown() {
        $categories_dropdown = array("" => "-");
        foreach ($this->Forum_categories_model->get_details()->getResult() as $category) {
            $categories_dropdown[$category->id] = esc($category->title);
        }
        return $categories_dropdown;
    }

    private function _get_categories_filter_dropdown() {
        $categories_dropdown = array(array("id" => "", "text" => "- " . app_lang("category") . " -"));
        foreach ($this->Forum_categories_model->get_details()->getResult() as $category) {
            $categories_dropdown[] = array("id" => $category->id, "text" => $category->title);
        }
        return json_encode($categories_dropdown);
    }

}

/* End of file Forum_topics.php */
/* Location: ./app/Controllers/Forum_topics.php */
