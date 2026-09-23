<div class="modal-body clearfix">
    <div class="container-fluid">
        <div class="d-flex text-break">
            <div class="flex-shrink-0 mr10">
                <span class="avatar avatar-sm">
                    <img src="<?php echo get_avatar($reply_info->created_by_avatar); ?>" alt="..." />
                </span>
            </div>
            <div class="w-100">
                <div>
                    <span class="dark strong"><?php echo $reply_info->created_by_user ? esc($reply_info->created_by_user) : "-"; ?></span>
                    <small><span class="text-off"><?php echo format_to_relative_time($reply_info->created_at); ?></span></small>
                </div>
                <div class="text-off mt5">
                    <?php echo app_lang("topic") . ": " . anchor(get_uri("forum_topics/view/" . $reply_info->topic_id), esc($reply_info->topic_title)); ?>
                    <span class="ml10"><?php echo app_lang("category") . ": " . esc($reply_info->category_title); ?></span>
                </div>
                <p class="mt10"><?php echo nl2br(esc($reply_info->description)); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?php echo anchor(get_uri("forum_topics/view/" . $reply_info->topic_id), "<i data-feather='external-link' class='icon-16'></i> " . app_lang('view_topic'), array("class" => "btn btn-default")); ?>
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
</div>
