<?php
load_js(array(
    "assets/js/awesomplete/awesomplete.min.js"
));
?>

<form action="<?php echo get_uri("forum/search"); ?>" method="get" class="input-group d-block search-box" role="search">
    <?php
    echo form_input(array(
        "id" => "forum-search-box",
        "name" => "search",
        "value" => isset($search) ? $search : "",
        "autocomplete" => "off",
        "class" => "form-control help-search-box",
        "placeholder" => app_lang('search_topics')
    ));
    ?>
    <span class="spinning-btn"></span>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        var $searchBox = $("#forum-search-box");
        var $spinningBtn = $(".spinning-btn");
        var awesomplete = new Awesomplete($searchBox[0], {
            minChars: 1,
            autoFirst: false,
            maxItems: 10
        });

        $searchBox.on("keyup", function (e) {
            if (!(e.which >= 37 && e.which <= 40) && e.which !== 13) {
                if (this.value) {
                    $spinningBtn.addClass("spinning");
                } else {
                    $spinningBtn.removeClass("spinning");
                }

                clearTimeout($.data(this, 'timer'));
                var wait = setTimeout(getAwesompleteList, 200);
                $(this).data('timer', wait);
            }
        });

        function getAwesompleteList() {
            $.ajax({
                url: "<?php echo get_uri('forum/get_topic_suggestion'); ?>",
                data: {search: $searchBox.val()},
                cache: false,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    $spinningBtn.removeClass("spinning");
                    awesomplete.list = response;
                }
            });
        }

        //suggestion selected, redirect to the topic
        $searchBox.on('awesomplete-selectcomplete', function () {
            if (this.value) {
                window.location.href = "<?php echo get_uri("forum_topics/view"); ?>/" + this.value;
            }
            this.value = "";
        });
    });
</script>
