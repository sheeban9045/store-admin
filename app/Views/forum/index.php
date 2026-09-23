<div id="page-content" class="page-wrapper clearfix help-page-container">
    <div id="search-box-wrapper">
        <div class="help-search-box-container">
            <h2><?php echo app_lang("forum"); ?></h2>
            <?php echo view("forum/search_box"); ?>
        </div>
    </div>

    <div class="help-page view-container-large" style="min-height: 300px;">
        <div class="clearfix mb15">
            <div class="float-end">
                <?php
                if ($login_user->is_admin) {
                    echo anchor(get_uri("forum_categories"), "<i data-feather='list' class='icon-16'></i> " . app_lang('forum_categories'), array("class" => "btn btn-default"));
                } else {
                    echo anchor(get_uri("forum_topics/my_topics"), "<i data-feather='file-text' class='icon-16'></i> " . app_lang('my_topics'), array("class" => "btn btn-default"));
                    echo anchor(get_uri("forum_replies/my_replies"), "<i data-feather='message-circle' class='icon-16'></i> " . app_lang('my_replies'), array("class" => "btn btn-default ml10"));
                }

                if ($categories) {
                    echo modal_anchor(get_uri("forum_topics/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_topic'), array("class" => "btn btn-default ml10", "title" => app_lang('add_topic')));
                }
                ?>
            </div>
        </div>

        <?php if (!$categories) { ?>
            <div class="card">
                <div class="page-body p15 text-center text-off"><?php echo app_lang("no_record_found"); ?></div>
            </div>
        <?php } ?>

        <?php
        $count = 0;

        foreach ($categories as $category) {
            if ($count % 3 === 0) {
                echo "<div class='row'>";
            }
            $count++;
            ?>
            <div class="col-md-4 col-sm-12">
                <a href="<?php echo get_uri("forum/category/" . $category->id); ?>">
                    <div class="card">
                        <div class="page-body p15 help-category-box">
                            <h4><?php echo esc($category->title); ?></h4>
                            <p class="text-off"><?php echo nl2br(esc($category->description)); ?></p>
                            <span class="anchor"><?php echo $category->total_topics . " " . app_lang("topics"); ?></span>
                        </div>
                    </div>
                </a>
            </div>
            <?php
            if (($count % 3 === 0) || ($count === count($categories))) {
                echo "</div>";
            }
        }
        ?>
    </div>
</div>
