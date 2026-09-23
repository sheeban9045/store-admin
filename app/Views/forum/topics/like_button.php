<?php
$like_action = $like_info->like_status ? "unlike" : "like";
$like_icon_fill = $like_info->like_status ? "icon-fill-secondary" : "";

echo ajax_anchor(get_uri("forum_topics/like_topic"), "<i data-feather='thumbs-up' class='icon-16 $like_icon_fill'></i> " . app_lang($like_action), array(
    "class" => "btn btn-default btn-sm forum-topic-like-button",
    "title" => app_lang($like_action),
    "data-post-topic_id" => $topic_info->id,
    "data-post-action" => $like_action,
    "data-real-target" => "#forum-topic-like-container",
    "data-inline-loader" => "1"
));
?>
<span class="ml10 text-off forum-topic-total-likes"><i data-feather='thumbs-up' class='icon-14 text-warning icon-fill-warning'></i> <span class="forum-topic-like-count"><?php echo $like_info->total_likes; ?></span> <?php echo app_lang("likes"); ?></span>
<script type="text/javascript">
    feather.replace();
</script>
