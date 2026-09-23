<?php

namespace App\Models;

class Forum_replies_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forum_replies';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $forum_replies_table = $this->db->prefixTable('forum_replies');
        $forum_topics_table = $this->db->prefixTable('forum_topics');
        $forum_categories_table = $this->db->prefixTable('forum_categories');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $sort = "DESC";

        $id = get_array_value($options, "id");
        if ($id) {
            $id = (int) $id;
            $where .= " AND $forum_replies_table.id=$id";
        }

        $topic_id = get_array_value($options, "topic_id");
        if ($topic_id) {
            $topic_id = (int) $topic_id;
            $where .= " AND $forum_replies_table.topic_id=$topic_id";
            $sort = "ASC"; //show the replies of a topic in ascending mode
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $created_by = (int) $created_by;
            $where .= " AND $forum_replies_table.created_by=$created_by";
        }

        $sql = "SELECT $forum_replies_table.*, $forum_topics_table.title AS topic_title, $forum_topics_table.category_id, $forum_categories_table.title AS category_title,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user, $users_table.image AS created_by_avatar
        FROM $forum_replies_table
        LEFT JOIN $forum_topics_table ON $forum_topics_table.id=$forum_replies_table.topic_id
        LEFT JOIN $forum_categories_table ON $forum_categories_table.id=$forum_topics_table.category_id
        LEFT JOIN $users_table ON $users_table.id=$forum_replies_table.created_by
        WHERE $forum_replies_table.deleted=0 AND $forum_topics_table.deleted=0 AND $forum_categories_table.deleted=0 $where
        ORDER BY $forum_replies_table.created_at $sort, $forum_replies_table.id $sort";
        return $this->db->query($sql);
    }

}
