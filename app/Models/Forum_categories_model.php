<?php

namespace App\Models;

class Forum_categories_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forum_categories';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $forum_categories_table = $this->db->prefixTable('forum_categories');
        $forum_topics_table = $this->db->prefixTable('forum_topics');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $id = (int) $id;
            $where .= " AND $forum_categories_table.id=$id";
        }

        $sql = "SELECT $forum_categories_table.*,
                (SELECT COUNT($forum_topics_table.id) FROM $forum_topics_table WHERE $forum_topics_table.category_id=$forum_categories_table.id AND $forum_topics_table.deleted=0) AS total_topics
        FROM $forum_categories_table
        WHERE $forum_categories_table.deleted=0 $where
        ORDER BY $forum_categories_table.sort ASC, $forum_categories_table.title ASC";
        return $this->db->query($sql);
    }

    function count_topics($category_id = 0) {
        $forum_topics_table = $this->db->prefixTable('forum_topics');
        $category_id = (int) $category_id;

        $sql = "SELECT COUNT($forum_topics_table.id) AS total
        FROM $forum_topics_table
        WHERE $forum_topics_table.deleted=0 AND $forum_topics_table.category_id=$category_id";
        return (int) $this->db->query($sql)->getRow()->total;
    }

}
