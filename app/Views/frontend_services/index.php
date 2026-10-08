<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1>Our Services</h1>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach($services as $service) { ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php 
                            if ($service->image && !empty($service->image)) {
                                $images = @unserialize($service->image);
                                if ($images && is_array($images) && count($images) > 0) {
                                    $image_url = get_source_url_of_file($images, get_setting("timeline_file_path"), "thumbnail");
                                    echo "<img class='card-img-top' src='{$image_url}' alt='{$service->title}'>";
                                }
                            }
                            ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $service->title; ?></h5>
                                <p class="card-text"><?php echo $service->description; ?></p>
                                
                                <p class="card-text"><strong><?php echo to_currency($service->price, "$"); ?></strong> <?php echo $service->price_type; ?></p>
                                
                                <a href="<?php echo get_uri('Frontend_services/checkout/' . $service->id); ?>" class="btn btn-primary">Buy Now</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            
            <?php if (count($services) == 0) { ?>
                <div class="alert alert-info">No active services available at the moment.</div>
            <?php } ?>
        </div>
    </div>
</div>
