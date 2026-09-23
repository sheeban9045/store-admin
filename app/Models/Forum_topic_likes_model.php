<?php

namespace App\Models;

class Forum_topic_likes_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'forum_topic_likes';
        parent::__construct($this->table);
    }

    //get the total likes of a topic and the like status of the user
    function get_like_info($topic_id = 0, $user_id = 0) {
        $forum_topic_likes_table = $this->db->prefixTable('forum_topic_likes');
        $topic_id = (int) $topic_id;
        $user_id = (int) $user_id;

        $sql = "SELECT COUNT($forum_topic_likes_table.id) AS total_likes,
                SUM(IF($forum_topic_likes_table.created_by=$user_id, 1, 0)) AS like_status
        FROM $forum_topic_likes_table
        WHERE $forum_topic_likes_table.deleted=0 AND $forum_topic_likes_table.topic_id=$topic_id";
        $info = $this->db->query($sql)->getRow();

        $info->total_likes = (int) $info->total_likes;
        $info->like_status = $info->like_status ? 1 : 0;
        return $info;
    }

    //the unique key (topic_id, created_by) prevents duplicate likes, a previously removed like will be restored
    function like($topic_id = 0, $user_id = 0) {
        $forum_topic_likes_table = $this->db->prefixTable('forum_topic_likes');
        $topic_id = (int) $topic_id;
        $user_id = (int) $user_id;
        $now = get_current_utc_time();

        $sql = "INSERT INTO $forum_topic_likes_table (topic_id, created_by, created_at, deleted)
        VALUES ($topic_id, $user_id, '$now', 0)
        ON DUPLICATE KEY UPDATE created_at=IF(deleted=1, VALUES(created_at), created_at), deleted=0";
        return $this->db->query($sql);
    }

    function unlike($topic_id = 0, $user_id = 0) {
        $forum_topic_likes_table = $this->db->prefixTable('forum_topic_likes');
        $topic_id = (int) $topic_id;
        $user_id = (int) $user_id;

        $sql = "UPDATE $forum_topic_likes_table SET $forum_topic_likes_table.deleted=1
        WHERE $forum_topic_likes_table.topic_id=$topic_id AND $forum_topic_likes_table.created_by=$user_id";
        return $this->db->query($sql);
    }

}
