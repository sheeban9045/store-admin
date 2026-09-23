<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo esc($topic_info->title); ?></h1>
            <div class="title-button-group p10">
                <?php echo anchor(get_uri("forum/category/" . $topic_info->category_id), "<i data-feather='arrow-left' class='icon-16'></i> " . esc($topic_info->category_title), array("class" => "btn btn-default", "title" => app_lang('category'))); ?>

                <?php if ($can_manage_topic) { ?>
                    <span class="dropdown inline-block">
                        <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                            <i data-feather='settings' class='icon-16'></i> <?php echo app_lang('actions'); ?>
                        </button>
                        <ul class="dropdown-menu float-end" role="menu">
                            <li role="presentation"><?php echo modal_anchor(get_uri("forum_topics/modal_form"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('edit_topic'), array("title" => app_lang('edit_topic'), "data-post-id" => $topic_info->id, "class" => "dropdown-item")); ?></li>
                            <li role="presentation"><?php echo js_anchor("<i data-feather='x' class='icon-16'></i> " . app_lang('delete_topic'), array("title" => app_lang('delete_topic'), "class" => "dropdown-item", "data-id" => $topic_info->id, "data-action-url" => get_uri("forum_topics/delete"), "data-action" => "delete-confirmation", "data-success-callback" => "forumTopicDeleted")); ?></li>
                        </ul>
                    </span>
                <?php } ?>
            </div>
        </div>

        <div class="d-flex b-b p15 m0 text-break bg-white">
            <div class="flex-shrink-0 mr10">
                <span class="avatar avatar-sm">
                    <img src="<?php echo get_avatar($topic_info->created_by_avatar); ?>" alt="..." />
                </span>
            </div>
            <div class="w-100">
                <div>
                    <span class="dark strong"><?php echo $topic_info->created_by_user ? esc($topic_info->created_by_user) : "-"; ?></span>
                    <small><span class="text-off"><?php echo format_to_relative_time($topic_info->created_at); ?></span></small>
                </div>
                <div class="mt10"><?php echo $topic_info->description_html; ?></div>
                <div id="forum-topic-like-container" class="mt10">
                    <?php echo view("forum/topics/like_button", array("topic_info" => $topic_info, "like_info" => $like_info)); ?>
                </div>
            </div>
        </div>

        <div class="p15 b-b">
            <strong><?php echo app_lang("replies"); ?> (<span id="forum-total-replies"><?php echo count($replies); ?></span>)</strong>
        </div>

        <div id="forum-reply-list">
            <?php
            foreach ($replies as $reply) {
                echo view("forum/replies/reply_row", array("reply" => $reply));
            }
            ?>
        </div>

        <div id="forum-reply-form-container">
            <?php echo form_open(get_uri("forum_replies/save"), array("id" => "forum-reply-form", "class" => "general-form", "role" => "form")); ?>
            <div class="p15 d-flex">
                <div class="flex-shrink-0">
                    <div class="avatar avatar-md pr15">
                        <img src="<?php echo get_avatar($login_user->image); ?>" alt="..." />
                    </div>
                </div>
                <div class="w-100">
                    <div class="form-group">
                        <input type="hidden" name="topic_id" value="<?php echo $topic_info->id; ?>" />
                        <input type="hidden" name="list_type" value="topic" />
                        <?php
                        echo form_textarea(array(
                            "id" => "forum-reply-description",
                            "name" => "description",
                            "class" => "form-control",
                            "style" => "height: 120px",
                            "placeholder" => app_lang('write_a_reply'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                    <div class="clearfix">
                        <button class="btn btn-primary float-end" type="submit"><i data-feather='send' class='icon-16'></i> <?php echo app_lang("post_reply"); ?></button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    //reload the page after editing the topic
    window.forumReloadOnTopicSave = true;

    function forumTopicDeleted(result) {
        window.location.href = result.category_url;
    }

    function forumReplyDeleted(result, $element) {
        $element.closest(".forum-reply-container").fadeOut(function () {
            $(this).remove();
            $("#forum-total-replies").text($("#forum-reply-list .forum-reply-container").length);
        });
    }

    $(document).ready(function () {
        $("#forum-reply-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                $("#forum-reply-description").val("");
                $("#forum-reply-list").append(result.data);
                $("#forum-total-replies").text($("#forum-reply-list .forum-reply-container").length);
                feather.replace();
                appAlert.success(result.message, {duration: 10000});
            }
        });
    });
</script>
