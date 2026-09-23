<div id="forum-reply-<?php echo $reply->id; ?>" class="forum-reply-container d-flex b-b p15 m0 text-break bg-white">
    <div class="flex-shrink-0 mr10">
        <span class="avatar avatar-sm">
            <img src="<?php echo get_avatar($reply->created_by_avatar); ?>" alt="..." />
        </span>
    </div>
    <div class="w-100">
        <div>
            <span class="dark strong"><?php echo $reply->created_by_user ? esc($reply->created_by_user) : "-"; ?></span>
            <small><span class="text-off"><?php echo format_to_relative_time($reply->created_at); ?></span></small>

            <?php if (!empty($reply->can_manage)) { ?>
                <span class="float-end">
                    <?php
                    echo modal_anchor(get_uri("forum_replies/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_reply'), "data-post-id" => $reply->id, "data-post-list_type" => "topic"));
                    echo js_anchor("<i data-feather='x' class='icon-16'></i>", array("title" => app_lang('delete_reply'), "class" => "delete ml10", "data-id" => $reply->id, "data-action-url" => get_uri("forum_replies/delete"), "data-action" => "delete-confirmation", "data-success-callback" => "forumReplyDeleted"));
                    ?>
                </span>
            <?php } ?>
        </div>
        <p class="mt5 mb0"><?php echo nl2br(esc($reply->description)); ?></p>
    </div>
</div>
