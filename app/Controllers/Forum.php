<?php

namespace App\Controllers;

class Forum extends Security_Controller {

    protected $Forum_categories_model;
    protected $Forum_topics_model;

    function __construct() {
        parent::__construct();
        $this->Forum_categories_model = model('App\Models\Forum_categories_model');
        $this->Forum_topics_model = model('App\Models\Forum_topics_model');
    }

    //show forum homepage
    function index() {
        $view_data["categories"] = $this->Forum_categories_model->get_details()->getResult();
        return $this->template->rander("forum/index", $view_data);
    }

    //show the topics of a category
    function category($category_id = 0) {
        validate_numeric_value($category_id);

        $category_info = $category_id ? $this->Forum_categories_model->get_details(array("id" => $category_id))->getRow() : null;
        if (!$category_info) {
            show_404();
        }

        $view_data["category_info"] = $category_info;
        return $this->template->rander("forum/category", $view_data);
    }

    //show the search result of topics
    function search() {
        $view_data["search"] = trim((string) $this->request->getGet("search"));
        return $this->template->rander("forum/search", $view_data);
    }

    //get search suggestion for autocomplete
    function get_topic_suggestion() {
        $search = trim((string) $this->request->getPost("search"));
        echo json_encode($this->Forum_topics_model->get_suggestions($search));
    }

}

/* End of file Forum.php */
/* Location: ./app/Controllers/Forum.php */
