<?php
foreach ($announcements as $announcement) {
    ?>
    <div id="<?php echo "announcement-$announcement->id"; ?>" class="alert alert-warning alert-dismissible">
        <h5 style="color:red;margin-top:0px;padding-top:0px">Announcement</h5>
        <i data-feather="volume-2" class="icon-18 mr10"></i> 
        <?php
        echo anchor(get_uri("announcements/view/" . $announcement->id), $announcement->title);
        // echo ajax_anchor(get_uri("announcements/mark_as_read/" . $announcement->id), "", array("class" => "btn-close", "data-remove-on-click" => "#announcement-$announcement->id"));
        ?>
        <div style="color: black;padding-left: 40px;font-size: 12px;" >
            <?php echo $announcement->description;?>
        </div>
    </div>
    <?php
}
?>