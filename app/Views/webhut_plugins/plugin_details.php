
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        color: #333;
    }

    .page-wrapper {
        max-width: 1100px;
        margin: 30px auto;
        padding: 0 20px;
    }

    /* ===== TOP SECTION ===== */
    .top-section {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 30px;
        background: #fff;
        border: 1px solid #dde3ea;
        border-radius: 8px;
        padding: 24px;
        margin-bottom: 30px;
    }

    /* ===== IMAGE BOX (LEFT) ===== */
    .image-box {
        border: 1px solid #dde3ea;
        border-radius: 6px;
        overflow: hidden;
    }

    .image-main {
        background: #1a6fa0;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 220px;
        overflow: hidden;
    }

    .image-main img {
        /* width: 100%;
        height: 100%; */
        object-fit: cover;
    }

    .image-main-placeholder {
        width: 100%;
        height: 220px;
        background: linear-gradient(135deg, #1a6fa0, #0d4f78);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .image-main-placeholder svg {
        opacity: 0.7;
    }

    .image-label {
        background: #1a6fa0;
        padding: 10px;
        text-align: center;
    }

    .image-label span {
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }

    .image-thumbs {
        display: flex;
        gap: 8px;
        padding: 12px;
        background: #fff;
        flex-wrap: wrap;
    }

    .image-thumbs img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #dde3ea;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .image-thumbs img:hover {
        border-color: #4a90d9;
    }

    .image-thumbs-placeholder {
        width: 70px;
        height: 55px;
        border-radius: 4px;
        border: 1px solid #dde3ea;
        background: #f4f6f8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #aaa;
    }

    .image-link {
        padding: 10px;
        text-align: center;
        background: #fff;
        border-top: 1px solid #f0f2f5;
    }

    .image-link a {
        color: #4a90d9;
        font-size: 13px;
        text-decoration: none;
    }

    .image-link a:hover {
        text-decoration: underline;
    }

    /* ===== DETAIL BOX (RIGHT) ===== */
    .detail-box {
        padding: 4px 0;
    }

    .plugin-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 8px;
    }

    .meta-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .meta-pill {
        background: #f4f6f8;
        border: 1px solid #dde3ea;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        color: #666;
    }

    .meta-pill.active {
        background: #e4f4e4;
        color: #2e7d2e;
        border-color: #b5d9b5;
    }

    .plugin-short-desc {
        font-size: 14px;
        color: #555;
        line-height: 1.7;
        margin-bottom: 20px;
        border-left: 3px solid #4a90d9;
        padding-left: 12px;
    }

    .price-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .price-left {
        display: flex;
        align-items: baseline;
        gap: 10px;
        flex-wrap: wrap;
    }

    .price-final {
        font-size: 30px;
        font-weight: 700;
        color: #e05c00;
    }

    .price-original {
        font-size: 16px;
        color: #aaa;
        text-decoration: line-through;
        font-weight: 400;
    }

    .discount-badge {
        background: #fff3e0;
        color: #e05c00;
        border: 1px solid #f9c27a;
        border-radius: 12px;
        font-size: 12px;
        padding: 3px 10px;
        font-weight: 600;
    }

    .in-stock {
        color: #3a9a3a;
        font-size: 14px;
        font-weight: 600;
    }

    .cart-area {
        background: #f4f6f8;
        border-radius: 6px;
        padding: 18px;
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-cart {
        background: #3cab3c;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 13px 28px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-cart:hover {
        background: #2e8f2e;
    }

    .btn-wishlist {
        background: #fff;
        color: #555;
        border: 1px solid #dde3ea;
        border-radius: 6px;
        padding: 12px 20px;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-wishlist:hover {
        background: #f0f2f5;
    }

    /* ===== TABS SECTION ===== */
    .tabs-section {
        background: #fff;
        border: 1px solid #dde3ea;
        border-radius: 8px;
        padding: 0 24px 24px;
    }

    .tabs-nav {
        display: flex;
        border-bottom: 1px solid #dde3ea;
        margin-bottom: 24px;
        gap: 0;
    }

    .tab-btn {
        padding: 16px 24px;
        font-size: 15px;
        color: #888;
        cursor: pointer;
        border: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -1px;
        background: none;
        font-family: inherit;
        transition: color 0.2s;
    }

    .tab-btn:hover {
        color: #333;
    }

    .tab-btn.active {
        color: #4a90d9;
        border-bottom: 3px solid #4a90d9;
        font-weight: 600;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    /* ===== DESCRIPTION TAB ===== */
    .desc-intro {
        font-size: 15px;
        color: #444;
        line-height: 1.8;
        margin-bottom: 20px;
    }

    .features-heading {
        font-size: 15px;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f0f2f5;
    }

    .feature-list {
        list-style: none;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f4f6f8;
        font-size: 14px;
        color: #555;
        line-height: 1.6;
    }

    .feature-item:last-child {
        border-bottom: none;
    }

    .tick-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #e4f4e4;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    /* ===== PHOTOS TAB ===== */
    .photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 14px;
    }

    .photo-thumb-item {
        border: 1px solid #dde3ea;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        transition: box-shadow 0.2s;
    }

    .photo-thumb-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .photo-thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .photo-placeholder {
        width: 100%;
        height: 130px;
        background: #f4f6f8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #aaa;
    }

    /* ===== CHANGELOG TAB ===== */
    .changelog-item {
        padding: 16px 0;
        border-bottom: 1px solid #f0f2f5;
    }

    .changelog-item:last-child {
        border-bottom: none;
    }

    .changelog-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 6px;
    }

    .changelog-ver {
        font-weight: 700;
        color: #4a90d9;
        font-size: 15px;
    }

    .changelog-date {
        font-size: 12px;
        color: #aaa;
    }

    .changelog-desc {
        font-size: 14px;
        color: #555;
        line-height: 1.65;
    }

    /* ===== VIDEOS TAB ===== */
    .no-content-msg {
        text-align: center;
        padding: 40px 0;
        color: #aaa;
        font-size: 15px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .top-section {
            grid-template-columns: 1fr;
        }

        .image-main {
            height: 200px;
        }

        .price-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .tab-btn {
            padding: 14px 14px;
            font-size: 13px;
        }

        .photos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="page-wrapper clearfix" id="page-content">

    <!-- ========== TOP SECTION ========== -->
    <div class="top-section">

        <!-- LEFT: Image Box -->
        <div class="image-box">

            <?php
            // Main plugin icon
            $icon_path = base_url('uploads/plugins/icons/' . $plugin_data->icon);
            ?>

            <div class="image-main">
                <img src="<?php echo $icon_path; ?>"
                     alt="<?php echo $plugin_data->name; ?>"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="image-main-placeholder" style="display:none;">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                        <rect x="8" y="16" width="64" height="48" rx="4" fill="#fff" fill-opacity="0.2"/>
                        <circle cx="28" cy="35" r="6" fill="#fff" fill-opacity="0.5"/>
                        <path d="M16 52l14-12 10 10 8-8 16 16H16z" fill="#fff" fill-opacity="0.35"/>
                    </svg>
                </div>
            </div>

            <div class="image-label">
                <span><?php echo $plugin_data->name; ?></span>
            </div>

            <!-- Thumbnail photos -->
            <?php
            $photos = json_decode($plugin_data->photos, true);
            if (!empty($photos)):
            ?>
            <div class="image-thumbs">
                <?php foreach($photos as $photo): ?>
                <img src="<?php echo base_url('uploads/plugins/photos/' . $photo); ?>"
                     alt="<?php echo $plugin_data->name; ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="image-link">
                <a href="<?php echo base_url('uploads/plugins/icons/' . $plugin_data->icon); ?>" target="_blank">
                    Click on above image to view full picture
                </a>
            </div>

        </div>
        <!-- END LEFT -->

        <!-- RIGHT: Detail Box -->
        <div class="detail-box">

            <h1 class="plugin-title"><?php echo $plugin_data->name; ?></h1>

            <!-- Meta pills -->
            <div class="meta-pills">
                <span class="meta-pill">Code: <?php echo $plugin_data->code; ?></span>
                <span class="meta-pill">Version <?php echo $plugin_data->version; ?></span>
                <span class="meta-pill <?php echo $plugin_data->status === 'active' ? 'active' : ''; ?>">
                    <?php echo ucfirst($plugin_data->status); ?>
                </span>
            </div>

            <!-- Short description (first sentence only) -->
            <?php
            $desc_lines = explode("\n", trim($plugin_data->description));
            $short_desc = trim($desc_lines[0]);
            ?>
            <p class="plugin-short-desc"><?php echo $short_desc; ?></p>

            <!-- Price Row -->
            <?php
            $original_price = floatval($plugin_data->rate);
            $discount_value = floatval($plugin_data->discount_value);
            $discount_type  = $plugin_data->discount_type;

            if ($discount_type === 'percentage') {
                $final_price    = $original_price * (1 - $discount_value / 100);
                $discount_label = $discount_value . '% off';
            } else {
                $final_price    = $original_price - $discount_value;
                $discount_label = '$' . number_format($discount_value, 2) . ' off';
            }
            ?>

            <div class="price-row">
                <div class="price-left">
                    <span class="price-final">$<?php echo number_format($final_price, 2); ?></span>
                    <?php if ($discount_value > 0): ?>
                    <span class="price-original">$<?php echo number_format($original_price, 2); ?></span>
                    <span class="discount-badge"><?php echo $discount_label; ?></span>
                    <?php endif; ?>
                </div>
                <!-- <span class="in-stock">In Stock</span> -->
            </div>

            <!-- Add to Cart -->
            <div class="cart-area">
                <a href="<?php echo get_uri('Webhut_plugins/checkout/' . $plugin_data->id); ?>" class="btn-cart">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    Add To Cart
                </a>
            </div>

        </div>
        <!-- END RIGHT -->

    </div>
    <!-- END TOP SECTION -->


    <!-- ========== TABS SECTION ========== -->
    <div class="tabs-section">

        <div class="tabs-nav">
            <button class="tab-btn active" onclick="switchTab('description', this)">Description</button>
            <button class="tab-btn" onclick="switchTab('changelog', this)">Change Log</button>
            <button class="tab-btn" onclick="switchTab('photos', this)">Photos</button>
        </div>

        <!-- TAB 1: Description -->
        <div id="tab-description" class="tab-pane active">

            <?php
            // Description parse: pehli line intro hai, baaki bullets
            $desc_full  = trim($plugin_data->description);
            $all_lines  = explode("\n", $desc_full);
            $intro_text = trim($all_lines[0]);

            // Features heading aur bullets dhundho
            $features = [];
            $feat_heading = '';
            foreach ($all_lines as $i => $line) {
                $line = trim($line);
                if ($i === 0) continue;
                if (empty($line)) continue;
                // Heading line (e.g. "Frontend User Features")
                if (strpos($line, 'Features') !== false && strlen($line) < 60) {
                    $feat_heading = $line;
                    continue;
                }
                $features[] = $line;
            }
            ?>

            <p class="desc-intro"><?php echo $intro_text; ?></p>

            <?php if (!empty($feat_heading)): ?>
            <div class="features-heading"><?php echo $feat_heading; ?></div>
            <?php endif; ?>

            <?php if (!empty($features)): ?>
            <ul class="feature-list">
                <?php foreach($features as $feat): ?>
                <?php if(trim($feat) === '') continue; ?>
                <li class="feature-item">
                    <div class="tick-icon">
                        <svg width="10" height="10" viewBox="0 0 10 10">
                            <polyline points="2,5 4,7 8,3" stroke="#3a9a3a" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span><?php echo htmlspecialchars($feat); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>

        </div>

        <!-- TAB 2: Change Log -->
        <div id="tab-changelog" class="tab-pane">
            <div class="changelog-item">
                <div class="changelog-header">
                    <span class="changelog-ver">v<?php echo $plugin_data->version; ?></span>
                    <span class="changelog-date"><?php echo date('d M Y', strtotime($plugin_data->updated_at)); ?></span>
                </div>
                <p class="changelog-desc">Latest release - <?php echo $plugin_data->name; ?> v<?php echo $plugin_data->version; ?> with full feature set and improvements.</p>
            </div>
        </div>

        <!-- TAB 3: Photos -->
        <div id="tab-photos" class="tab-pane">
            <?php
            $photos = json_decode($plugin_data->photos, true);
            if (!empty($photos)):
            ?>
            <div class="photos-grid">
                <?php foreach($photos as $photo): ?>
                <div class="photo-thumb-item">
                    <img src="<?php echo base_url('uploads/plugins/photos/' . $photo); ?>"
                         alt="<?php echo $plugin_data->name; ?>"
                         onerror="this.outerHTML='<div class=\'photo-placeholder\'><?php echo $photo; ?></div>';">
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="no-content-msg">No photos available.</div>
            <?php endif; ?>
        </div>

    </div>
    <!-- END TABS SECTION -->

</div>
<!-- END PAGE WRAPPER -->

<script>
function switchTab(name, el) {
    // Sabhi tabs se active hatao
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.tab-pane').forEach(function(pane) {
        pane.classList.remove('active');
    });

    // Selected tab ko active karo
    el.classList.add('active');
    document.getElementById('tab-' + name).classList.add('active');
}
</script>
</div>