<?php

namespace App\Models;

class Forum_topics_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forum_topics';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $forum_topics_table = $this->db->prefixTable('forum_topics');
        $forum_categories_table = $this->db->prefixTable('forum_categories');
        $forum_replies_table = $this->db->prefixTable('forum_replies');
        $users_table = $this->db->prefixTable('users');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $id = (int) $id;
            $where .= " AND $forum_topics_table.id=$id";
        }

        $category_id = get_array_value($options, "category_id");
        if ($category_id) {
            $category_id = (int) $category_id;
            $where .= " AND $forum_topics_table.category_id=$category_id";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $created_by = (int) $created_by;
            $where .= " AND $forum_topics_table.created_by=$created_by";
        }

        $search = get_array_value($options, "search");
        if ($search) {
            $search = $this->db->escapeLikeString($search);
            $where .= " AND ($forum_topics_table.title LIKE '%$search%' ESCAPE '!' OR $forum_topics_table.description LIKE '%$search%' ESCAPE '!')";
        }

        $sql = "SELECT $forum_topics_table.*, $forum_categories_table.title AS category_title,
                CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user, $users_table.image AS created_by_avatar,
                (SELECT COUNT($forum_replies_table.id) FROM $forum_replies_table WHERE $forum_replies_table.topic_id=$forum_topics_table.id AND $forum_replies_table.deleted=0) AS total_replies
        FROM $forum_topics_table
        LEFT JOIN $forum_categories_table ON $forum_categories_table.id=$forum_topics_table.category_id
        LEFT JOIN $users_table ON $users_table.id=$forum_topics_table.created_by
        WHERE $forum_topics_table.deleted=0 AND $forum_categories_table.deleted=0 $where
        ORDER BY $forum_topics_table.last_activity_at DESC, $forum_topics_table.id DESC";
        return $this->db->query($sql);
    }

    function get_suggestions($search = "") {
        $forum_topics_table = $this->db->prefixTable('forum_topics');
        $forum_categories_table = $this->db->prefixTable('forum_categories');

        $result_array = array();
        if (!$search) {
            return $result_array;
        }

        $search = $this->db->escapeLikeString($search);

        $sql = "SELECT $forum_topics_table.id, $forum_topics_table.title
        FROM $forum_topics_table
        LEFT JOIN $forum_categories_table ON $forum_categories_table.id=$forum_topics_table.category_id
        WHERE $forum_topics_table.deleted=0 AND $forum_categories_table.deleted=0 AND $forum_topics_table.title LIKE '%$search%' ESCAPE '!'
        ORDER BY $forum_topics_table.title ASC
        LIMIT 0, 10";

        foreach ($this->db->query($sql)->getResult() as $value) {
            //the label is rendered as html by the autocomplete
            $result_array[] = array("value" => $value->id, "label" => esc($value->title));
        }

        return $result_array;
    }

    function update_last_activity($topic_id = 0) {
        $forum_topics_table = $this->db->prefixTable('forum_topics');
        $topic_id = (int) $topic_id;
        $now = get_current_utc_time();

        $sql = "UPDATE $forum_topics_table SET $forum_topics_table.last_activity_at='$now' WHERE $forum_topics_table.id=$topic_id";
        return $this->db->query($sql);
    }

    //delete the topic and its replies
    function delete_topic_and_replies($topic_id = 0) {
        $forum_topics_table = $this->db->prefixTable('forum_topics');
        $forum_replies_table = $this->db->prefixTable('forum_replies');
        $topic_id = (int) $topic_id;

        if (!$topic_id) {
            return false;
        }

        $delete_topic_sql = "UPDATE $forum_topics_table SET $forum_topics_table.deleted=1 WHERE $forum_topics_table.id=$topic_id";
        if (!$this->db->query($delete_topic_sql)) {
            return false;
        }

        $delete_replies_sql = "UPDATE $forum_replies_table SET $forum_replies_table.deleted=1 WHERE $forum_replies_table.topic_id=$topic_id";
        $this->db->query($delete_replies_sql);

        return true;
    }

}
